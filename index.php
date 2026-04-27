<?php
session_start();
$pdo = require 'db.php';
$isLoggedIn = isset($_SESSION['user_id']);
if ($isLoggedIn) {
    updateActivity($pdo);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ANTI-GRAVITY | College Survival Playground</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
</head>
<body>

    <!-- Background Chaos & Orbs (Shared) -->
    <div class="glow-orb orb-purple"></div>
    <div class="glow-orb orb-mint"></div>
    <div class="bg-chaos-container" id="bgChaos"></div>

    <!-- Navigation -->
    <nav class="top-nav">
        <div class="logo-text">ANTI-GRAVITY</div>
        <div class="nav-actions">
            <?php if ($isLoggedIn): ?>
                <div class="user-pill">
                    <span>Legend: <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    <button class="btn-nuke" onclick="selfDestruct()">Self-Destruct 💥</button>
                </div>
            <?php else: ?>
                <button class="btn-join" onclick="window.location.href='gate.php'">Join the Chaos 🚀</button>
            <?php endif; ?>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <h1 class="hero-title">ANTI-GRAVITY</h1>
        <p class="hero-subtitle">Your College Survival Playground. Defy Logic. Escape Reality.</p>
        <button class="btn-pop" onclick="document.getElementById('playground').scrollIntoView({behavior: 'smooth'})">Get Started ➔</button>
    </section>

    <!-- Main Playground -->
    <main id="playground" class="playground-grid">
        
        <!-- EXCUSE GENERATOR -->
        <div class="tool-card glass-bubble" onclick="openTool('excuse')">
            <div class="tool-icon">🤥</div>
            <h3>Excuse Generator</h3>
            <p>Generate high-quality lies for your professors.</p>
        </div>

        <!-- BUNK METER -->
        <div class="tool-card glass-bubble" onclick="openTool('bunk')">
            <div class="tool-icon">🎲</div>
            <h3>Bunk Maker</h3>
            <p>Let destiny decide your attendance today.</p>
        </div>

        <!-- OVERTHINKING SIMULATOR -->
        <div class="tool-card glass-bubble" onclick="openTool('overthink')">
            <div class="tool-icon">🧠</div>
            <h3>Overthinker 3000</h3>
            <p>Enter a simple text, get 5 life-ruining scenarios.</p>
        </div>

        <!-- PALM READER -->
        <div class="tool-card glass-bubble" onclick="openTool('palm')">
            <div class="tool-icon">🤚</div>
            <h3>AI Luck Scanner</h3>
            <p>Scan your palm to see if you'll pass your exams.</p>
        </div>

    </main>

    <!-- Tool Modal (Floating Container) -->
    <div id="toolModal" class="modal-overlay">
        <div class="modal-content glass-bubble">
            <button class="close-modal" onclick="closeTool()">×</button>
            <div id="toolContent"></div>
        </div>
    </div>

    <script src="script.js"></script>
    <script>
        const isLoggedIn = <?php echo $isLoggedIn ? 'true' : 'false'; ?>;

        function openTool(toolType) {
            if (!isLoggedIn) {
                Swal.fire({
                    title: 'WHOA! 🛑',
                    text: 'Only Legends can use this. Sign in to unlock the chaos.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Join the Chaos 🚀',
                    cancelButtonText: 'Maybe later',
                    background: '#FFF',
                    color: '#4A4A4A',
                    confirmButtonColor: '#8A2BE2'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'gate.php';
                    }
                });
                return;
            }

            const modal = document.getElementById('toolModal');
            const content = document.getElementById('toolContent');
            modal.style.display = 'flex';

            if (toolType === 'excuse') {
                content.innerHTML = `
                    <h2 class="neon-text-purple">Excuse Gen</h2>
                    <p>Pick your Vibe:</p>
                    <div class="choice-grid">
                        <div class="choice-item" onclick="generateExcuse('Sick')">🤒 Sick</div>
                        <div class="choice-item" onclick="generateExcuse('Family')">🏠 Family Event</div>
                        <div class="choice-item" onclick="generateExcuse('Crisis')">🌀 Existential Crisis</div>
                    </div>
                    <div id="excuseResult" class="result-box"></div>
                `;
            } else if (toolType === 'bunk') {
                content.innerHTML = `
                    <h2 class="neon-text-purple">Bunk Meter</h2>
                    <div class="meter-container">
                        <div class="meter-needle" id="needle"></div>
                        <div class="meter-label">?</div>
                    </div>
                    <button class="btn-pop" onclick="spinBunkMeter()">Spin Destiny</button>
                    <div id="bunkResult" class="result-box"></div>
                `;
            } else if (toolType === 'overthink') {
                content.innerHTML = `
                    <h2 class="neon-text-purple">Overthinker</h2>
                    <input type="text" id="overthinkInput" placeholder="What did they say?">
                    <button class="btn-pop" onclick="simulateOverthinking()">Analyze Chaos</button>
                    <div id="overthinkResults" class="scenarios-list"></div>
                `;
            } else if (toolType === 'palm') {
                content.innerHTML = `
                    <h2 class="neon-text-purple">Luck Scanner</h2>
                    <div class="scanner-box">
                        <div class="scanner-line"></div>
                        <span style="font-size: 5rem;">🤚</span>
                    </div>
                    <button class="btn-pop" onclick="scanPalm()">Initialize Scan</button>
                    <div id="scanResult" class="result-box"></div>
                `;
            }
        }

        function closeTool() {
            document.getElementById('toolModal').style.display = 'none';
        }

        // Feature Logic
        function generateExcuse(vibe) {
            const excuses = {
                'Sick': ["My immune system decided to take a gap year today.", "I accidentally ate a digital cookie and got a virus.", "My pillow has developed separation anxiety."],
                'Family': ["My grandmother's cat is getting married.", "I have to help my family move the internet to the living room.", "My cousin's imaginary friend is visiting."],
                'Crisis': ["I realized that gravity is just a suggestion and I'm currently floating.", "I'm having a mid-semester crisis about the entropy of the universe.", "My 1 AM motivation hasn't worn off and I'm scared."]
            };
            const result = excuses[vibe][Math.floor(Math.random() * excuses[vibe].length)];
            document.getElementById('excuseResult').innerHTML = `<p class="bouncy-text">"${result}"</p>`;
        }

        function spinBunkMeter() {
            const needle = document.getElementById('needle');
            const resultBox = document.getElementById('bunkResult');
            const deg = 1800 + Math.random() * 360;
            needle.style.transform = `rotate(${deg}deg)`;
            
            setTimeout(() => {
                const decision = Math.random() > 0.5 ? "BUNK IT" : "ATTEND";
                const reason = decision === "BUNK IT" ? "The universe wants you to nap." : "Go, or your internal guilt will kill you.";
                resultBox.innerHTML = `<h3>${decision}</h3><p>${reason}</p>`;
            }, 2000);
        }

        function simulateOverthinking() {
            const input = document.getElementById('overthinkInput').value;
            if (!input) return;
            const scenarios = [
                "They actually hate you and this is the start of your social exile.",
                "They are planning a surprise party (but you weren't invited).",
                "It was a typo, but they're too embarrassed to correct it.",
                "They sent it to the wrong person and are now panicking.",
                "They are waiting for you to fail so they can laugh."
            ];
            const list = document.getElementById('overthinkResults');
            list.innerHTML = scenarios.map(s => `<p class="scenario-item">${s}</p>`).join('');
        }

        function scanPalm() {
            const line = document.querySelector('.scanner-line');
            const result = document.getElementById('scanResult');
            line.style.animation = 'scan 2s infinite';
            result.innerHTML = "<p>Analyzing digital palm lines...</p>";
            
            setTimeout(() => {
                line.style.animation = 'none';
                const luck = ["GOD LEVEL LUCK 🍀", "AVERAGE HUMAN 🤡", "ABSOLUTE DISASTER 💀"];
                result.innerHTML = `<h3>${luck[Math.floor(Math.random() * luck.length)]}</h3>`;
            }, 3000);
        }
    </script>
</body>
</html>
