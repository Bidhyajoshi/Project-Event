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
let userIdentity = '';
let userTool = '';

window.nextStep = (step) => {
    const name = document.getElementById('earthName').value;
    if(step === 2 && !name) { alert("Enter your Earth Name first!"); return; }
    document.querySelectorAll('.onboard-step').forEach(e => e.classList.remove('active'));
    document.getElementById('step' + step).classList.add('active');
};

window.selectIdentity = (emoji, name, el) => {
    userIdentity = name;
    // Clear previous selection
    el.parentElement.querySelectorAll('.onboard-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('nextToStep3').style.display = 'block';
};

window.selectTool = (emoji, name, el) => {
    userTool = name;
    // Clear previous selection
    el.parentElement.querySelectorAll('.onboard-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('finishBtn').style.display = 'block';
};

window.finishOnboard = () => {
    document.getElementById('onboardOverlay').style.display = 'none';
<<<<<<< HEAD
=======
    // Trigger celebratory toast or animation
    console.log(`Registered: ${userIdentity} with ${userTool}`);
>>>>>>> 236e0aa (changes made)
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

<<<<<<< HEAD
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
=======
// Excuse Generator
const excusesPool = [
    // Level 1: Chill
    { level: 1, text: "I was helping an old lady cross the road, but she was actually going in the wrong direction so I had to take her back." },
    { level: 1, text: "My alarm clock decided to join a local strike and didn't ring." },
    { level: 1, text: "I accidentally joined a marathon and couldn't stop until the finish line." },
    { level: 1, text: "I was stuck in a very intense rock-paper-scissors match with a toddler." },
    { level: 1, text: "I forgot how to walk for 15 minutes." },
    { level: 1, text: "My cat sat on my laptop and I didn't want to disturb his royal slumber." },
    { level: 1, text: "I was researching 'how to be on time' and lost track of time." },
    { level: 1, text: "A squirrel was looking at me judgmentally and I had to resolve the conflict." },
    { level: 1, text: "I thought it was Sunday... for the third time this week." },
    { level: 1, text: "I was busy defending my sandwich from a very aggressive pigeon." },
    
    // Level 2: Annoyed
    { level: 2, text: "My Wi-Fi developed a consciousness and refused to load educational content." },
    { level: 2, text: "I got stuck in a rotating door for an hour." },
    { level: 2, text: "My roommate locked me in the balcony 'as a prank'." },
    { level: 2, text: "I accidentally put my phone in the fridge and couldn't find it to check the time." },
    { level: 2, text: "A cloud followed me the whole way and it was very distracting." },
    { level: 2, text: "I was trying to calculate the trajectory of a falling leaf." },
    { level: 2, text: "My socks didn't match and I had to hold a crisis meeting with myself." },
    { level: 2, text: "I got distracted by a 'Buy 1 Get 1' sale on items I don't even need." },
    { level: 2, text: "I was convinced I was in a simulation and was looking for the exit." },
    { level: 2, text: "My coffee was too hot, so I had to wait for it to reach exactly 54.3 degrees." },

    // Level 3: Strict
    { level: 3, text: "My laptop started speaking in Spanish and then died a tragic death." },
    { level: 3, text: "I was questioning the meaning of the syllabus and entered a deep meditative state." },
    { level: 3, text: "My goldfish has a fever and required a very small cold compress." },
    { level: 3, text: "I was kidnapped by a group of mimes and couldn't call for help." },
    { level: 3, text: "My textbook was stolen by a magpie with academic ambitions." },
    { level: 3, text: "I developed a temporary allergy to the sound of your voice (no offense)." },
    { level: 3, text: "A localized gravity anomaly kept me pinned to my bed." },
    { level: 3, text: "I was participating in a secret underground underground society meeting." },
    { level: 3, text: "My brain hit 100% capacity and had to reboot in safe mode." },
    { level: 3, text: "I found a glitch in the matrix and spent the morning reporting it to the devs." },

    // Level 4: Final Boss
    { level: 4, text: "A monkey stole my backpack and is currently negotiating for 40 bananas." },
    { level: 4, text: "I was drafted into a localized intergalactic war that only lasted 2 hours." },
    { level: 4, text: "My house was declared an independent sovereign state and I had to apply for a visa to leave." },
    { level: 4, text: "I accidentally swapped bodies with my neighbor's husky." },
    { level: 4, text: "The ghost of a 17th-century pirate was using my laptop to write his memoirs." },
    { level: 4, text: "I was chased by a flock of angry geese who claimed I owed them money." },
    { level: 4, text: "My car was recruited for a movie stunt and I wasn't allowed to leave." },
    { level: 4, text: "I found a secret door in my closet that leads to Narnia, but there was a queue." },
    { level: 4, text: "A group of ninjas challenged me to a duel and I couldn't say no." },
    { level: 4, text: "I was busy preventing a temporal rift in the kitchen." },

    // Level 5: Dean Level
    { level: 5, text: "I have been chosen as the temporary leader of a small island nation and was busy with my inauguration." },
    { level: 5, text: "The laws of physics were suspended in my zip code for the morning." },
    { level: 5, text: "I was involved in a top-secret government operation involving a missing rubber duck." },
    { level: 5, text: "I accidentally invented a time machine and spent 'tomorrow' in the year 3026." },
    { level: 5, text: "The President called me for advice on his Minecraft server." },
    { level: 5, text: "I was temporarily abducted by aliens but they returned me because I wouldn't stop overthinking." },
    { level: 5, text: "I discovered a new color and had to name it before it disappeared." },
    { level: 5, text: "I was in a parallel universe where this class doesn't exist yet." },
    { level: 5, text: "My dog actually did the assignment, but then he submitted it to a different university." },
    { level: 5, text: "I am currently a ghost and I'm still figuring out how to touch the keyboard." }
];

let lastExcuse = "";

window.updateStrictLabel = (val) => {
    const labels = ["Chill", "Annoyed", "Strict", "Final Boss", "The Dean"];
    document.getElementById('strictVal').innerText = labels[val - 1];
>>>>>>> 236e0aa (changes made)
};

window.brewExcuse = () => {
    const sit = document.getElementById('exSituation').value;
    const level = parseInt(document.getElementById('exStrict').value);
    
    if(!sit) { alert("Please enter your situation first!"); return; }

    document.getElementById('exInputArea').style.display = 'none';
    document.getElementById('exCooking').style.display = 'block';
    document.getElementById('exResultContainer').style.display = 'none';

    setTimeout(() => {
        const filtered = excusesPool.filter(e => e.level === level);
        let excuse = filtered[Math.floor(Math.random() * filtered.length)].text;
        
        // Prevent immediate repeat
        if (excuse === lastExcuse) {
            excuse = filtered[(filtered.indexOf(excuse) + 1) % filtered.length].text;
        }
        lastExcuse = excuse;

        const fullExcuse = `Regarding the situation where "${sit}", I am truly sorry but ${excuse}`;
        
        document.getElementById('exCooking').style.display = 'none';
        document.getElementById('exResultContainer').style.display = 'block';
        document.getElementById('exResult').innerText = fullExcuse;
        
        // Show Stamp with delay
        setTimeout(() => {
            document.getElementById('exStamp').style.display = 'block';
        }, 300);
    }, 1500);
};

window.copyExcuse = () => {
    const text = document.getElementById('exResult').innerText;
    navigator.clipboard.writeText(text).then(() => {
        const btn = document.querySelector('.copy-btn');
        const oldText = btn.innerText;
        btn.innerText = "COPIED! ✅";
        setTimeout(() => btn.innerText = oldText, 2000);
    });
};

window.resetExcuse = () => {
    document.getElementById('exInputArea').style.display = 'block';
    document.getElementById('exResultContainer').style.display = 'none';
    document.getElementById('exStamp').style.display = 'none';
    document.getElementById('exSituation').value = '';
};

// Overthinker 3000
let otActive = false;
window.startOverthink = () => {
    if (otActive) return;
    const input = document.getElementById('otInput').value;
    if (!input) { alert("Type something to overthink about!"); return; }

    otActive = true;
    document.getElementById('otInputArea').style.display = 'none';
    document.getElementById('otLoading').style.display = 'block';
    document.getElementById('otResults').style.display = 'none';

    // 2-second loading drama
    setTimeout(() => {
        document.getElementById('otLoading').style.display = 'none';
        document.getElementById('otResults').style.display = 'block';
        const list = document.getElementById('otList');
        list.innerHTML = '';
        list.classList.add('heartbeat');
        
        const scenarios = generateScenarios(input);
        revealScenarios(scenarios, 0);
    }, 2000);
};

function generateScenarios(input) {
    const templates = [
        `Maybe they just didn't hear you mention "${input}".`,
        `Actually, they heard it and it sounded a bit awkward.`,
        `They're definitely discussing how weird "${input}" was in a group chat right now.`,
        `Wait, did you have something on your face while you said "${input}"?`,
        `They think you've been practicing "${input}" in the mirror for hours.`,
        `Someone recorded you saying "${input}" and it's about to go viral for the wrong reasons.`,
        `Your crush just saw the viral clip and is blocking you.`,
        `Your future employer found the clip and revoked your offer.`,
        `The entire internet is now making memes about you and "${input}".`,
        `Conclusion: You must change your name, burn your passport, and live in a cave forever.`
    ];
    return templates;
}
<<<<<<< HEAD
=======

function revealScenarios(scenarios, index) {
    if (index >= scenarios.length) {
        otActive = false;
        return;
    }

    const list = document.getElementById('otList');
    const item = document.createElement('div');
    item.className = 'ot-item';
    
    const chaosLevel = index + 1;
    const tag = document.createElement('span');
    tag.className = `chaos-tag chaos-${chaosLevel}`;
    tag.innerText = `Level ${chaosLevel}: ${getChaosLabel(chaosLevel)}`;
    
    const textSpan = document.createElement('span');
    textSpan.className = 'typewriter-text';
    
    item.appendChild(tag);
    item.appendChild(textSpan);
    list.appendChild(item);
    list.scrollTop = list.scrollHeight;

    let charIndex = 0;
    const text = scenarios[index];
    
    function type() {
        if (charIndex < text.length) {
            textSpan.innerHTML += text.charAt(charIndex);
            charIndex++;
            setTimeout(type, 30);
        } else {
            // Wait a bit before next scenario
            setTimeout(() => revealScenarios(scenarios, index + 1), 600);
        }
    }
    type();
}

function getChaosLabel(level) {
    const labels = ["Mild Panic", "Sweaty Palms", "Rapid Heartbeat", "Sudden Regret", "Social Suicide", "Existential Dread", "Pure Paranoia", "Total Meltdown", "Absolute Doom", "GAME OVER"];
    return labels[level - 1];
}

window.resetOT = () => {
    otActive = false;
    document.getElementById('otInputArea').style.display = 'block';
    document.getElementById('otLoading').style.display = 'none';
    document.getElementById('otResults').style.display = 'none';
    document.getElementById('otList').innerHTML = '';
    document.getElementById('otInput').value = '';
    document.getElementById('otList').classList.remove('heartbeat');
};

// Camera
function startCam() {
    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
        navigator.mediaDevices.getUserMedia({video:true}).then(s => {
            document.getElementById('myVid').srcObject = s;
        }).catch(e => console.log("Camera failed"));
    }
}
>>>>>>> 236e0aa (changes made)
