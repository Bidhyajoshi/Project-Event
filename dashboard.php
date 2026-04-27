<?php
session_start(); 
if(!isset($_SESSION['user_id'])) { 
    header('Location: index.php'); 
    exit(); 
}
$pdo = require 'db.php';
updateActivity($pdo);

// Get user count
$stmt = $pdo->query("SELECT COUNT(*) FROM users");
$userCount = $stmt->fetchColumn() ?: rand(42, 420);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BAWAAL | Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <!-- SimplePeer CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/simple-peer/9.11.1/simplepeer.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
</head>
<body>
    <div class="bg-gradient" id="bgGradient"></div>
    <div id="floatingEmojis"></div>

    <div class="live-counter">🟢 <?php echo $userCount + rand(1, 10); ?> Legends Bunking Now</div>

    <!-- MAIN DASHBOARD -->
    <div class="dashboard">
        <header class="text-center" style="position: relative;">
            <a href="logout.php" class="close-btn" style="top: 0; right: 0; font-size: 1rem; text-decoration: none; padding: 5px 15px;">Log Out</a>
            <h1 class="main-title bawaal-glitch" data-text="BAWAAL">BAWAAL</h1>
            <p class="subtitle">BAWAAL: Create Chaos, Not Assignments</p>
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
        <div class="portal-content" style="max-width: 1200px;">
            <h1 style="margin-bottom:10px;">Ome-Gravity</h1>
            <div id="ome-error" style="display:none; font-size: 2rem; color: #ff007f; margin-bottom:20px;">Don't be shy, show your beautiful face! Allow Camera Access.</div>
            
            <div class="video-grid" style="display: none;" id="omeVideoGrid">
                <div class="video-wrapper">
                    <div class="vid-label">Stranger</div>
                    <div id="radarPulse" class="radar-pulse"></div>
                    <video id="remoteVideo" autoplay playsinline></video>
                    <div class="chat-overlay">
                        <div id="omeChatBox" class="ome-chat-box"></div>
                        <input type="text" id="omeChatInput" placeholder="Type a message..." class="glass-input" style="margin-bottom:0;" onkeypress="handleOmeChat(event)">
                    </div>
                </div>
                <div class="video-wrapper">
                    <div class="vid-label">You</div>
                    <video id="localVideo" autoplay muted playsinline></video>
                    <div class="video-controls">
                        <button onclick="toggleMute()" class="control-btn" id="muteBtn">🎤</button>
                        <button onclick="toggleVideo()" class="control-btn" id="vidBtn">📷</button>
                    </div>
                </div>
            </div>
            <button class="glow-btn" id="nextBtn" onclick="nextOmePartner()" style="display:none; margin-top:30px;">SKIP / NEXT</button>
        </div>
    </section>

    <section class="portal ai-neon-theme" id="ai-portal">
        <div id="aiParticles" class="ai-particles"></div>
        <button class="close-btn neon-btn" onclick="closePortal('ai-portal')">Back to Reality</button>
        <div class="portal-content ai-layout">
            <div class="ai-main">
                <h1 class="ai-title">Savage AI <button class="neon-btn" style="font-size: 1rem; margin-left: 20px; z-index: 1000000; position:relative;" onclick="clearAIChat()">🗑️ Clear Chaos</button></h1>
                <div class="ai-modes flex-row neon-glass">
                    <label class="neon-toggle"><input type="radio" name="aiMode" value="savage" checked> Savage</label>
                    <label class="neon-toggle"><input type="radio" name="aiMode" value="damage"> Damage</label>
                    <label class="neon-toggle"><input type="radio" name="aiMode" value="brainless"> Brainless</label>
                </div>
                <div class="chat-ui">
                    <div id="aiChatBox" class="chat-box neon-glass"></div>
                    <div class="chat-input-area neon-input-area">
                        <input type="text" id="aiInput" class="neon-input" placeholder="Crush? Exams? Money?">
                        <button class="arcade-btn" onclick="sendAIMsg()">SEND</button>
                    </div>
                </div>
                <div id="memeSticker" class="meme-sticker"></div>
            </div>
            <div class="burn-meter-container neon-glass">
                <h3>BURN METER</h3>
                <div class="lava-tube">
                    <div class="lava-fill" id="burnFill">
                        <div class="bubble b1"></div>
                        <div class="bubble b2"></div>
                        <div class="bubble b3"></div>
                    </div>
                </div>
                <div id="burnText" class="burn-text glitch-text" data-text="0% Roast">0% Roast</div>
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
