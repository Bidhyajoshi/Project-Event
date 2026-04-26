document.addEventListener('DOMContentLoaded', () => {
    const cursor = document.getElementById('custom-cursor');
    const starfield = document.getElementById('starfield');
    const gravityWell = document.getElementById('gravity-well');
    let idleTimer;

    // 1. Custom Cursor Movement
    document.addEventListener('mousemove', (e) => {
        cursor.style.left = e.clientX + 'px';
        cursor.style.top = e.clientY + 'px';
        
        // Reset idle timer on movement
        resetIdleTimer();
    });

    // 2. Generate Starfield
    function createStars() {
        const starCount = 150;
        for (let i = 0; i < starCount; i++) {
            const star = document.createElement('div');
            star.className = 'star';
            const x = Math.random() * 100;
            const y = Math.random() * 100;
            const size = Math.random() * 2 + 1;
            const duration = Math.random() * 3 + 2;
            
            star.style.left = `${x}%`;
            star.style.top = `${y}%`;
            star.style.width = `${size}px`;
            star.style.height = `${size}px`;
            star.style.setProperty('--duration', `${duration}s`);
            
            starfield.appendChild(star);
        }
    }
    createStars();

    // 3. Load Feature Function
    window.loadFeature = (featureName) => {
        const features = {
            'bunk': {
                title: 'Bunk Probability 🎲',
                content: 'Current chance of success: <span style="color: var(--electric-cyan);">85%</span>. Professor looks sleepy.',
                accent: 'var(--electric-cyan)'
            },
            'excuses': {
                title: 'Excuse Generator 🤥',
                content: '"My cat accidentally submitted a DMCA takedown against my homework."',
                accent: '#FFD700'
            },
            'overthink': {
                title: 'Overthink Tank 🧠',
                content: 'Processing 1.2 million scenarios for why they said "K" instead of "Okay".',
                accent: 'var(--neon-violet)'
            },
            'chat': {
                title: 'Shadow Chat 💬',
                content: 'Connecting to the underground college network... [Encrypted]',
                accent: '#00FF41'
            }
        };

        const data = features[featureName];

        // Fade out
        gravityWell.classList.add('fade-out');

        setTimeout(() => {
            gravityWell.innerHTML = `
                <div class="glass-card" style="border-color: ${data.accent}">
                    <h2 style="color: ${data.accent}; margin-bottom: 1rem;">${data.title}</h2>
                    <p style="opacity: 0.9; line-height: 1.6; font-size: 1.1rem;">${data.content}</p>
                    <button onclick="location.reload()" style="margin-top: 1.5rem; background: transparent; border: 1px solid ${data.accent}; color: white; padding: 8px 16px; border-radius: 8px; cursor: pointer; transition: 0.3s; font-family: inherit;">Reset Gravity</button>
                </div>
            `;
            // Fade in
            gravityWell.classList.remove('fade-out');
            gravityWell.classList.add('fade-in');
            
            setTimeout(() => {
                gravityWell.classList.remove('fade-in');
            }, 400);
        }, 400);
    };

    // 4. Idle Shake Effect
    function resetIdleTimer() {
        document.body.classList.remove('shifting-gravity');
        clearTimeout(idleTimer);
        idleTimer = setTimeout(() => {
            document.body.classList.add('shifting-gravity');
        }, 5000); // Trigger after 5 seconds of idle
    }

    // Initialize idle timer
    resetIdleTimer();

    // Interaction feedback for nav items
    const navItems = document.querySelectorAll('.nav-item');
    navItems.forEach(item => {
        item.addEventListener('mouseenter', () => {
            cursor.style.transform = 'scale(2.5)';
            cursor.style.borderColor = 'var(--neon-violet)';
            cursor.style.boxShadow = '0 0 25px var(--neon-violet)';
        });
        item.addEventListener('mouseleave', () => {
            cursor.style.transform = 'scale(1)';
            cursor.style.borderColor = 'var(--electric-cyan)';
            cursor.style.boxShadow = '0 0 15px var(--electric-cyan)';
        });
    });
});
