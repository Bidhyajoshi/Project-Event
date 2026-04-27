document.addEventListener('DOMContentLoaded', () => {
    const cursor = document.getElementById('custom-cursor');
    const starfield = document.getElementById('starfield');
    const gravityWell = document.getElementById('gravity-well');
    let idleTimer;

    // 1. Custom Cursor Movement (Smoother & Bouncier)
    document.addEventListener('mousemove', (e) => {
        cursor.style.left = e.clientX + 'px';
        cursor.style.top = e.clientY + 'px';
        resetIdleTimer();
    });

    // 2. Space-Rave Background with Floating Chaos
    function createChaos() {
        const elements = ['🤡', '📈', '🗑️', '👑', '💸', '🧠', '💀', '👽', '🍆', '✨'];
        const count = 30;
        
        for (let i = 0; i < count; i++) {
            const el = document.createElement('div');
            el.className = 'floating-element';
            el.innerText = elements[Math.floor(Math.random() * elements.length)];
            
            const startX = Math.random() * 100;
            const startY = Math.random() * 100;
            const duration = 10 + Math.random() * 20;
            
            el.style.left = `${startX}%`;
            el.style.top = `${startY}%`;
            el.style.setProperty('--duration', `${duration}s`);
            el.style.fontSize = `${1 + Math.random() * 2}rem`;
            
            starfield.appendChild(el);
        }
    }
    createChaos();

    // 3. Load Feature Function (Chaotic Edition)
    window.loadFeature = (featureName) => {
        const features = {
            'bunk': {
                title: 'BUNK OR DIE 🎲',
                content: 'Probability: <span style="color: var(--electric-cyan);">HIGH</span>. The professor is literally reading from a PDF.',
                accent: 'var(--electric-cyan)'
            },
            'excuses': {
                title: 'LIAR LIAR 🤥',
                content: '"My internet had a stroke and died during the submission."',
                accent: 'var(--electric-pink)'
            },
            'overthink': {
                title: 'BRAIN ROT 🧠',
                content: 'Overthinking why they didn\'t like your story. (It\'s because you\'re weird).',
                accent: 'var(--neon-purple)'
            },
            'chat': {
                title: 'DARK WEB 💬',
                content: 'Talking to people you\'ll ignore in the hallway tomorrow.',
                accent: '#00FF41'
            }
        };

        const data = features[featureName];
        if (!gravityWell) return;

        gravityWell.classList.add('fade-out');

        setTimeout(() => {
            gravityWell.innerHTML = `
                <div class="glass-bubble" style="border-color: ${data.accent}; border-radius: 30px;">
                    <h2 class="neon-text-pink" style="color: ${data.accent}; margin-bottom: 1rem;">${data.title}</h2>
                    <p style="opacity: 0.9; line-height: 1.6; font-size: 1.1rem;">${data.content}</p>
                    <button onclick="location.reload()" class="btn-chaos" style="padding: 10px 20px; font-size: 0.9rem; background: ${data.accent}; color: black;">RESET REALITY</button>
                </div>
            `;
            gravityWell.classList.remove('fade-out');
            gravityWell.classList.add('fade-in');
            
            setTimeout(() => {
                gravityWell.classList.remove('fade-in');
            }, 500);
        }, 500);
    };

    // 4. Idle Gravity Shift (More Extreme)
    function resetIdleTimer() {
        document.body.classList.remove('shifting-gravity');
        clearTimeout(idleTimer);
        idleTimer = setTimeout(() => {
            document.body.classList.add('shifting-gravity');
        }, 8000);
    }
    resetIdleTimer();

    // 5. Self Destruct (Nuke)
    window.selfDestruct = () => {
        if (typeof confetti !== 'undefined') {
            confetti({
                particleCount: 150,
                spread: 180,
                colors: ['#ff0000', '#000000', '#ffffff']
            });
        }
        document.body.style.transition = 'all 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
        document.body.style.transform = 'scale(0) rotate(720deg)';
        document.body.style.filter = 'invert(1) blur(10px)';
        setTimeout(() => {
            window.location.href = 'logout.php';
        }, 1000);
    };

    // Custom hover effects for anything clickable
    const clickables = document.querySelectorAll('.nav-item, .btn-chaos, .character-card, .toggle-link');
    clickables.forEach(item => {
        item.addEventListener('mouseenter', () => {
            cursor.style.transform = 'scale(3)';
            cursor.style.borderColor = 'var(--neon-purple)';
            cursor.style.background = 'rgba(191, 0, 255, 0.2)';
        });
        item.addEventListener('mouseleave', () => {
            cursor.style.transform = 'scale(1)';
            cursor.style.borderColor = 'var(--electric-pink)';
            cursor.style.background = 'transparent';
        });
    });
});
