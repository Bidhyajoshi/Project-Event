<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GATEWAY | Anti-Gravity Character Creator</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="gate.css">
</head>
<body>
    <div class="bg-vibe"></div>
    
    <div class="portal-container">
        <!-- Progress Bar -->
        <div class="progress-wrapper">
            <div class="progress-bar" id="progressBar"></div>
            <p class="progress-caption" id="progressCaption">Scanning for sanity... 0%</p>
        </div>

        <div class="glass-portal">
            <!-- Step 1: Call Sign -->
            <div class="step-container active" id="step1">
                <h1 class="step-title">The Call Sign</h1>
                <p class="step-desc">What's your legendary name, backbencher?</p>
                <div class="input-group">
                    <input type="text" id="username" class="gate-input" placeholder="Enter your alias..." autocomplete="off">
                    <button class="gate-btn" onclick="goToStep(2)">Proceed to Destiny ➔</button>
                </div>
            </div>

            <!-- Step 2: Survival Tool -->
            <div class="step-container" id="step2">
                <h1 class="step-title">Survival Tool</h1>
                <p class="step-desc">Choose your weapon for the semester</p>
                <div class="visual-card-grid">
                    <div class="snap-card" onclick="selectTool('☕', 'Coffee Cup', this)">
                        <div class="snap-emoji">☕</div>
                        <div class="snap-label">Coffee Cup</div>
                    </div>
                    <div class="snap-card" onclick="selectTool('📝', 'Last Minute Notes', this)">
                        <div class="snap-emoji">📝</div>
                        <div class="snap-label">Last Minute Notes</div>
                    </div>
                    <div class="snap-card" onclick="selectTool('🔥', 'Burn Motivation', this)">
                        <div class="snap-emoji">🔥</div>
                        <div class="snap-label">Burn Motivation</div>
                    </div>
                    <div class="snap-card" onclick="selectTool('🍀', 'Pure Luck', this)">
                        <div class="snap-emoji">🍀</div>
                        <div class="snap-label">Pure Luck</div>
                    </div>
                </div>
            </div>

            <!-- Step 3: Earthly Identity -->
            <div class="step-container" id="step3">
                <h1 class="step-title">Earthly Identity</h1>
                <p class="step-desc">How will history remember you?</p>
                <div class="visual-card-grid">
                    <div class="snap-card" onclick="finishCharacter('👑', 'Backbench King', this)">
                        <div class="snap-emoji">👑</div>
                        <div class="snap-label">Backbench King</div>
                    </div>
                    <div class="snap-card" onclick="finishCharacter('👩‍🎓', 'Frontbench Queen', this)">
                        <div class="snap-emoji">👩‍🎓</div>
                        <div class="snap-label">Frontbench Queen</div>
                    </div>
                    <div class="snap-card" onclick="finishCharacter('👽', 'Agent of Chaos', this)">
                        <div class="snap-emoji">👽</div>
                        <div class="snap-label">Agent of Chaos</div>
                    </div>
                    <div class="snap-card" onclick="finishCharacter('✨', 'Vibe Provider', this)">
                        <div class="snap-emoji">✨</div>
                        <div class="snap-label">Vibe Provider</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="gate_script.js"></script>
</body>
</html>
