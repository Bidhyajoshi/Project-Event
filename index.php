<?php
session_start();
$pdo = require 'db.php';
$isLoggedIn = isset($_SESSION['user_id']);
if ($isLoggedIn) {
    updateActivity($pdo);
}

// Get user count
$stmt = $pdo->query("SELECT COUNT(*) FROM users");
$userCount = $stmt->fetchColumn() ?: rand(42, 420);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ANTI-GRAVITY | Production</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="bg-gradient" id="bgGradient"></div>
    <div id="floatingEmojis"></div>

    <div class="live-counter">🟢 <?php echo $userCount + rand(1, 10); ?> Legends Bunking Now</div>

    <!-- ONBOARDING FLOW -->
    <?php if (!$isLoggedIn): ?>
    <div class="onboarding-overlay" id="onboardOverlay">
        <div class="glass-card onboard-step active" id="step1">
            <h1>Enter your Earth Name</h1>
            <input type="text" id="earthName" class="glass-input" placeholder="Name here...">
            <button class="glow-btn" onclick="nextStep(2)">Next ➔</button>
        </div>
        <div class="glass-card onboard-step" id="step2">
            <h1>Select your Avatar</h1>
            <div class="avatar-grid">
                <div class="avatar-card" onclick="selectAvatar('🤓')">🤓 Topper</div>
                <div class="avatar-card" onclick="selectAvatar('😎')">😎 Backbencher</div>
                <div class="avatar-card" onclick="selectAvatar('👻')">👻 Ghost</div>
            </div>
        </div>
        <div class="glass-card onboard-step" id="step3">
            <h1>Choose your Struggle</h1>
            <button class="glow-btn struggle-btn" onclick="finishOnboard(1)">Year 1 (Clueless)</button>
            <button class="glow-btn struggle-btn" onclick="finishOnboard(2)">Year 2 (Depressed)</button>
            <button class="glow-btn struggle-btn" onclick="finishOnboard(3)">Year 3 (Dead Inside)</button>
            <button class="glow-btn struggle-btn" onclick="finishOnboard(4)">Year 4 (Let me out)</button>
        </div>
    </div>
    <?php endif; ?>

    <!-- MAIN DASHBOARD -->
    <div class="dashboard">
        <header class="text-center">
            <h1 class="main-title">ANTI-GRAVITY</h1>
            <p class="subtitle">The Ultimate Interactive Playground</p>
        </header>

        <div class="grid">
            <div class="glass-card tool" onclick="openPortal('ome-portal')">
                <div class="icon">🌐</div>
                <h2>Ome-Gravity</h2>
            </div>
            <div class="glass-card tool" onclick="openPortal('ai-portal')">
                <div class="icon">🤖</div>
                <h2>Savage AI</h2>
            </div>
            <div class="glass-card tool" onclick="openPortal('bunk-portal')">
                <div class="icon">🎲</div>
                <h2>Bunk Decision</h2>
            </div>
            <div class="glass-card tool" onclick="openPortal('excuse-portal')">
                <div class="icon">🤥</div>
                <h2>Excuse Generator</h2>
            </div>
            <div class="glass-card tool" onclick="openPortal('overthink-portal')">
                <div class="icon">🧠</div>
                <h2>Overthinker 3000</h2>
            </div>
        </div>
    </div>

    <!-- PORTALS -->
    <section class="portal" id="ome-portal">
        <button class="close-btn" onclick="closePortal('ome-portal')">Back to Reality</button>
        <div class="portal-content">
            <h1>Ome-Gravity</h1>
            <div class="video-grid">
                <video id="myVid" autoplay muted playsinline class="glass-vid"></video>
                <div class="glass-vid placeholder-vid">Connecting to Stranger...</div>
            </div>
        </div>
    </section>

    <section class="portal" id="ai-portal">
        <button class="close-btn" onclick="closePortal('ai-portal')">Back to Reality</button>
        <div class="portal-content">
            <h1>Savage AI</h1>
            <div class="chat-ui">
                <div id="aiChatBox" class="chat-box glass-card"></div>
                <div class="chat-input-area">
                    <input type="text" id="aiInput" class="glass-input" placeholder="Crush? Exams? Money?">
                    <button class="glow-btn" onclick="sendAIMsg()">Send</button>
                </div>
            </div>
        </div>
    </section>

    <section class="portal" id="bunk-portal">
        <button class="close-btn" onclick="closePortal('bunk-portal')">Back to Reality</button>
        <div class="portal-content">
            <h1>Spin the Wheel</h1>
            <div class="flex-row">
                <input type="number" id="bAtt" class="glass-input mini" placeholder="Attendance %">
                <input type="number" id="bFr" class="glass-input mini" placeholder="Friends Count">
            </div>
            <div class="wheel-box">
                <div class="wheel-pointer">▼</div>
                <div class="wheel" id="bWheel"></div>
            </div>
            <button class="glow-btn" onclick="spinWheel()">SPIN TO DECIDE</button>
            <h2 id="wheelResult" class="result-text"></h2>
        </div>
    </section>

    <section class="portal" id="excuse-portal">
        <button class="close-btn" onclick="closePortal('excuse-portal')">Back to Reality</button>
        <div class="portal-content">
            <h1>Excuse Generator</h1>
            <div class="excuse-cols">
                <select id="exCat" class="glass-input select"><option value="College">College</option><option value="Date">Date</option></select>
                <select id="exVic" class="glass-input select"><option value="Professor">Professor</option><option value="Partner">Partner</option></select>
                <button class="glow-btn" onclick="brewExcuse()">Brew Lie</button>
            </div>
            <h2 id="exResult" class="result-text glass-card" style="display:none; margin-top:20px;"></h2>
        </div>
    </section>

    <section class="portal" id="overthink-portal">
        <button class="close-btn" onclick="closePortal('overthink-portal')">Back to Reality</button>
        <div class="portal-content" id="otContainer">
            <h1>Overthinker 3000</h1>
            <input type="text" id="otInput" class="glass-input" placeholder="What did they say?">
            <button class="glow-btn" onclick="startOverthink()">Start Panic</button>
            <div id="otList" class="ot-list"></div>
        </div>
    </section>

    <script src="script.js"></script>
</body>
</html>
