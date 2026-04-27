<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Floating Student ID | Anti-Gravity</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
</head>
<body>

    <!-- Glow Orbs -->
    <div class="glow-orb orb-purple"></div>
    <div class="glow-orb orb-mint"></div>

    <!-- Background Chaos -->
    <div class="bg-chaos-container" id="bgChaos"></div>

    <!-- Easter Eggs -->
    <div class="easter-egg egg-bottom-left">
        Gravity: 0% | Stress: 100%
    </div>
    <div class="easter-egg egg-top-right">
        <div class="live-dot"></div>
        <span id="bunker-status">Loading Legends...</span>
    </div>

    <!-- Main Floating Container -->
    <div class="gate-wrapper">
        <div class="glass-portal gate-container" id="gateCard">
            
            <!-- Progress Bar -->
            <div id="progress-container" style="display: none; margin-bottom: 30px;">
                <div class="progress-bar">
                    <div class="progress-fill" id="progressFill"></div>
                </div>
                <p id="step-label" class="step-indicator">Step 1 of 3</p>
            </div>

            <div class="auth-slider" id="authSlider">
                <!-- LOGIN FACE -->
                <div class="auth-slide active" id="loginSlide">
                    <div class="hologram-photo floating-icon">🌌</div>
                    <h1 class="portal-title">VOID PORTAL</h1>
                    <p class="portal-subtitle">Ready to escape reality and defy gravity?</p>
                    
                    <form id="loginForm" class="portal-form">
                        <div class="input-wrap">
                            <input type="text" name="username" placeholder="Legendary Nickname" required>
                        </div>
                        <div class="input-wrap">
                            <input type="password" name="password" placeholder="Secret Portal Code" required>
                        </div>
                        <button type="submit" class="btn-beam-3d">BEAM ME IN</button>
                    </form>

                    <button class="btn-minimalist" onclick="switchTo('register')">Become a Floating Student</button>
                </div>

                <!-- REGISTER VIEW -->
                <div class="auth-slide" id="registerSlide">
                    <form id="registerForm" class="portal-form">
                        <!-- Step 1: Identity -->
                        <div class="reg-step active" id="step1">
                            <h2 class="neon-text-pink">WHO ARE YOU?</h2>
                            <div class="input-wrap">
                                <input type="text" name="reg_username" id="reg_username" placeholder="Legendary Nickname">
                            </div>
                            <div class="input-wrap">
                                <input type="password" name="reg_password" id="reg_password" placeholder="Secret Portal Code">
                            </div>
                            <button type="button" class="btn-beam-3d" onclick="goToRegStep(2)">Next Level ➔</button>
                        </div>

                        <!-- Step 2: Survival Tool -->
                        <div class="reg-step" id="step2">
                            <h2 class="neon-text-pink">PICK YOUR TOOL</h2>
                            <div class="emoji-grid">
                                <div class="emoji-card" onclick="pickEmojiCard('tool', 'Coffee Cup ☕', this)">
                                    <span class="emoji">☕</span>
                                    <p>Eternal Fuel</p>
                                </div>
                                <div class="emoji-card" onclick="pickEmojiCard('tool', 'Notes 📝', this)">
                                    <span class="emoji">📝</span>
                                    <p>Cheat Sheet</p>
                                </div>
                                <div class="emoji-card" onclick="pickEmojiCard('tool', 'Motivation 🔥', this)">
                                    <span class="emoji">🔥</span>
                                    <p>1 AM Fire</p>
                                </div>
                            </div>
                            <input type="hidden" name="survival_tool" id="survival_tool">
                            <button type="button" class="btn-beam-3d" style="margin-top: 20px;" onclick="goToRegStep(3)">Looking Sharp ➔</button>
                        </div>

                        <!-- Step 3: Earthly Identity -->
                        <div class="reg-step" id="step3">
                            <h2 class="neon-text-pink">EARTHLY ROLE</h2>
                            <div class="emoji-grid">
                                <div class="emoji-card" onclick="pickEmojiCard('identity', 'Backbench King 👑', this)">
                                    <span class="emoji">👑</span>
                                    <p>Backbench King</p>
                                </div>
                                <div class="emoji-card" onclick="pickEmojiCard('identity', 'Frontbench Queen 👩🎓', this)">
                                    <span class="emoji">👩🎓</span>
                                    <p>Frontbench Queen</p>
                                </div>
                                <div class="emoji-card" onclick="pickEmojiCard('identity', 'Agent of Chaos 👽', this)">
                                    <span class="emoji">👽</span>
                                    <p>Chaos Agent</p>
                                </div>
                            </div>
                            <input type="hidden" name="earthly_identity" id="earthly_identity">
                            <button type="submit" class="btn-beam-3d" style="margin-top: 20px;">ASCEND TO VOID</button>
                        </div>
                    </form>
                    <button class="btn-minimalist" onclick="switchTo('login')">Return to Portal</button>
                </div>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
    <script>
        // Background Chaos Generation
        const bgChaos = document.getElementById('bgChaos');
        const bgIcons = ['🍕', '☕', '📚', '🎧', '💡', '🧪', '😴', '🧠'];
        const bgElements = [];

        function initBackground() {
            for (let i = 0; i < 15; i++) {
                const el = document.createElement('div');
                el.className = 'bg-element';
                el.innerText = bgIcons[Math.floor(Math.random() * bgIcons.length)];
                
                // Random position
                const x = Math.random() * 100;
                const y = Math.random() * 100;
                el.style.left = x + '%';
                el.style.top = y + '%';
                
                // Random drift speed and rotation
                const duration = 15 + Math.random() * 20;
                const delay = Math.random() * -20;
                el.style.animation = `drift ${duration}s linear infinite ${delay}s`;
                
                bgChaos.appendChild(el);
                bgElements.push({
                    el: el,
                    baseX: x,
                    baseY: y,
                    factor: 0.05 + Math.random() * 0.1 // Parallax strength
                });
            }
        }

        // Parallax Effect
        document.addEventListener('mousemove', (e) => {
            const moveX = (e.clientX - window.innerWidth / 2) * 0.01;
            const moveY = (e.clientY - window.innerHeight / 2) * 0.01;

            bgElements.forEach(item => {
                const px = moveX * item.factor * 10;
                const py = moveY * item.factor * 10;
                item.el.style.transform = `translate(${-px}px, ${-py}px) rotate(${px * 2}deg)`;
            });
        });

        // Drift Animation Style
        const style = document.createElement('style');
        style.innerHTML = `
            @keyframes drift {
                0% { transform: translate(0, 0) rotate(0deg); }
                25% { transform: translate(30px, 30px) rotate(10deg); }
                50% { transform: translate(0, 60px) rotate(0deg); }
                75% { transform: translate(-30px, 30px) rotate(-10deg); }
                100% { transform: translate(0, 0) rotate(0deg); }
            }
        `;
        document.head.appendChild(style);
        initBackground();

        // Update bunker count from real API
        async function updateOnlineCount() {
            try {
                const res = await fetch('online_count.php');
                const data = await res.json();
                const status = document.getElementById('bunker-status');
                
                if (data.success) {
                    const count = data.count;
                    if (count <= 1) {
                        status.innerText = "You're the only legend here right now. Forever alone? 💀";
                    } else {
                        status.innerText = `${count} Legends currently bunking classes`;
                    }
                }
            } catch (e) {
                console.error("Failed to fetch online count", e);
            }
        }

        setInterval(updateOnlineCount, 10000);
        updateOnlineCount();

        // Typing effects & Bubbling
        const inputs = [document.getElementById('login-input'), document.getElementById('reg-input')];
        const displays = [document.getElementById('display-login-name'), document.getElementById('display-reg-name')];
        const emojis = ['📝', '☕', '🤪', '💻', '✨', '🚀', '🧠', '🍕'];

        inputs.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                const val = e.target.value || (index === 0 ? "STUDENT NAME" : "NEW LEGEND");
                displays[index].innerText = val;

                // Create bubble
                const bubble = document.createElement('div');
                bubble.className = 'emoji-bubble';
                bubble.innerText = emojis[Math.floor(Math.random() * emojis.length)];
                bubble.style.left = (Math.random() * 80 + 10) + '%';
                bubble.style.setProperty('--x', (Math.random() * 100 - 50) + 'px');
                
                const card = index === 0 ? document.getElementById('loginCard') : document.getElementById('registerCard');
                card.appendChild(bubble);
                setTimeout(() => bubble.remove(), 1500);
            });
        });

        function switchTo(view) {
            const loginSlide = document.getElementById('loginSlide');
            const registerSlide = document.getElementById('registerSlide');
            const progress = document.getElementById('progress-container');

            if (view === 'register') {
                loginSlide.classList.remove('active');
                loginSlide.classList.add('slide-left');
                registerSlide.classList.add('active');
                progress.style.display = 'block';
                goToRegStep(1);
            } else {
                registerSlide.classList.remove('active');
                loginSlide.classList.remove('slide-left');
                loginSlide.classList.add('active');
                progress.style.display = 'none';
            }
        }

        function goToRegStep(step) {
            // Validation
            if (step === 2 && !document.getElementById('reg_username').value) return;
            if (step === 3 && !document.getElementById('survival_tool').value) {
                Swal.fire({ title: 'Pick a tool!', text: 'You can\'t survive the void empty-handed.', icon: 'warning' });
                return;
            }

            document.querySelectorAll('.reg-step').forEach(s => s.classList.remove('active'));
            document.getElementById('step' + step).classList.add('active');

            // Update Progress
            const fill = document.getElementById('progressFill');
            const label = document.getElementById('step-label');
            const percent = (step / 3) * 100;
            fill.style.width = percent + '%';
            label.innerText = `Step ${step} of 3`;
        }

        function pickEmojiCard(type, value, element) {
            document.getElementById(type === 'tool' ? 'survival_tool' : 'earthly_identity').value = value;
            
            // Highlight with a 'pop'
            const container = element.parentElement;
            container.querySelectorAll('.emoji-card').forEach(c => c.classList.remove('selected'));
            element.classList.add('selected');
            
            // Mini burst effect
            confetti({
                particleCount: 20,
                spread: 30,
                origin: { 
                    x: element.getBoundingClientRect().left / window.innerWidth, 
                    y: element.getBoundingClientRect().top / window.innerHeight 
                },
                colors: ['#8A2BE2', '#FF007F']
            });
        }

        // AJAX for Login
        document.getElementById('loginForm').onsubmit = async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            formData.append('action', 'login');
            
            const res = await fetch('auth.php', { method: 'POST', body: formData });
            const data = await res.json();

            if (data.success) {
                confetti({ particleCount: 150, spread: 70, origin: { y: 0.6 } });
                Swal.fire({ title: 'Access Granted!', text: 'Welcome to the dimension.', icon: 'success' })
                .then(() => window.location.href = 'index.php');
            } else {
                Swal.fire({ title: 'Access Denied', text: data.message, icon: 'error' });
            }
        };

        // AJAX for Register
        document.getElementById('registerForm').onsubmit = async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            formData.append('action', 'register');
            
            if (!document.getElementById('earthly_identity').value) {
                Swal.fire({ title: 'Who are you?', text: 'Specify your earthly identity.', icon: 'warning' });
                return;
            }

            const res = await fetch('auth.php', { method: 'POST', body: formData });
            const data = await res.json();

            if (data.success) {
                confetti({ particleCount: 200, spread: 160, origin: { y: 0.6 } });
                Swal.fire({ title: 'Success!', text: 'Your ID is now valid.', icon: 'success' })
                .then(() => window.location.href = 'index.php');
            } else {
                Swal.fire({ title: 'Error', text: data.message, icon: 'error' });
            }
        };
    </script>
</body>
</html>
