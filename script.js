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
    // Optionally trigger AJAX to register in db here
};

// Portals
window.openPortal = (id) => {
    const p = document.getElementById(id);
    p.style.display = 'flex';
    setTimeout(() => p.classList.add('active'), 50);
    if(id === 'ome-portal') startCam();
};
window.closePortal = (id) => {
    const p = document.getElementById(id);
    p.classList.remove('active');
    setTimeout(() => { p.style.display = 'none'; }, 400);
    if(id === 'overthink-portal') resetOT();
};

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

// Savage AI
window.sendAIMsg = () => {
    const box = document.getElementById('aiChatBox');
    const input = document.getElementById('aiInput');
    const val = input.value;
    if(!val) return;

    box.innerHTML += `<div class="msg user">${val}</div>`;
    input.value = '';

    let reply = "Skill issue.";
    if(val.toLowerCase().includes('crush')) reply = "They left you on read 3 years ago.";
    if(val.toLowerCase().includes('exam')) reply = "Start packing for the Himalayas.";
    if(val.toLowerCase().includes('money')) reply = "Your wallet is a black hole.";

    setTimeout(() => {
        box.innerHTML += `<div class="msg ai">${reply}</div>`;
        box.scrollTop = box.scrollHeight;
    }, 500);
};

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

// Camera
function startCam() {
    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
        navigator.mediaDevices.getUserMedia({video:true}).then(s => {
            document.getElementById('myVid').srcObject = s;
        }).catch(e => console.log("Camera failed"));
    }
}
