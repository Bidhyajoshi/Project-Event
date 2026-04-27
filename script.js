document.addEventListener('DOMContentLoaded', () => {
    // Floating Emojis
    const emojis = ['🍕', '☕', '🎮', '💀', '🤡', '💸', '🧠', '🚀', '🔥', '🤓'];
    const container = document.getElementById('floatingEmojis');
    if (container) {
        for(let i=0; i<10; i++) {
            let el = document.createElement('div');
            el.className = 'f-emoji';
            el.innerText = emojis[Math.floor(Math.random() * emojis.length)];
            el.style.left = Math.random() * 90 + 'vw';
            el.style.animationDuration = (10 + Math.random() * 15) + 's';
            el.style.animationDelay = (Math.random() * 5) + 's';
            container.appendChild(el);
        }
    }
});

// Auth Flow
let userAvatar = '';
window.nextStep = (step) => {
    const name = document.getElementById('earthName').value;
    if(step === 2 && !name) { alert("Enter a name!"); return; }
    document.querySelectorAll('.onboard-step').forEach(e => e.classList.remove('active'));
    document.getElementById('step' + step).classList.add('active');
};
window.selectAvatar = (avatar) => {
    userAvatar = avatar;
    nextStep(3);
};
window.finishOnboard = (year) => {
    document.getElementById('onboardOverlay').style.display = 'none';
};

// Portals
window.openPortal = (id) => {
    const p = document.getElementById(id);
    p.style.display = 'flex';
    document.body.classList.add('no-scroll');
    setTimeout(() => p.classList.add('active'), 50);
    if(id === 'ome-portal') startOmeCamera();
    if(id === 'ai-portal') fetchAIChatHistory();
};
window.closePortal = (id) => {
    const p = document.getElementById(id);
    p.classList.remove('active');
    document.body.classList.remove('no-scroll');
    setTimeout(() => { p.style.display = 'none'; }, 400);
    if(id === 'overthink-portal') resetOT();
    if(id === 'ome-portal') {
        if (localStream) {
            localStream.getTracks().forEach(t => t.stop());
            localStream = null;
        }
        if(peer) peer.destroy();
        clearInterval(omePollInterval);
    }
};

// ----------------------------------------------------
// OME-GRAVITY WebRTC (P2P) Logic
// ----------------------------------------------------
let localStream = null;
let peer = null;
let omeSessionId = null;
let omeRole = null;
let omePollInterval = null;

window.startOmeCamera = () => {
    const err = document.getElementById('ome-error');
    const grid = document.getElementById('omeVideoGrid');
    const nextBtn = document.getElementById('nextBtn');
    
    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
        navigator.mediaDevices.getUserMedia({ video: true, audio: true })
            .then(stream => {
                localStream = stream;
                document.getElementById('localVideo').srcObject = stream;
                err.style.display = 'none';
                grid.style.display = 'flex';
                nextBtn.style.display = 'inline-block';
                joinOmeWaitingRoom();
            })
            .catch(e => {
                console.error(e);
                err.style.display = 'block';
            });
    } else {
        err.style.display = 'block';
    }
}

function joinOmeWaitingRoom() {
    if(peer) peer.destroy();
    document.getElementById('remoteVideo').srcObject = null;
    document.getElementById('radarPulse').style.display = 'block';
    document.getElementById('omeChatBox').innerHTML = '<div class="ome-msg">System: Looking for someone...</div>';
    clearInterval(omePollInterval);

    fetch('ome-gravity.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=join'
    }).then(r => r.json()).then(data => {
        if(data.success) {
            omeSessionId = data.session_id;
            omeRole = data.role;
            initPeer();
            omePollInterval = setInterval(pollOme, 2000); // Poll DB for signals
        }
    });
}

function initPeer() {
    if(typeof SimplePeer === 'undefined') {
        setTimeout(initPeer, 500); // wait for CDN to load
        return;
    }
    peer = new SimplePeer({
        initiator: omeRole === 'caller',
        stream: localStream,
        trickle: false // Disable trickle to send one big SDP string via PHP
    });

    peer.on('signal', data => {
        // Send SDP signal to PHP
        fetch('ome-gravity.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=signal&session_id=${omeSessionId}&signal=${encodeURIComponent(JSON.stringify(data))}`
        });
    });

    peer.on('stream', stream => {
        document.getElementById('remoteVideo').srcObject = stream;
        document.getElementById('radarPulse').style.display = 'none';
        document.getElementById('omeChatBox').innerHTML += '<div class="ome-msg" style="color:#32CD32">System: Stranger connected! Say Hi.</div>';
    });

    peer.on('data', data => {
        document.getElementById('omeChatBox').innerHTML += `<div class="ome-msg"><b>Stranger:</b> ${new TextDecoder("utf-8").decode(data)}</div>`;
        scrollToBottomOme();
    });

    peer.on('close', () => {
        joinOmeWaitingRoom(); // Re-queue if stranger leaves
    });
}

let lastPeerSignalStr = "";
function pollOme() {
    fetch('ome-gravity.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=poll&session_id=${omeSessionId}`
    }).then(r => r.json()).then(data => {
        if(data.status === 'closed') {
            joinOmeWaitingRoom(); // Stranger skipped
            return;
        }
        if(data.peer_signal) {
            const sigStr = JSON.stringify(data.peer_signal);
            if(sigStr !== lastPeerSignalStr) {
                lastPeerSignalStr = sigStr;
                peer.signal(data.peer_signal); // Inject peer SDP
            }
        }
    });
}

window.nextOmePartner = () => {
    fetch('ome-gravity.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=skip&session_id=${omeSessionId}`
    }).then(() => joinOmeWaitingRoom());
};

window.handleOmeChat = (e) => {
    if(e.key === 'Enter' && e.target.value) {
        const msg = e.target.value;
        if(peer && peer.connected) {
            peer.send(msg);
            document.getElementById('omeChatBox').innerHTML += `<div class="ome-msg"><b>You:</b> ${msg}</div>`;
            scrollToBottomOme();
        } else {
             document.getElementById('omeChatBox').innerHTML += `<div class="ome-msg" style="color:red">System: Not connected.</div>`;
        }
        e.target.value = '';
    }
};

function scrollToBottomOme() {
    const box = document.getElementById('omeChatBox');
    box.scrollTop = box.scrollHeight;
}

let isVideoOn = true;
let isMuted = false;
window.toggleVideo = () => {
    if(!localStream) return;
    isVideoOn = !isVideoOn;
    localStream.getVideoTracks()[0].enabled = isVideoOn;
    document.getElementById('vidBtn').style.background = isVideoOn ? "rgba(0,0,0,0.6)" : "#ff3300";
};
window.toggleMute = () => {
    if(!localStream) return;
    isMuted = !isMuted;
    localStream.getAudioTracks()[0].enabled = !isMuted;
    document.getElementById('muteBtn').style.background = !isMuted ? "rgba(0,0,0,0.6)" : "#ff3300";
};

// ----------------------------------------------------
// OTHER GAMES
// ----------------------------------------------------

// Bunk Wheel
window.spinWheel = () => {
    const wheel = document.getElementById('bWheel');
    const res = document.getElementById('wheelResult');
    const att = parseInt(document.getElementById('bAtt').value)||0;
    
    const spins = 5 + Math.random() * 5;
    const deg = Math.random() * 360;
    wheel.style.transform = `rotate(${spins*360 + deg}deg)`;
    res.innerText = "Spinning...";
    res.style.color = "#fff";

    setTimeout(() => {
        if(att < 75) { res.innerText = "DANGER! GO TO CLASS!"; res.style.color = "#ff4d4d"; }
        else { res.innerText = "BUNK IT LIKE A BOSS!"; res.style.color = "#32CD32"; }
    }, 3000);
};

// Savage AI State
let burnLevel = 0;
window.sendAIMsg = () => {
    const box = document.getElementById('aiChatBox');
    const inputField = document.getElementById('aiInput');
    const portal = document.getElementById('ai-portal');
    
    const val = inputField.value;
    const modeNode = document.querySelector('input[name="aiMode"]:checked');
    const mode = modeNode ? modeNode.value : 'savage';

    if(!val) return;

    // Screen Shake on Send
    document.body.classList.add('violent-shake');
    setTimeout(() => document.body.classList.remove('violent-shake'), 300);

    // User Message
    box.innerHTML += `<div class="msg user">${val}</div>`;
    inputField.value = '';
    scrollToBottomAI();

    // Fetch AI Roast
    fetch('savage_ai.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `input=${encodeURIComponent(val)}&mode=${mode}`
    }).then(r => r.json()).then(data => {
        setTimeout(() => {
            // Neon Flash & Glitch
            portal.classList.add('screen-flash');
            setTimeout(() => portal.classList.remove('screen-flash'), 100);
            
            // Add AI Message with Glitch animation
            const aiMsgHtml = `<div class="msg ai glitch-shake">${data.reply}</div>`;
            box.innerHTML += aiMsgHtml;
            scrollToBottomAI();
            setTimeout(() => {
                const msgs = document.querySelectorAll('.glitch-shake');
                msgs.forEach(m => m.classList.remove('glitch-shake'));
            }, 600); // Stop glitching after 600ms

            // Speech Synthesis
            if ('speechSynthesis' in window) {
                const utterance = new SpeechSynthesisUtterance(data.reply);
                utterance.rate = 1.1;
                utterance.pitch = data.mode === 'damage' ? 0.5 : 1.2;
                window.speechSynthesis.speak(utterance);
            }

            // Burn Meter Logic
            if(data.mode === 'savage' || data.mode === 'damage') {
                burnLevel += 25;
                if(burnLevel > 100) burnLevel = 100;
                document.getElementById('burnFill').style.height = `${burnLevel}%`;
                const textEl = document.getElementById('burnText');
                textEl.innerText = `${burnLevel}% Roast`;
                textEl.setAttribute('data-text', `${burnLevel}% Roast`);
            }

            // Meme Pop-up Randomly (30% chance)
            if(Math.random() < 0.3) {
                const meme = document.getElementById('memeSticker');
                meme.innerHTML = data.mode === 'damage' ? "💀 DESTROYED" : "🔥 SAVAGE";
                meme.style.display = 'block';
                setTimeout(() => { meme.style.display = 'none'; }, 2000);
            }

            // Spawn Particles
            spawnSkulls();

        }, 600);
    }).catch(e => console.error(e));
};

function scrollToBottomAI() {
    const box = document.getElementById('aiChatBox');
    box.scrollTop = box.scrollHeight;
}

function fetchAIChatHistory() {
    fetch('savage_ai.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=fetch'
    }).then(r => r.json()).then(data => {
        const box = document.getElementById('aiChatBox');
        box.innerHTML = '';
        if(data.chats && data.chats.length > 0) {
            data.chats.forEach(c => {
                box.innerHTML += `<div class="msg user history-msg">${c.message}</div>`;
                box.innerHTML += `<div class="msg ai history-msg">${c.response}</div>`;
            });
            scrollToBottomAI();
        }
    });
}

window.clearAIChat = () => {
    fetch('savage_ai.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=clear'
    }).then(r => r.json()).then(data => {
        document.getElementById('aiChatBox').innerHTML = '';
        burnLevel = 0;
        document.getElementById('burnFill').style.height = `0%`;
        const textEl = document.getElementById('burnText');
        textEl.innerText = `0% Roast`;
        textEl.setAttribute('data-text', `0% Roast`);
    });
};

function spawnSkulls() {
    const container = document.getElementById('aiParticles');
    for(let i=0; i<4; i++) {
        let el = document.createElement('div');
        el.className = 'f-skull';
        el.innerText = Math.random() > 0.5 ? '💔' : '📨';
        el.style.left = Math.random() * 100 + '%';
        container.appendChild(el);
        setTimeout(() => el.remove(), 4000);
    }
}

// Excuse Gen
window.brewExcuse = () => {
    const cat = document.getElementById('exCat').value;
    const vic = document.getElementById('exVic').value;
    const res = document.getElementById('exResult');
    const base = cat === 'College' ? "my internet exploded" : "my pet ate my clothes";
    res.innerText = `I can't face the ${vic} because ${base}.`;
    res.style.display = 'block';
    res.style.color = '#00E5FF';
};

// Overthinker
let otTimer;
window.startOverthink = () => {
    const list = document.getElementById('otList');
    const cont = document.getElementById('otContainer');
    list.innerHTML = '';
    let count = 1;
    clearInterval(otTimer);

    function loop() {
        if(count > 10) return;
        list.innerHTML += `<div class="ot-item">${count}. Everyone noticed and they hate you.</div>`;
        document.body.style.transform = `scale(${1 + (count*0.02)})`;
        cont.style.animation = `shakeOT ${0.5/count}s infinite`;
        count++;
        otTimer = setTimeout(loop, 1200);
    }
    loop();
};
function resetOT() {
    clearInterval(otTimer);
    document.body.style.transform = 'scale(1)';
    document.getElementById('otContainer').style.animation = '';
}
