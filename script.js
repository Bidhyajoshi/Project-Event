console.log('BAWAAL Script Loaded');

window.addEventListener('load', function() {
    console.log('BAWAAL DOM Ready');
});

// OVERTHINKER 3000 - GLOBAL PANIC LOGIC
window.startPanicAttack = function() {
    const input = document.getElementById('otInput').value.toLowerCase();
    const list = document.getElementById('otList');
    const container = document.getElementById('otContainer');
    list.innerHTML = '';
    
    const scenarios = [
        "That 'K.' has more attitude than a Bollywood villain.",
        "They spent 2 minutes typing 'K.'? Something is wrong.",
        "A full stop after K is a digital slap.",
        "K means 'Kill me now, I am bored of you.'",
        "They are definitely talking about you in another group.",
        "That full stop means they hate you now.",
        "They only replied to end the conversation.",
        "They are typing a breakup message right now.",
        "They are literally rolling their eyes at your text.",
        "You've been officially friend-zoned by a single letter.",
        "They are with someone else and just sent a quick K.",
        "K is just a polite way of saying 'Shut up.'",
        "They are laughing at your long paragraph.",
        "They just screenshotted your text to roast you.",
        "Read at 10:30 PM. It's 10:31 PM. You are dead to them.",
        "They saw it and decided a wall was more interesting.",
        "They are waiting for someone 'better' to reply first.",
        "They showed your text to their friends and they all laughed.",
        "They are busy living a life that doesn't include you.",
        "The blue ticks are a sign of your impending doom.",
        "They have muted your chat forever.",
        "They are active on Instagram but ignored your text.",
        "They deleted the chat after reading it.",
        "They are thinking of ways to block you without feeling guilty.",
        "They read it and said 'Ugh, not this person again.'",
        "Busy? They are watching Netflix and ignoring you.",
        "Busy means 'Busy talking to someone else.'",
        "They have time for everyone except you.",
        "Busy is code for 'I don't want to talk, go away.'",
        "They are currently out with friends and laughing at your text.",
        "They will reply in 3 business days... maybe.",
        "They are intentionally making you wait to show dominance.",
        "They are busy... finding a replacement for you.",
        "They have time to post stories but not to reply.",
        "Ok is the silent killer of conversations.",
        "Ok means 'I am not listening, but I have to reply.'",
        "They are clearly annoyed but being 'polite.'",
        "Ok is the shortest path to a breakup.",
        "They are bored to death by your existence.",
        "Ok was sent with a heavy sigh.",
        "They are wondering why they ever replied to you.",
        "They are already talking to someone more interesting.",
        "They took 5 seconds longer to reply. PANIC!",
        "They didn't use an emoji. The end is near.",
        "They used a different font in their head while reading your text.",
        "They are definitely judging your grammar.",
        "They are showing this to their mom right now.",
        "They are planning to ghost you by Friday.",
        "They are literally cringing at your last text.",
        "They have a secret group chat about your weird habits.",
        "They are comparing you to their ex right now.",
        "They think you are 'too much' to handle.",
        "They are going to leave you on 'Delivered' for 2 days."
    ];

    // Filter logic based on situation awareness
    let filtered = [...scenarios];
    if (input.includes('k')) filtered = scenarios.filter(s => s.toLowerCase().includes('k'));
    if (input.includes('seen') || input.includes('read')) filtered = scenarios.filter(s => s.toLowerCase().includes('read') || s.toLowerCase().includes('seen'));
    if (input.includes('say') || input.includes('said')) filtered = scenarios.filter(s => s.toLowerCase().includes('attitude') || s.toLowerCase().includes('tone') || s.toLowerCase().includes('angry'));
    
    // Fallback if filter is too restrictive
    if (filtered.length < 10) filtered = [...filtered, ...scenarios.sort(() => 0.5 - Math.random()).slice(0, 10)];

    // Shuffle and pick 10
    const final10 = filtered.sort(() => 0.5 - Math.random()).slice(0, 10);
    
    container.style.animation = "heartbeat-red 0.5s infinite";
    
    final10.forEach((text, i) => {
        setTimeout(() => {
            const el = document.createElement('div');
            el.className = 'ot-item';
            el.style.cssText = "background:rgba(255,0,0,0.2); border-left:5px solid #ff0000; padding:15px; color:#ff0000; font-family:'Courier New', monospace; font-weight:bold; animation:shake 0.2s infinite; text-shadow:0 0 5px #ff0000; margin-bottom:10px; border-radius:5px;";
            el.innerText = `[PANIC] ${text}`;
            list.prepend(el);
            list.scrollTop = 0;
        }, i * 600);
    });
};

// GLOBAL PORTAL FUNCTIONS - HOISTED
window.openPortal = function(id) {
    try {
        console.log('Opening Portal: ' + id);
        var el = document.getElementById(id);
        if(!el) { console.error('Portal element not found: ' + id); return; }
        el.classList.add('active');
        document.body.classList.add('no-scroll');
        if(id === 'ome-portal' && typeof startOmeCamera === 'function') startOmeCamera();
        if(id === 'meme-portal' && typeof startMemeCamera === 'function') startMemeCamera();
        if(id === 'ai-portal' && typeof fetchAIChatHistory === 'function') fetchAIChatHistory();
    } catch (e) {
        console.error("BAWAAL ERROR opening portal: ", e);
    }
};

window.closePortal = function(id) {
    try {
        console.log('Closing Portal: ' + id);
        var el = document.getElementById(id);
        if(!el) return;
        el.classList.remove('active');
        document.body.classList.remove('no-scroll');
        if(id === 'overthink-portal' && typeof resetOT === 'function') resetOT();
        if(id === 'ome-portal') {
            if (typeof localStream !== 'undefined' && localStream) {
                localStream.getTracks().forEach(t => t.stop());
                localStream = null;
            }
            if(typeof peer !== 'undefined' && peer) peer.destroy();
            if(typeof omePollInterval !== 'undefined') clearInterval(omePollInterval);
        }
        if(id === 'meme-portal') {
            if (typeof memeStream !== 'undefined' && memeStream) {
                memeStream.getTracks().forEach(t => t.stop());
                memeStream = null;
            }
        }
    } catch (e) {
        console.error("BAWAAL ERROR closing portal: ", e);
    }
};

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
    const name = document.getElementById('earthName')?.value;
    if(step === 2 && !name && document.getElementById('earthName')) { alert("Enter your Earth Name first!"); return; }
    document.querySelectorAll('.onboard-step').forEach(e => e.classList.remove('active'));
    if(document.getElementById('step' + step)) document.getElementById('step' + step).classList.add('active');
};

window.selectIdentity = (emoji, name, el) => {
    userIdentity = name;
    el.parentElement.querySelectorAll('.onboard-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    if(document.getElementById('nextToStep3')) document.getElementById('nextToStep3').style.display = 'block';
};

window.selectTool = (emoji, name, el) => {
    userTool = name;
    el.parentElement.querySelectorAll('.onboard-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    if(document.getElementById('finishBtn')) document.getElementById('finishBtn').style.display = 'block';
};

window.finishOnboard = () => {
    console.log(`Registered: ${userIdentity} with ${userTool}`);
};

// ----------------------------------------------------
// MEME CAMERA LOGIC
// ----------------------------------------------------
let memeStream = null;
let currentMemeFilter = { css: 'none', style: '' };

const memeFilters = [
    { name: 'Normal', icon: '👤', css: 'none' },
    { name: 'Alien Mode', icon: '👽', css: 'hue-rotate(90deg) saturate(300%) contrast(150%)' },
    { name: 'Bhoot', icon: '👻', css: 'invert(100%) blur(2px) brightness(150%)' },
    { name: 'Deep Fried', icon: '🍟', css: 'contrast(300%) saturate(500%) brightness(150%)' },
    { name: 'Heat Map', icon: '🔥', css: 'invert(100%) hue-rotate(180deg) saturate(300%)' },
    { name: 'Thug Life', icon: '😎', css: 'contrast(150%) saturate(150%) grayscale(20%)' },
    { name: 'Purana Jamana', icon: '🎞️', css: 'sepia(100%) contrast(150%) brightness(90%)' },
    { name: 'Rainbow', icon: '🌈', css: 'hue-rotate(180deg) saturate(200%)' },
    { name: 'Nose Job', icon: '👃', css: 'saturate(200%) contrast(120%)' },
    { name: 'Pixel Galti', icon: '👾', css: 'contrast(200%) blur(3px)' },
    { name: 'Bijli', icon: '⚡', css: 'saturate(500%) hue-rotate(45deg) contrast(200%)' },
    { name: 'Rona Dhona', icon: '😭', css: 'brightness(80%) contrast(120%) blur(1px)' },
    { name: 'Shaitaan', icon: '👹', css: 'sepia(100%) hue-rotate(-50deg) saturate(500%)' },
    { name: 'Sketch It', icon: '✏️', css: 'grayscale(100%) contrast(500%) brightness(150%)' },
    { name: 'Ulti Duniya', icon: '🪞', css: 'none' },
    { name: 'Gol Gappa', icon: '🌀', css: 'contrast(150%) hue-rotate(20deg)' },
    { name: 'Mota Face', icon: '👀', css: 'contrast(110%)' },
    { name: 'Glitch Out', icon: '📺', css: 'contrast(200%) hue-rotate(90deg) blur(1px)' },
    { name: 'Disco Deewane', icon: '🎉', css: 'hue-rotate(270deg) saturate(300%)' },
    { name: 'Comic Book', icon: '🎨', css: 'contrast(200%) saturate(0%) brightness(150%) contrast(500%)' },
    { name: 'Nightmare', icon: '💀', css: 'grayscale(100%) contrast(200%) brightness(50%)' }
];

window.startMemeCamera = () => {
    console.log('Activating Meme Camera...');
    navigator.mediaDevices.getUserMedia({ video: true })
        .then(stream => {
            console.log('Meme Camera Active');
            memeStream = stream;
            const vid = document.getElementById('meme-video');
            vid.srcObject = stream;
            vid.style.display = 'block';
            initFilterCarousel();
        })
        .catch(err => {
            console.error('Meme Camera Failed:', err);
            alert("Camera access denied for Meme Camera!");
        });
};

function initFilterCarousel() {
    const carousel = document.getElementById('filterCarousel');
    carousel.innerHTML = '';
    memeFilters.forEach((f, idx) => {
        let item = document.createElement('div');
        item.className = 'filter-item' + (idx === 0 ? ' active' : '');
        item.style.cssText = "cursor: pointer !important; pointer-events: auto !important; flex-shrink: 0; text-align: center;";
        
        let circle = document.createElement('div');
        circle.className = 'filter-circle';
        circle.style.cssText = "width: 70px; height: 70px; border-radius: 50%; border: 3px solid #fff; display: flex; justify-content: center; align-items: center; font-size: 2rem; background: rgba(255,255,255,0.1); margin-bottom: 5px; pointer-events: none;";
        circle.innerText = f.icon;
        
        let name = document.createElement('div');
        name.className = 'filter-name';
        name.style.cssText = "color: #fff; font-size: 0.8rem; pointer-events: none;";
        name.innerText = f.name;
        
        item.appendChild(circle);
        item.appendChild(name);
        
        item.onclick = () => {
            document.querySelectorAll('.filter-item').forEach(c => c.classList.remove('active'));
            item.classList.add('active');
            applyMemeFilter(f);
        };
        carousel.appendChild(item);
    });
}

window.applyMemeFilter = (filter) => {
    currentMemeFilter = filter;
    const vid = document.getElementById('meme-video');
    const thug = document.getElementById('thugOverlay');
    
    // Apply CSS Filter Directly
    vid.style.filter = filter.css;
    
    // Toggle Thug Overlay
    if (filter.name === 'Thug Life') {
        thug.style.display = 'block';
    } else {
        thug.style.display = 'none';
    }
};

window.captureMeme = () => {
    const vid = document.getElementById('memeVideo');
    const canvas = document.getElementById('memeCanvas');
    const flash = document.getElementById('camFlash');
    const popup = document.getElementById('snapPopup');
    
    // Pause Video
    vid.pause();
    
    // Flash effect
    flash.style.display = 'block';
    setTimeout(() => { flash.style.display = 'none'; }, 100);
    
    // Setup Canvas
    canvas.width = vid.videoWidth;
    canvas.height = vid.videoHeight;
    const ctx = canvas.getContext('2d');
    
    // Apply exact CSS filter to canvas
    ctx.filter = currentMemeFilter.css;
    
    // Draw Video
    ctx.drawImage(vid, 0, 0, canvas.width, canvas.height);
    
    // Draw Thug Life if active
    if(currentMemeFilter.name === 'Thug Life') {
        const thugImg = new Image();
        thugImg.src = 'https://upload.wikimedia.org/wikipedia/commons/e/e4/Thug_Life_Glasses.svg';
        ctx.drawImage(thugImg, canvas.width/2 - 150, canvas.height/3, 300, 100);
    }
    
    // Show Popup with animation
    popup.style.display = 'flex';
    setTimeout(() => {
        popup.style.transform = 'translate(-50%, -50%) scale(1)';
    }, 10);
};

window.saveMeme = () => {
    const canvas = document.getElementById('memeCanvas');
    const link = document.getElementById('hiddenSaveLink');
    const dataURL = canvas.toDataURL('image/png');
    
    link.href = dataURL;
    link.download = `bawaal_meme_${new Date().getTime()}.png`;
    link.click();
    
    trashMeme(); // Resumes and hides popup
};

window.trashMeme = () => {
    const popup = document.getElementById('snapPopup');
    const vid = document.getElementById('memeVideo');
    
    popup.style.transform = 'translate(-50%, -50%) scale(0)';
    setTimeout(() => {
        popup.style.display = 'none';
        vid.play(); // Resume video
    }, 300);
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
    }).catch(e => console.error(e));
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
    }).catch(e => console.error(e));
}

window.nextOmePartner = () => {
    fetch('ome-gravity.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=skip&session_id=${omeSessionId}`
    }).then(() => joinOmeWaitingRoom()).catch(e => console.error(e));
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

// Bunk Decision Maker
let bunkData = { importance: '', friends: 0, attendance: 50 };

window.nextBunkStep = (currentStep, val = null) => {
    if(currentStep === 1) {
        bunkData.importance = val;
        document.getElementById('bunkStep1').style.display = 'none';
        document.getElementById('bunkStep2').style.display = 'flex';
    } else if(currentStep === 2) {
        let fr = document.getElementById('bunkFriends').value;
        if(fr === '') fr = 0;
        bunkData.friends = parseInt(fr);
        document.getElementById('bunkStep2').style.display = 'none';
        document.getElementById('bunkStep3').style.display = 'flex';
    }
};

window.calculateBunkDestiny = () => {
    bunkData.attendance = parseInt(document.getElementById('bunkAtt').value);
    
    document.getElementById('bunkStep3').style.display = 'none';
    const loading = document.getElementById('bunkLoading');
    loading.style.display = 'flex';
    loading.classList.add('violent-shake');
    
    const bar = document.getElementById('bunkBar');
    bar.style.width = '0%';
    setTimeout(() => { bar.style.width = '100%'; }, 100);

    setTimeout(() => {
        loading.classList.remove('violent-shake');
        loading.style.display = 'none';
        document.getElementById('bunkResult').style.display = 'flex';
        
        let verdict = "";
        let reason = "";
        
        if (bunkData.attendance < 75) {
            verdict = "DANGER!";
            reason = "Go to class or start selling tea.";
            document.getElementById('bunkVerdict').style.color = '#ff0040';
            document.getElementById('bunkVerdict').style.textShadow = '0 0 20px #ff0040';
        } else if (bunkData.friends > 5) {
            verdict = "BUNK IT!";
            reason = "The squad is waiting. Bunking is a moral obligation now.";
            document.getElementById('bunkVerdict').style.color = '#00ff00';
            document.getElementById('bunkVerdict').style.textShadow = '0 0 20px #00ff00';
        } else if (bunkData.importance === 'Mid' || bunkData.importance === "Who is the Prof?") {
            verdict = "SLEEP IN!";
            reason = "Sleep > This Lecture. Go back to bed, legend.";
            document.getElementById('bunkVerdict').style.color = '#00ffff';
            document.getElementById('bunkVerdict').style.textShadow = '0 0 20px #00ffff';
        } else {
            verdict = "ATTEND.";
            reason = "You have enough attendance, but it's life or death. Don't risk it.";
            document.getElementById('bunkVerdict').style.color = '#ffaa00';
            document.getElementById('bunkVerdict').style.textShadow = '0 0 20px #ffaa00';
        }
        
        document.getElementById('bunkVerdict').innerText = verdict;
        document.getElementById('bunkReason').innerText = reason;
        
    }, 1500);
};

window.resetBunk = () => {
    bunkData = { importance: '', friends: 0, attendance: 50 };
    document.getElementById('bunkFriends').value = '';
    document.getElementById('bunkAtt').value = 50;
    document.getElementById('bunkAttVal').innerText = '50%';
    document.getElementById('bunkBar').style.width = '0%';
    
    document.getElementById('bunkResult').style.display = 'none';
    document.getElementById('bunkStep1').style.display = 'flex';
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
    }).catch(e => console.error(e));
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
    }).catch(e => console.error(e));
};

function spawnSkulls() {
 // Overthinker 3000 Scenarios
const otScenarios = {
    "k": [
        "That 'K.' has more attitude than a Bollywood villain.",
        "They spent 2 minutes typing 'K.'? Something is wrong.",
        "A full stop after K is a digital slap.",
        "K means 'Kill me now, I am bored of you.'",
        "They are definitely talking about you in another group.",
        "That full stop means they hate you now.",
        "They only replied to end the conversation.",
        "They are typing a breakup message right now.",
        "They are literally rolling their eyes at your text.",
        "You've been officially friend-zoned by a single letter.",
        "They are with someone else and just sent a quick K.",
        "K is just a polite way of saying 'Shut up.'",
        "They are laughing at your long paragraph.",
        "They just screenshotted your text to roast you.",
        "That K was typed with pure anger."
    ],
    "seen": [
        "Read at 10:30 PM. It's 10:31 PM. You are dead to them.",
        "They saw it and decided a wall was more interesting.",
        "They are waiting for someone 'better' to reply first.",
        "They showed your text to their friends and they all laughed.",
        "They are drafting a roast but decided you aren't worth the effort.",
        "They are busy living a life that doesn't include you.",
        "The blue ticks are a sign of your impending doom.",
        "They have muted your chat forever.",
        "They are active on Instagram but ignored your text.",
        "They deleted the chat after reading it.",
        "They are thinking of ways to block you without feeling guilty.",
        "You are just another notification they want to swipe away.",
        "They are currently texting 5 other people.",
        "They read it and said 'Ugh, not this person again.'",
        "They are literally ghosting you in real-time."
    ],
    "busy": [
        "Busy? They are watching Netflix and ignoring you.",
        "Busy means 'Busy talking to someone else.'",
        "They have time for everyone except you.",
        "Busy is code for 'I don't want to talk, go away.'",
        "They are currently out with friends and laughing at your text.",
        "They will reply in 3 business days... maybe.",
        "They are intentionally making you wait to show dominance.",
        "They are busy... finding a replacement for you.",
        "Busy with 'important stuff' (scrolling Reels).",
        "They are just not that into you.",
        "They have time to post stories but not to reply.",
        "You are at the bottom of their priority list.",
        "They are pretending to be busy to avoid a long chat.",
        "They are literally waiting for you to stop texting."
    ],
    "late": [
        "Late? They are already there with someone else.",
        "They forgot you existed until 5 minutes ago.",
        "They are making an excuse while typing 'On my way.'",
        "They are actually just waking up now.",
        "They don't respect your time because they don't respect you.",
        "They are dreading meeting you.",
        "They are hoping you'll cancel so they can stay home.",
        "They are intentionally late to look 'cool.'",
        "They are with their ex and lost track of time.",
        "They are already thinking of how to leave early."
    ],
    "ok": [
        "Ok is the silent killer of conversations.",
        "Ok means 'I am not listening, but I have to reply.'",
        "They are clearly annoyed but being 'polite.'",
        "Ok is the shortest path to a breakup.",
        "They are bored to death by your existence.",
        "They are checking their watch while typing Ok.",
        "Ok was sent with a heavy sigh.",
        "They are wondering why they ever replied to you.",
        "Ok is just a placeholder for 'Get lost.'",
        "They are already talking to someone more interesting."
    ],
    "generic": [
        "They took 5 seconds longer to reply. PANIC!",
        "They didn't use an emoji. The end is near.",
        "They used a different font in their head while reading your text.",
        "They are definitely judging your grammar.",
        "They are showing this to their mom right now.",
        "They are planning to ghost you by Friday.",
        "They are literally cringing at your last text.",
        "They are wondering how to tell you to stop texting.",
        "They have a secret group chat about your weird habits.",
        "They are comparing you to their ex right now.",
        "They think you are 'too much' to handle.",
        "They are only talking to you because they are bored.",
        "They are already planning their next move without you.",
        "You are just a side character in their story.",
        "They are going to leave you on 'Delivered' for 2 days."
    ]
};

window.startPanicAttack = () => {
    const input = document.getElementById('otInput').value.toLowerCase();
    const list = document.getElementById('otList');
    const container = document.getElementById('otContainer');
    list.innerHTML = '';
    
    let category = "generic";
    if (input.includes('k') || input === 'k') category = "k";
    else if (input.includes('seen') || input.includes('read')) category = "seen";
    else if (input.includes('busy')) category = "busy";
    else if (input.includes('late')) category = "late";
    else if (input.includes('ok')) category = "ok";

    const pool = otScenarios[category];
    const genericPool = otScenarios["generic"];
    const fullPool = [...pool, ...genericPool];
    
    // Heartbeat Pulse Effect
    container.style.animation = "heartbeat-red 0.5s infinite";
    
    let count = 0;
    const interval = setInterval(() => {
        if (count >= 10) {
            clearInterval(interval);
            return;
        }
        
        const randomScenario = fullPool[Math.floor(Math.random() * fullPool.length)];
        const el = document.createElement('div');
        el.className = 'ot-item';
        el.style.cssText = "background:rgba(255,0,0,0.2); border-left:5px solid #ff0000; padding:15px; color:#fff; font-family:'Courier New', Courier, monospace; font-weight:bold; font-size:1.1rem; animation:shake 0.2s infinite, slideInRight 0.3s forwards; text-shadow:2px 2px #000; text-transform:uppercase;";
        el.innerText = `[PANIC] ${randomScenario}`;
        
        list.prepend(el);
        count++;
        
        // Auto scroll to top
        list.scrollTop = 0;
    }, 800);
}; 
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

// Excuse Generator
window.brewExcuse = () => {
    const cat = document.getElementById('exCat').value;
    const vic = document.getElementById('exVic').value;
    const res = document.getElementById('exResult');
    const base = cat === 'College' ? "my internet exploded" : "my pet ate my clothes";
    res.innerText = `I can't face the ${vic} because ${base}.`;
    res.style.display = 'block';
    res.style.color = '#00E5FF';
};

window.copyExcuse = () => {
    const text = document.getElementById('exResult').innerText;
    navigator.clipboard.writeText(text).then(() => {
        const btn = document.querySelector('.copy-btn');
        if(btn) {
            const oldText = btn.innerText;
            btn.innerText = "COPIED! ✅";
            setTimeout(() => btn.innerText = oldText, 2000);
        }
    });
};

window.resetExcuse = () => {
    if(document.getElementById('exInputArea')) document.getElementById('exInputArea').style.display = 'block';
    if(document.getElementById('exResultContainer')) document.getElementById('exResultContainer').style.display = 'none';
    if(document.getElementById('exStamp')) document.getElementById('exStamp').style.display = 'none';
    if(document.getElementById('exSituation')) document.getElementById('exSituation').value = '';
};

// Overthinker 3000
let otActive = false;
window.startOverthink = () => {
    if (otActive) return;
    const input = document.getElementById('otInput').value;
    if (!input) { alert("Type something to overthink about!"); return; }

    otActive = true;
    
    // Add loading indicator if missing
    if(!document.getElementById('otLoading')) {
        const loadingHtml = `<div id="otLoading" style="display:none; font-size:2rem; text-align:center; padding:20px; color:#ff00ff; animation: pulse 1s infinite;">Simulating Anxiety...</div>`;
        document.getElementById('otList').insertAdjacentHTML('beforebegin', loadingHtml);
    }
    
    document.getElementById('otInput').style.display = 'none';
    const btn = document.querySelector('#overthink-portal .glow-btn');
    if(btn) btn.style.display = 'none';
    
    document.getElementById('otLoading').style.display = 'block';
    if(document.getElementById('otResults')) document.getElementById('otResults').style.display = 'none';

    // 2-second loading drama
    setTimeout(() => {
        document.getElementById('otLoading').style.display = 'none';
        if(document.getElementById('otResults')) document.getElementById('otResults').style.display = 'block';
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
    document.getElementById('otInput').style.display = 'block';
    const btn = document.querySelector('#overthink-portal .glow-btn');
    if(btn) btn.style.display = 'block';
    document.getElementById('otInput').value = '';
    const loading = document.getElementById('otLoading');
    if(loading) loading.style.display = 'none';
    const results = document.getElementById('otResults');
    if(results) results.style.display = 'none';
    const list = document.getElementById('otList');
    if(list) {
        list.innerHTML = '';
        list.classList.remove('heartbeat');
    }
};

// Camera
function startCam() {
    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
        navigator.mediaDevices.getUserMedia({video:true}).then(s => {
            const vid = document.getElementById('myVid');
            if(vid) vid.srcObject = s;
        }).catch(e => console.log("Camera failed"));
    }
}

// ----------------------------------------------------
// PALM PREDICTION LOGIC
// ----------------------------------------------------
const palmFuture = [
    'You will find 500 rupees in an old jeans, then lose it in 5 minutes.', 
    'You will become a meme legend... as the "Before" picture.', 
    'You will finally understand 1+1, but only after failing the exam.', 
    'Success is coming... it just missed the bus. Wait 40 years.',
    'You will become famous on TikTok for a video you didn\'t want posted.',
    'Your Wi-Fi will be fast, but only when you have no work to do.',
    'You will conquer the world... in a dream you\'ll forget by 8 AM.',
    'A random cat will decide you are its servant. Accept your fate.',
    'You will invent a new excuse so good, even you will believe it.',
    'Your crypto will go to the moon... right after you sell it all.',
    'You will find your car keys in the fridge. Why? Nobody knows.',
    'You will win the lottery, but the ticket will be from 1994.',
    'You will meet the person of your dreams. They will ask for directions to your ex\'s house.',
    'You will be the lead role in a movie that never gets released.',
    'Your biography will be titled "Well, That Happened."',
    'You will discover a new planet, but it will be made of homework.'
];
const palmLove = [
    'Your crush will like your photo... because their screen was oily.', 
    'Single life is your destiny. Your cat is your only true love.', 
    'A wedding is coming... and you\'re the one serving the snacks.', 
    'You will fall in love with a pizza. It won\'t leave you.',
    'Someone is admiring you from afar. Keep them there. Afar.',
    'You and your bed: A toxic relationship you can\'t quit.',
    'Your soulmate is currently blocking you on all platforms.',
    'You will get a text back... once they need a favor.',
    'Love is blind. In your case, it\'s also legally deaf and mute.',
    'You will marry your phone. The honeymoon is just scrolling.',
    'Your ex will call you to ask for their Netflix password.',
    'You will have 12 kids. All of them will be succulents.',
    'You will find true love at a bus stop. They will be a bus.',
    'Your crush\'s name starts with "N"... as in "No Chance."',
    'You will go on a date with destiny. Destiny will split the bill.'
];
const palmCareer = [
    'CEO of a company that sells air. You\'ll go bankrupt.', 
    'Professional Overthinker. Salary: Paid in anxiety.', 
    'You will get a promotion... to "Unpaid Intern Plus."', 
    'Future owner of a tea stall that only serves lukewarm water.',
    'You will change jobs faster than you change your socks.',
    'Your boss will laugh at your joke... then fire you for it.',
    'You will be paid in "exposure" and "good vibes."',
    'Master of the "I was on mute" technique. It\'s your only skill.',
    'You will retire at 30... because your company folded at 29.',
    'Your LinkedIn profile is a work of fiction. Write a novel.',
    'You will be a billionaire... in Zimbabwe dollars.',
    'Your dream job exists, but it requires 200 years of experience.',
    'You will be the first person fired by an AI bot.',
    'You will invent a "Smart Nap" device. You are the only user.',
    'Your career path is a circle. You are currently at the bottom.'
];
const palmStudy = [
    'Your exams will be like your crush—they won\'t even look at you.',
    'You will open the PDF, stare at it, and become a philosopher instead.',
    'Top of the class! (In a class of one, and you\'re second).',
    'You will invent a new math theory: 1 Study Minute = 4 Hours of YouTube.',
    'Your brain has 99 tabs open, and all are playing "Baby Shark."',
    'You will study for 5 minutes and reward yourself with a 3-day vacation.',
    'The syllabus is written in a language you haven\'t discovered yet.',
    'You will magically remember the answer... 2 minutes after submitting.',
    'Ctrl+C and Ctrl+V will be the only reasons you graduate.',
    'You will pass by the grace of a teacher who is retiring and doesn\'t care.',
    'Your degree will be useful... as a very expensive coaster.',
    'You will spend more time choosing a playlist than actually studying.',
    'You will become an expert in "Procrastination Science."',
    'Your pens will all run out of ink the second the exam starts.',
    'You will graduate with honors... in Minecraft Architecture.'
];

window.activatePalmCamera = () => {
    console.log('Activating Palm Camera...');
    navigator.mediaDevices.getUserMedia({ video: true })
        .then(stream => {
            console.log('Camera Active');
            palmStream = stream;
            const vid = document.getElementById('palm-video');
            vid.srcObject = stream;
            vid.style.display = 'block';
            
            document.getElementById('palm-auth-methods').style.display = 'none';
            document.getElementById('capturePalmBtn').style.display = 'block';
            document.getElementById('handPlaceholder').style.zIndex = '6';
        })
        .catch(err => {
            console.error('Camera Error:', err);
            alert("Camera access denied! Check permissions.");
        });
};

let lastPalmIndices = { future: -1, love: -1, career: -1, study: -1, wealth: -1 };

function getRandomUnique(arr, category) {
    let idx;
    do {
        idx = Math.floor(Math.random() * arr.length);
    } while (idx === lastPalmIndices[category] && arr.length > 1);
    lastPalmIndices[category] = idx;
    return arr[idx];
}

let palmStream = null;

window.startPalmLiveCamera = () => {
    navigator.mediaDevices.getUserMedia({ video: { facingMode: "user" } })
        .then(stream => {
            palmStream = stream;
            const vid = document.getElementById('palmVideoFeed');
            vid.srcObject = stream;
            vid.style.display = 'block';
            
            // Hide the choice buttons, show the capture button
            document.getElementById('palm-auth-methods').style.display = 'none';
            document.getElementById('capturePalmBtn').style.display = 'block';
            
            // Bring hand placeholder to front so user can align their hand
            document.getElementById('handPlaceholder').style.zIndex = '6';
        }).catch(err => alert("Camera blocked or not available!"));
};

window.capturePalmPhoto = () => {
    const vid = document.getElementById('palm-video');
    const canvas = document.getElementById('palmHiddenCanvas');
    canvas.width = vid.videoWidth;
    canvas.height = vid.videoHeight;
    const ctx = canvas.getContext('2d');
    
    ctx.drawImage(vid, 0, 0, canvas.width, canvas.height);
    const dataURL = canvas.toDataURL('image/png');
    
    if (palmStream) {
        palmStream.getTracks().forEach(track => track.stop());
    }
    vid.style.display = 'none';
    document.getElementById('capturePalmBtn').style.display = 'none';
    
    triggerPalmScanFlow(dataURL);
};

window.startPalmScan = (event) => {
    const file = event.target.files[0];
    if(!file) return;

    const reader = new FileReader();
    reader.onload = (e) => {
        document.getElementById('palm-auth-methods').style.display = 'none';
        triggerPalmScanFlow(e.target.result);
    };
    reader.readAsDataURL(file);
};

function triggerPalmScanFlow(imageSrc) {
    document.getElementById('palmImagePreview').src = imageSrc;
    document.getElementById('palmImagePreview').style.display = 'block';
    document.getElementById('handPlaceholder').style.display = 'none';
    document.getElementById('scannerLine').style.display = 'block';
    
    // Start scanning phase
    setTimeout(() => {
        document.getElementById('palmUploadView').style.display = 'none';
        document.getElementById('palmScanningView').style.display = 'flex';
        
        // Wait 3 seconds for results
        setTimeout(() => {
            document.getElementById('palmScanningView').style.display = 'none';
            document.getElementById('palmResultsView').style.display = 'flex';
            
            document.getElementById('txtFuture').innerText = getRandomUnique(palmFuture, 'future');
            document.getElementById('txtCareer').innerText = getRandomUnique(palmCareer, 'career');
            document.getElementById('txtStudy').innerText = getRandomUnique(palmStudy, 'study');
            document.getElementById('txtLove').innerText = getRandomUnique(palmLove, 'love');
            document.getElementById('txtWealth').innerText = getRandomUnique(palmWealth, 'wealth');
            
        }, 3000);
    }, 1500);
}

window.resetPalm = () => {
    document.getElementById('palmResultsView').style.display = 'none';
    document.getElementById('palmUploadView').style.display = 'flex';
    document.getElementById('palmImagePreview').style.display = 'none';
    document.getElementById('palmVideoFeed').style.display = 'none';
    document.getElementById('handPlaceholder').style.display = 'block';
    document.getElementById('scannerLine').style.display = 'none';
    
    // Reset file input
    const input = document.getElementById('palmInput');
    if(input) input.value = '';
    
    document.getElementById('palm-auth-methods').style.display = 'flex';
    document.getElementById('capturePalmBtn').style.display = 'none';
    
// ----------------------------------------------------
// EXCUSE GENERATOR LOGIC
// ----------------------------------------------------

const excuseDB = {
    Professor: {
        Late: {
            Emotional: ["My dog ate my alarm clock and I was too busy crying to realize the time.", "I had an existential crisis on the way here about the meaning of higher education.", "A pigeon looked at me sad, so I had to feed it. It was a beautiful moment.", "I was helping an old lady cross the street... and she lived really far away.", "My heart wasn't ready to face the complexities of today's lecture."],
            Savage: ["I was debating whether this class is actually worth my tuition.", "I didn't want to seem too eager. It ruins my mysterious aura.", "Traffic was bad, but honestly, I was just scrolling TikTok in the parking lot.", "I am not late, the lecture simply started before I arrived.", "I run on Elon Musk time."],
            Professional: ["I was caught in an unexpected synergy meeting with my inner demons.", "Due to unforeseen logistical bottlenecks in my morning routine, my arrival was delayed.", "I was optimizing my workflow for maximum academic output.", "An unexpected pivot in my transport strategy caused a slight schedule variance.", "I was engaged in a deep-dive analysis of the traffic light patterns."],
            Confused: ["Wait, we had class today? I thought it was Sunday.", "Am I in the right room? Who are you people?", "I walked into the wrong campus, sir.", "My calendar app is in a different timezone for some reason.", "I thought the lecture was a podcast today."]
        },
        Assignment: {
            Emotional: ["My laptop died and took all my hopes and dreams with it.", "I poured my soul into it, and then accidentally deleted the folder.", "My cat walked over the keyboard and submitted a blank document.", "The Wi-Fi went down, and so did my mental health.", "I stayed up all night, and then spilled coffee on the hard drive."],
            Savage: ["My assignment was so fire it literally burnt my laptop. Here is the ash.", "I outsourced it to ChatGPT but it refused to do it because the prompt was too boring.", "I decided the prompt didn't align with my creative vision.", "I graded myself. I got an A. You don't need to see it.", "I didn't do it because I'm practicing quiet quitting."],
            Professional: ["The assignment is currently in the beta testing phase and is not ready for deployment.", "I encountered a critical error during the compilation of my research matrix.", "We are facing supply chain issues with the necessary intellectual resources.", "The deliverable is pending final stakeholder approval.", "I am currently pivoting the project scope to better align with syllabus KPIs."],
            Confused: ["Assignment? I thought that was just a suggestion.", "I wrote it on paper, but I don't know what paper is anymore.", "Wasn't that due next semester?", "I submitted it telepathically. Did you not receive it?", "I thought we were supposed to perform the assignment through interpretive dance."]
        },
        Bunked: {
            Emotional: ["My bed was holding me hostage and I couldn't break its heart.", "I needed a mental health day to process the plot twist in my TV show.", "I was mourning the loss of my motivation.", "The weather was too beautiful, it felt like a sin to be indoors.", "I was trying to find myself."],
            Savage: ["I valued my sleep more than your slides.", "I already knew everything you were going to teach.", "I took a calculated risk, and boy, am I bad at math.", "I was busy building my empire.", "Your lecture was competing with a very comfortable pillow. The pillow won."],
            Professional: ["I utilized PTO to recalibrate my academic bandwidth.", "I was attending an off-site strategic ideation session (in my dreams).", "I was executing a proactive risk management strategy regarding burnout.", "I was streamlining my core competencies by skipping non-essential deliverables.", "I was engaged in independent study of advanced napping techniques."],
            Confused: ["I was here... in spirit.", "Did I bunk? I thought I was invisible.", "I thought it was a public holiday.", "I got lost on the way to the bathroom and ended up at home.", "I swear I was sitting in the back row, maybe I blended into the wall."]
        },
        ForgotBirthday: {
            Emotional: ["I was so overwhelmed by how much I love you that my brain short-circuited.", "I didn't forget, I was just too busy planning a surprise for next year.", "Every day is your birthday to me, so I didn't want to restrict it to just one day.", "I was crying over how fast you are growing up.", "I wanted to see if our telepathic connection was working."],
            Savage: ["I didn't forget, I just don't care about the concept of time.", "I thought you stopped aging after 21.", "I was waiting to see if you would remind me.", "I got you the gift of silence.", "I am too broke to acknowledge this day."],
            Professional: ["Your annual natal anniversary was omitted from my Q3 projections.", "We are experiencing a temporary delay in the delivery of celebratory assets.", "Please accept this retroactive acknowledgement of your birth event.", "The birthday module failed to load in my memory bank.", "We have rescheduled the celebration to align with our current fiscal timeline."],
            Confused: ["Wait, you were born?", "I thought your birthday was in leap year.", "Am I not your gift?", "I thought we agreed to skip this year.", "What is a birthday, really? Just a construct."]
        }
    },
    // Adding fallbacks for other targets
    HOD: { Late: null, Assignment: null, Bunked: null, ForgotBirthday: null },
    Parents: { Late: null, Assignment: null, Bunked: null, ForgotBirthday: null },
    Crush: { Late: null, Assignment: null, Bunked: null, ForgotBirthday: null },
    Boss: { Late: null, Assignment: null, Bunked: null, ForgotBirthday: null }
};

// Fill out the missing combinations with generic but tailored ones if needed, 
// for brevity we just fallback to Professor for missing specific logic, but with slight tweaks.
function getExcuseText(target, situation, vibe) {
    // If specific logic doesn't exist for a target, default to Professor array but replace some words if needed
    let pool = excuseDB[target]?.[situation]?.[vibe] || excuseDB['Professor'][situation][vibe];
    let excuse = pool[Math.floor(Math.random() * pool.length)];
    
    if(target === 'Boss') excuse = excuse.replace('lecture', 'meeting').replace('class', 'shift').replace('tuition', 'salary').replace('syllabus', 'company goals');
    if(target === 'Parents') excuse = excuse.replace('tuition', 'allowance').replace('Professor', 'Mom/Dad').replace('assignment', 'chores');
    if(target === 'Crush') excuse = excuse.replace('assignment', 'text back').replace('lecture', 'date').replace('class', 'our hangout');
    if(target === 'HOD') excuse = excuse.replace('Professor', 'HOD').replace('class', 'department meeting');
    
    return excuse;
}

window.brewExcuse = () => {
    const target = document.getElementById('exTarget').value;
    const situation = document.getElementById('exSituation').value;
    const vibe = document.getElementById('exVibe').value;

    if (!target || !situation || !vibe) {
        alert("Select all ingredients to brew the lie!");
        return;
    }

    document.getElementById('exInputView').style.display = 'none';
    document.getElementById('exBrewingView').style.display = 'flex';

    setTimeout(() => {
        const finalExcuse = getExcuseText(target, situation, vibe);
        document.getElementById('exResultText').innerText = finalExcuse;
        
        document.getElementById('exBrewingView').style.display = 'none';
        document.getElementById('exResultView').style.display = 'flex';
    }, 2500); // 2.5s brewing
};

window.copyExcuse = () => {
    const text = document.getElementById('exResultText').innerText;
    navigator.clipboard.writeText(text).then(() => {
        alert("Copied to clipboard! Go save your life on WhatsApp.");
    });
};

window.resetExcuse = () => {
    document.getElementById('exResultView').style.display = 'none';
    document.getElementById('exInputView').style.display = 'flex';
};
