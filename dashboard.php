<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
$pdo = require 'db.php';
updateActivity($pdo);

// Get user count for live counter
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
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <!-- SimplePeer CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/simple-peer/9.11.1/simplepeer.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <style>
        .top-bar { display: flex; justify-content: space-between; align-items: center; padding: 20px 40px; position: fixed; top: 0; left: 0; width: 100%; z-index: 100; background: rgba(0,0,0,0.5); backdrop-filter: blur(10px); border-bottom: 1px solid rgba(255,255,255,0.1); }
        .bawaal-mini { font-size: 2.5rem; margin: 0; animation: none; text-shadow: 0 0 10px #ff00ff; }
        .dash-container { padding-top: 120px; display: flex; flex-direction: column; align-items: center; min-height: 100vh; }
        .logout-btn { background: #ff0040; color: #fff; text-decoration: none; padding: 10px 20px; border-radius: 20px; font-size: 1.2rem; font-weight: bold; box-shadow: 0 0 10px #ff0040; transition: 0.3s; }
        .logout-btn:hover { background: #fff; color: #ff0040; }
        .user-info { display: flex; align-items: center; gap: 15px; }
        .welcome-back { font-size: 1.5rem; color: #00ffff; text-shadow: 0 0 5px #00ffff; }
        .meme-video-container { position: relative; width: 100%; max-width: 600px; margin: 0 auto; border-radius: 20px; overflow: hidden; border: 5px solid #ff00ff; }
        .meme-video-container video { width: 100%; display: block; }
        .filter-btn { margin: 5px; }
    </style>
</head>
<body>
    <div class="bg-gradient" id="bgGradient"></div>
    <div id="floatingEmojis"></div>

    <div class="top-bar">
        <h1 class="bawaal-glitch bawaal-mini" data-text="BAWAAL">BAWAAL</h1>
        <div class="user-info">
            <span class="welcome-back">Sup, <?php echo htmlspecialchars($_SESSION['username']); ?>?</span>
            <div class="live-counter" style="position:static; padding:5px 15px;">🟢 <?php echo $userCount + rand(1, 10); ?> Online</div>
            <a href="logout.php" class="logout-btn">Log Out</a>
        </div>
    </div>

    <div class="dash-container">
        <h2 class="subtitle" style="margin-bottom: 50px;">Choose Your Chaos</h2>
        <div class="grid">
            <div class="glass-card tool" onclick="openPortal('ai-portal')" style="cursor: pointer !important; pointer-events: auto !important;">
                <div class="icon">🤖</div>
                <h2>Savage AI</h2>
                <p style="margin-top:10px; font-size:1rem; opacity:0.8;">Get roasted brutally.</p>
            </div>
            <div class="glass-card tool" onclick="openPortal('ome-portal')" style="cursor: pointer !important; pointer-events: auto !important;">
                <div class="icon">🌐</div>
                <h2>Ome-Gravity</h2>
                <p style="margin-top:10px; font-size:1rem; opacity:0.8;">Live Stranger Chat.</p>
            </div>
            <div class="glass-card tool" onclick="openPortal('bunk-portal')" style="cursor: pointer !important; pointer-events: auto !important;">
                <div class="icon">🎲</div>
                <h2>Bunk Decision</h2>
                <p style="margin-top:10px; font-size:1rem; opacity:0.8;">To bunk or not to bunk?</p>
            </div>
            <div class="glass-card tool" onclick="openPortal('excuse-portal')" style="cursor: pointer !important; pointer-events: auto !important;">
                <div class="icon">🤥</div>
                <h2>Excuse Generator</h2>
                <p style="margin-top:10px; font-size:1rem; opacity:0.8;">Smart lies for dumb situations.</p>
            </div>
            <div class="glass-card tool" onclick="openPortal('meme-portal')" style="cursor: pointer !important; pointer-events: auto !important;">
                <div class="icon">🎭</div>
                <h2>Meme Camera</h2>
                <p style="margin-top:10px; font-size:1rem; opacity:0.8;">Live funny filters.</p>
            </div>
            <div class="glass-card tool" onclick="openPortal('overthink-portal')" style="cursor: pointer !important; pointer-events: auto !important;">
                <div class="icon">🧠</div>
                <h2>Overthinker 3000</h2>
                <p style="margin-top:10px; font-size:1rem; opacity:0.8;">Simulate endless anxiety.</p>
            </div>
            <div class="glass-card tool" onclick="openPortal('palm-portal')" style="cursor: pointer !important; pointer-events: auto !important;">
                <div class="icon">🔮</div>
                <h2>Palm Prediction</h2>
                <p style="margin-top:10px; font-size:1rem; opacity:0.8;">Discover your savage destiny.</p>
            </div>
        </div>
    </div>

    <!-- PORTALS -->
    
    <!-- Savage AI Portal -->
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

    <!-- Ome-Gravity Portal -->
    <section class="portal" id="ome-portal">
        <button class="close-btn" onclick="closePortal('ome-portal')">Back to Reality</button>
        <div class="portal-content" style="max-width: 1200px;">
            <h1 class="ai-title" style="margin-bottom:10px;">Ome-Gravity</h1>
            <div id="ome-error" style="display:none; font-size: 2rem; color: #ff007f; margin-bottom:20px;">Don't be shy, allow Camera Access!</div>
            
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

    <!-- Meme Camera Portal -->
    <section class="portal" id="meme-portal" style="background:#000 !important; padding:0;">
        <button class="close-btn" onclick="closePortal('meme-portal')" style="z-index:1000001; background:rgba(0,0,0,0.5); color:#fff;">Back to Reality</button>
        
        <div class="snap-container">
            <video id="meme-video" autoplay muted playsinline style="width:100%; height:100%; object-fit:cover; transition: filter 0.3s;"></video>
            
            <div class="snap-ui-bottom" style="background: linear-gradient(transparent, rgba(0,0,0,0.8));">
                <div class="filter-carousel" id="filterCarousel" style="display:flex; overflow-x:auto; gap:20px; padding:20px; width:100%; scrollbar-width:none; -ms-overflow-style: none;">
                    <!-- Filter circles with labels generated by JS -->
                </div>
                
                <div style="display:flex; justify-content:center; align-items:center; margin-bottom:20px;">
                    <button class="capture-btn" onclick="captureMemeSnapshot()" style="width:80px; height:80px; border-radius:50%; border:5px solid #fff; background:rgba(255,255,255,0.3); cursor:pointer; box-shadow:0 0 20px rgba(255,255,255,0.5);"></button>
                </div>
            </div>
            
            <!-- Snapshot Preview Overlay -->
            <div id="snapPopup" style="display:none; position:absolute; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.9); z-index:1005; flex-direction:column; align-items:center; justify-content:center; padding:20px;">
                <h2 style="color:#fff; font-family:'Fredoka One'; font-size:2rem; margin-bottom:20px;">Keep this masterpiece?</h2>
                <img id="snapPreview" style="max-width:90%; max-height:60%; border-radius:15px; border:3px solid #ff00ff; box-shadow:0 0 30px #ff00ff; margin-bottom:30px;">
                <div style="display:flex; gap:20px; width:100%; max-width:400px;">
                    <button class="glow-btn" style="flex:1; background:#00ff00; color:#000;" onclick="saveMemeToGallery()">✅ SAVE</button>
                    <button class="glow-btn" style="flex:1; background:#ff0000; color:#fff;" onclick="discardMemeSnapshot()">❌ DISCARD</button>
                </div>
                <a id="hiddenSaveLink" style="display:none;"></a>
            </div>
            
            <canvas id="memeCanvas" style="display:none;"></canvas>
            
            <!-- Flash Effect -->
            <div id="camFlash" style="display:none; position:absolute; top:0; left:0; width:100%; height:100%; background:#fff; z-index:1000;"></div>
        </div>
        
        <!-- Embedded SVG Filters for complex effects -->
        <svg style="width:0; height:0; position:absolute;">
            <filter id="thermal"><feColorMatrix type="matrix" values="0.33 0.33 0.33 0 0  0.33 0.33 0.33 0 0  0.33 0.33 0.33 0 0  0 0 0 1 0" /><feComponentTransfer><feFuncR type="table" tableValues="0 0 1 1 1"/><feFuncG type="table" tableValues="0 0 0 1 1"/><feFuncB type="table" tableValues="1 0 0 0 0"/></feComponentTransfer></filter>
            <filter id="pixelate"><feGaussianBlur stdDeviation="5" result="blur" /><feComposite in="SourceGraphic" in2="blur" operator="over" /></filter>
            <filter id="neon"><feMorphology operator="dilate" radius="2" in="SourceAlpha" result="darkness"/><feFlood flood-color="magenta"/><feComposite operator="in" in2="darkness"/><feComposite operator="over" in2="SourceGraphic"/></filter>
        </svg>
    </section>

    <!-- Bunk Portal -->
    <section class="portal ai-neon-theme" id="bunk-portal" style="background: #050505 !important;">
        <button class="close-btn neon-btn" onclick="closePortal('bunk-portal')" style="z-index: 1000000 !important; position:absolute; top:20px; right:20px;">Back to Reality</button>
        
        <div class="portal-content" style="display:flex; flex-direction:column; align-items:center; justify-content:center; height:100vh; text-align:center; max-width: 800px; margin: 0 auto;">
            
            <div id="bunkStep1" class="bunk-step active" style="display:flex; flex-direction:column; gap:20px; width:100%;">
                <h1 class="ai-title" style="font-size:3rem; color:#fff;">How important is this lecture?</h1>
                <button class="arcade-btn" onclick="nextBunkStep(1, 'Life or Death')" style="padding: 15px;">Life or Death</button>
                <button class="arcade-btn" onclick="nextBunkStep(1, 'Mid')" style="padding: 15px;">Mid</button>
                <button class="arcade-btn" onclick="nextBunkStep(1, 'Who is the Prof?')" style="padding: 15px;">I don't even know the Prof's name</button>
            </div>

            <div id="bunkStep2" class="bunk-step" style="display:none; flex-direction:column; gap:20px; width:100%;">
                <h1 class="ai-title" style="font-size:3rem; color:#fff;">How many homies are coming?</h1>
                <input type="number" id="bunkFriends" class="neon-input" placeholder="Squad count..." style="font-size:2rem; text-align:center;">
                <button class="glow-btn" onclick="nextBunkStep(2)">Next</button>
            </div>

            <div id="bunkStep3" class="bunk-step" style="display:none; flex-direction:column; gap:20px; width:100%;">
                <h1 class="ai-title" style="font-size:3rem; color:#fff;">Current Attendance %?</h1>
                <h2 id="bunkAttVal" style="color:#00ffff; font-size:4rem; margin:0; text-shadow: 0 0 10px #00ffff;">50%</h2>
                <input type="range" id="bunkAtt" min="0" max="100" value="50" class="glass-slider" oninput="document.getElementById('bunkAttVal').innerText = this.value + '%'">
                <button class="glow-btn" onclick="calculateBunkDestiny()" style="margin-top:30px; padding: 20px 40px; font-size:2rem;">Decide My Fate</button>
            </div>

            <div id="bunkLoading" style="display:none; flex-direction:column; align-items:center; width:100%;">
                <h1 class="bawaal-glitch" data-text="Calculating Destiny..." style="font-size: 3rem;">Calculating Destiny...</h1>
                <div class="loading-bar-container" style="width:100%; max-width:400px; height:20px; border: 2px solid #00ffff;"><div class="loading-bar" id="bunkBar" style="background:#00ffff; height:100%; width:0%; transition: width 1.5s ease-out;"></div></div>
            </div>

            <div id="bunkResult" style="display:none; flex-direction:column; align-items:center; gap:20px; width:100%;">
                <h1 class="ai-title" id="bunkVerdict" style="font-size:5rem; text-shadow: 0 0 20px #ff00ff; margin-bottom: 0;"></h1>
                <p id="bunkReason" style="font-size:2rem; color:#fff; line-height: 1.4;"></p>
                <button class="glow-btn" onclick="resetBunk()" style="margin-top: 20px;">Play Again</button>
            </div>

        </div>
    </section>

    <!-- Excuse Portal -->
    <section class="portal ai-neon-theme" id="excuse-portal" style="background: #0d0d1a !important;">
        <button class="close-btn neon-btn" onclick="closePortal('excuse-portal')" style="z-index: 1000000; position:absolute; top:20px; right:20px;">Back to Reality</button>
        <div class="portal-content" style="display:flex; flex-direction:column; align-items:center; justify-content:center; height:100vh; text-align:center; max-width: 800px; margin: 0 auto;">
            
            <div id="exInputView" style="display:flex; flex-direction:column; width:100%; gap:25px; align-items:center;">
                <h1 class="ai-title" style="font-size:3.5rem; color:#fff; text-shadow:0 0 20px #ff00ff; margin-bottom:20px;">Master of Lies</h1>
                
                <select id="exTarget" class="neon-input" style="font-size:1.5rem; padding:15px; width:100%; max-width:500px; text-align:center; appearance:none;">
                    <option value="" disabled selected>Who is the Victim? (Target)</option>
                    <option value="Professor">Professor</option>
                    <option value="HOD">HOD</option>
                    <option value="Parents">Parents</option>
                    <option value="Crush">Crush</option>
                    <option value="Boss">Boss</option>
                </select>
                
                <select id="exSituation" class="neon-input" style="font-size:1.5rem; padding:15px; width:100%; max-width:500px; text-align:center; appearance:none;">
                    <option value="" disabled selected>What did you do? (Situation)</option>
                    <option value="Late">Late for class/work</option>
                    <option value="Assignment">Assignment not done</option>
                    <option value="Bunked">Bunked yesterday</option>
                    <option value="ForgotBirthday">Forgot birthday/anniversary</option>
                </select>
                
                <select id="exVibe" class="neon-input" style="font-size:1.5rem; padding:15px; width:100%; max-width:500px; text-align:center; appearance:none;">
                    <option value="" disabled selected>Choose your Flavor (Vibe)</option>
                    <option value="Emotional">Emotional (Tears)</option>
                    <option value="Savage">Savage (No fear)</option>
                    <option value="Professional">Professional (Corporate BS)</option>
                    <option value="Confused">Totally Confused</option>
                </select>
                
                <button class="glow-btn" style="padding: 20px 50px; font-size: 2rem; margin-top:20px; font-family:'Fredoka One', cursive;" onclick="brewExcuse()">🔮 BREW LIE</button>
            </div>
            
            <div id="exBrewingView" style="display:none; flex-direction:column; align-items:center;">
                <div style="font-size:6rem; animation:shake 0.5s infinite;">🍲</div>
                <h2 class="bawaal-glitch" data-text="COOKING THE PERFECT LIE..." style="color:#00ffff; font-size:2rem; margin-top:20px;">COOKING THE PERFECT LIE...</h2>
            </div>
            
            <div id="exResultView" style="display:none; flex-direction:column; align-items:center; width:100%;">
                <h1 style="color:#00ff00; font-family:'Fredoka One'; font-size:3rem; margin-bottom:20px;">Lie Successfully Cooked!</h1>
                <div id="exResultText" style="background:rgba(255,255,255,0.05); border:2px solid #00ff00; border-radius:15px; padding:30px; font-size:2rem; color:#fff; width:100%; line-height:1.4; position:relative;">
                    <!-- Result goes here -->
                </div>
                <div style="display:flex; gap:20px; margin-top:30px;">
                    <button class="glow-btn" style="background:#25D366; color:#fff;" onclick="copyExcuse()">📋 Copy to WhatsApp</button>
                    <button class="arcade-btn" onclick="resetExcuse()">Cook Another</button>
                </div>
            </div>

        </div>
    </section>

    <!-- Overthinker Portal -->
    <section class="portal" id="overthink-portal">
        <button class="close-btn" onclick="closePortal('overthink-portal')">Back to Reality</button>
        <div class="portal-content" id="otContainer" style="display:flex; flex-direction:column; align-items:center; justify-content:center; height:100vh; max-width:600px; margin:0 auto; text-align:center;">
            <h1 class="ai-title bawaal-glitch" data-text="Overthinker 3000" style="font-size:3.5rem;">Overthinker 3000</h1>
            <p style="color:#00ffff; font-size:1.2rem; margin-bottom:30px; opacity:0.8;">The Simulation of Endless Anxiety</p>
            
            <input type="text" id="otInput" class="neon-input" placeholder="What did they text or say to you?" style="width:100%; font-size:1.5rem; text-align:center; margin-bottom:20px;">
            <button class="glow-btn" style="width: 100%; background:#ff0000; color:#fff; border:none; padding:20px; font-size:1.5rem; cursor:pointer !important; pointer-events:auto !important;" onclick="startPanicAttack()">⚠️ START PANIC ATTACK</button>
            
            <div id="otList" class="ot-list" style="margin-top:30px; width:100%; height:300px; overflow-y:auto; display:flex; flex-direction:column; gap:15px; scrollbar-width:none;">
                <!-- Scenarios will pop up here with shaky animation -->
            </div>
        </div>
    </section>

    <!-- Palm Prediction Portal -->
    <section class="portal mystic-bg" id="palm-portal" style="background: radial-gradient(circle at center, #3a0ca3, #050505); font-family: 'Baloo 2', cursive;">
        <button class="close-btn glow-btn" onclick="closePortal('palm-portal')" style="z-index: 1000000; position:absolute; top:20px; right:20px; border-radius:30px; font-family:'Fredoka One', cursive;">Back to Reality</button>
        <div class="portal-content" style="display:flex; flex-direction:column; align-items:center; justify-content:center; height:100vh; text-align:center;">
            
            <!-- Upload View -->
            <div id="palmUploadView" style="display:flex; flex-direction:column; align-items:center;">
                <h1 style="font-size:3rem; color:#f72585; text-shadow:0 0 20px #f72585; font-family:'Fredoka One', cursive; margin-bottom:10px;">Mystic Palm Scanner</h1>
                <p style="font-size:1.5rem; color:#4cc9f0; margin-bottom:40px;">Let the universe judge your poor life choices.</p>
                
                <div class="hand-outline-container" style="position:relative; width:300px; height:400px; border:3px dashed #4cc9f0; border-radius:20px; display:flex; justify-content:center; align-items:center; overflow:hidden; box-shadow:inset 0 0 30px rgba(76,201,240,0.3);">
                    <!-- Hand outline moved INSIDE camera container -->
                    <div id="handPlaceholder" style="font-size:8rem; opacity:0.3; animation:pulse-glow 2s infinite; position:relative; z-index:6; pointer-events:none;">✋</div>
                    
                    <!-- Video for live capture -->
                    <video id="palm-video" autoplay muted playsinline style="display:none; width:100%; height:100%; object-fit:cover; position:absolute; top:0; left:0; z-index:4;"></video>
                    
                    <img id="palmImagePreview" src="" style="display:none; width:100%; height:100%; object-fit:cover; z-index:5;">
                    <div id="scannerLine" class="scanner-line" style="display:none; position:absolute; top:0; left:0; width:100%; height:5px; background:#f72585; box-shadow:0 0 15px #f72585; z-index:10;"></div>
                </div>
                
                <!-- UPDATED BUTTONS -->
                <div id="palm-auth-methods" style="margin-top:30px; display:flex; gap:15px; flex-direction:column; align-items:center;">
                    <div style="position:relative;">
                        <input type="file" id="palmInput" accept="image/*" style="opacity:0; position:absolute; top:0; left:0; width:100%; height:100%; cursor:pointer; z-index:20; pointer-events:auto;" onchange="startPalmScan(event)">
                        <button class="glow-btn" style="background:#4cc9f0; font-family:'Fredoka One'; padding:15px 30px; font-size:1.2rem; color:#000; pointer-events:none;">🖼️ UPLOAD FROM GALLERY</button>
                    </div>
                    
                    <button class="palm-btn camera-access" style="cursor: pointer !important; pointer-events: auto !important; position: relative; z-index: 10000 !important; display: block; background:#f72585; font-family:'Fredoka One'; padding:15px 30px; font-size:1.2rem; color:#fff;" onclick="activatePalmCamera()">📷 TAKE LIVE PHOTO</button>
                </div>
                
                <button class="arcade-btn" id="capturePalmBtn" style="display:none; background:#00ff00; color:#000; font-family:'Fredoka One'; padding:20px 40px; font-size:1.5rem; margin-top:20px; z-index:100; pointer-events:auto;" onclick="capturePalmPhoto()">📸 SNAP HAND</button>
                
                <!-- Hidden Canvas for palm capture -->
                <canvas id="palmHiddenCanvas" style="display:none;"></canvas>
            </div>

            <!-- Scanning Animation View -->
            <div id="palmScanningView" style="display:none; flex-direction:column; align-items:center;">
                <div class="mystic-spinner" style="font-size:5rem; animation:spin 3s linear infinite;">🔯</div>
                <h2 style="color:#4cc9f0; font-size:2rem; margin-top:20px; animation:blink 1s infinite;">CALCULATING YOUR DESTINY...<br><span style="font-size:1.2rem; color:#f72585;">(AND YOUR EXCUSES)</span></h2>
            </div>

            <!-- Results View -->
            <div id="palmResultsView" style="display:none; flex-direction:column; align-items:center; width:100%; max-width:800px; gap:20px;">
                <h1 style="font-size:3rem; color:#f72585; text-shadow:0 0 20px #f72585; font-family:'Fredoka One', cursive;">The Universe Has Spoken.</h1>
                
                <div class="savage-grid" style="display:grid; grid-template-columns: 1fr 1fr; gap:20px; width:100%; text-align:left;">
                    <div class="mystic-card fade-in" id="resFuture" style="background:rgba(255,255,255,0.05); padding:20px; border-radius:15px; border-left:5px solid #4cc9f0; animation-delay:0.2s;">
                        <h3 style="color:#4cc9f0; font-size:1.2rem; margin:0 0 10px 0;">🔮 FUTURE</h3>
                        <p id="txtFuture" style="font-size:1.3rem; color:#fff; margin:0; line-height:1.4;"></p>
                    </div>
                    <div class="mystic-card fade-in" id="resCareer" style="background:rgba(255,255,255,0.05); padding:20px; border-radius:15px; border-left:5px solid #f72585; animation-delay:0.8s;">
                        <h3 style="color:#f72585; font-size:1.2rem; margin:0 0 10px 0;">💼 CAREER</h3>
                        <p id="txtCareer" style="font-size:1.3rem; color:#fff; margin:0; line-height:1.4;"></p>
                    </div>
                    <div class="mystic-card fade-in" id="resStudy" style="background:rgba(255,255,255,0.05); padding:20px; border-radius:15px; border-left:5px solid #b5179e; animation-delay:1.4s;">
                        <h3 style="color:#b5179e; font-size:1.2rem; margin:0 0 10px 0;">📚 STUDY</h3>
                        <p id="txtStudy" style="font-size:1.3rem; color:#fff; margin:0; line-height:1.4;"></p>
                    </div>
                    <div class="mystic-card fade-in" id="resLove" style="background:rgba(255,255,255,0.05); padding:20px; border-radius:15px; border-left:5px solid #7209b7; animation-delay:2.0s;">
                        <h3 style="color:#7209b7; font-size:1.2rem; margin:0 0 10px 0;">❤️ LOVE LIFE</h3>
                        <p id="txtLove" style="font-size:1.3rem; color:#fff; margin:0; line-height:1.4;"></p>
                    </div>
                    <div class="mystic-card fade-in" id="resWealth" style="background:rgba(255,255,255,0.05); padding:20px; border-radius:15px; border-left:5px solid #3f37c9; animation-delay:2.6s;">
                        <h3 style="color:#3f37c9; font-size:1.2rem; margin:0 0 10px 0;">💰 WEALTH</h3>
                        <p id="txtWealth" style="font-size:1.3rem; color:#fff; margin:0; line-height:1.4;"></p>
                    </div>
                </div>
                
                <button class="glow-btn fade-in" onclick="resetPalm()" style="margin-top:30px; font-family:'Fredoka One'; background:#4cc9f0; color:#000; animation-delay:2.5s;">READ AGAIN</button>
            </div>
        </div>
    </section>

    <script>
        console.log('BAWAAL ABSOLUTE INLINE SCRIPT LOADED');
        
        window.openPortal = function(id) {
            console.log('Opening Portal: ' + id);
            var el = document.getElementById(id);
            if(el) {
                el.classList.add('active');
                document.body.classList.add('no-scroll');
                // Auto-start features
                if(id === 'ome-portal') startOmeCamera();
                if(id === 'meme-portal') startMemeCamera();
                if(id === 'ai-portal') fetchAIChatHistory();
            }
        };

        window.closePortal = function(id) {
            var el = document.getElementById(id);
            if(el) {
                el.classList.remove('active');
                document.body.classList.remove('no-scroll');
                // Cleanup
                if(id === 'ome-portal' && typeof localStream !== 'undefined' && localStream) {
                    localStream.getTracks().forEach(t => t.stop());
                }
                if(id === 'meme-portal' && typeof memeStream !== 'undefined' && memeStream) {
                    memeStream.getTracks().forEach(t => t.stop());
                }
                if(id === 'overthink-portal') {
                    document.getElementById('otList').innerHTML = '';
                    document.getElementById('otContainer').style.animation = 'none';
                }
            }
        };

        // DOM Content Loaded - Floating Emojis
        document.addEventListener('DOMContentLoaded', () => {
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

        // MASSIVE SARCASTIC PREDICTION DATABASE
        const palmFuture = [
            "You will find 500 rupees in an old jeans, then lose it in 5 minutes.",
            "You will become a meme legend... as the 'Before' picture.",
            "You will finally understand 1+1, but only after failing the exam.",
            "Success is coming... it just missed the bus. Wait 40 years.",
            "You will become famous on TikTok for a video you didn't want posted.",
            "Your Wi-Fi will be fast, but only when you have no work to do.",
            "You will conquer the world... in a dream you'll forget by 8 AM.",
            "A random cat will decide you are its servant. Accept your fate.",
            "You will invent a new excuse so good, even you will believe it.",
            "Your crypto will go to the moon... right after you sell it all.",
            "You will find your car keys in the fridge. Why? Nobody knows.",
            "You will win the lottery, but the ticket will be from 1994.",
            "You will meet the person of your dreams. They will ask for directions to your ex's house.",
            "You will be the lead role in a movie that never gets released.",
            "Your biography will be titled 'Well, That Happened.'",
            "You will discover a new planet, but it will be made of homework."
        ];
        const palmLove = [
            "Your crush will like your photo... because their screen was oily.",
            "Single life is your destiny. Your cat is your only true love.",
            "A wedding is coming... and you're the one serving the snacks.",
            "You will fall in love with a pizza. It won't leave you.",
            "Someone is admiring you from afar. Keep them there. Afar.",
            "You and your bed: A toxic relationship you can't quit.",
            "Your soulmate is currently blocking you on all platforms.",
            "You will get a text back... once they need a favor.",
            "Love is blind. In your case, it's also legally deaf and mute.",
            "You will marry your phone. The honeymoon is just scrolling.",
            "Your ex will call you to ask for their Netflix password.",
            "You will have 12 kids. All of them will be succulents.",
            "You will find true love at a bus stop. They will be a bus.",
            "Your crush's name starts with 'N'... as in 'No Chance.'",
            "You will go on a date with destiny. Destiny will split the bill."
        ];
        const palmCareer = [
            "CEO of a company that sells air. You'll go bankrupt.",
            "Professional Overthinker. Salary: Paid in anxiety.",
            "You will get a promotion... to 'Unpaid Intern Plus.'",
            "Future owner of a tea stall that only serves lukewarm water.",
            "You will change jobs faster than you change your socks.",
            "Your boss will laugh at your joke... then fire you for it.",
            "You will be paid in 'exposure' and 'good vibes.'",
            "Master of the 'I was on mute' technique. It's your only skill.",
            "You will retire at 30... because your company folded at 29.",
            "Your LinkedIn profile is a work of fiction. Write a novel.",
            "You will be a billionaire... in Zimbabwe dollars.",
            "Your dream job exists, but it requires 200 years of experience.",
            "You will be the first person fired by an AI bot.",
            "You will invent a 'Smart Nap' device. You are the only user.",
            "Your career path is a circle. You are currently at the bottom."
        ];
        const palmStudy = [
            "Your exams will be like your crush—they won't even look at you.",
            "You will open the PDF, stare at it, and become a philosopher instead.",
            "Top of the class! (In a class of one, and you're second).",
            "You will invent a new math theory: 1 Study Minute = 4 Hours of YouTube.",
            "Your brain has 99 tabs open, and all are playing 'Baby Shark.'",
            "You will study for 5 minutes and reward yourself with a 3-day vacation.",
            "The syllabus is written in a language you haven't discovered yet.",
            "You will magically remember the answer... 2 minutes after submitting.",
            "Ctrl+C and Ctrl+V will be the only reasons you graduate.",
            "You will pass by the grace of a teacher who is retiring and doesn't care.",
            "Your degree will be useful... as a very expensive coaster.",
            "You will spend more time choosing a playlist than actually studying.",
            "You will become an expert in 'Procrastination Science.'",
            "Your pens will all run out of ink the second the exam starts.",
            "You will graduate with honors... in Minecraft Architecture."
        ];
        const palmWealth = [
            "You will be a billionaire... in Monopoly money.",
            "Your bank balance is like your social life: Zero.",
            "You will find a coin on the road. Don't spend it all at once.",
            "Wealth is coming... but it's currently stuck in traffic.",
            "You will win a free coupon for a gym you'll never visit.",
            "Your wallet will be full... of old receipts and regret.",
            "You will become rich by selling your bad advice.",
            "You'll find a gold mine, but it'll be a chocolate coin factory.",
            "You will be paid in 'exposure' for the next 10 years.",
            "Your investment in dogecoin will finally pay off (it won't).",
            "You will find a hidden treasure... in your laundry.",
            "You will be the richest person in your graveyard.",
            "Your net worth will be equal to the number of friends you have (Ouch).",
            "You will inherit a massive fortune... of debt.",
            "You will find a dollar bill in the dryer. Big day for you."
        ];

        // Palm Prediction Logic
        let palmStream = null;
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
                .catch(err => alert("Camera access denied!"));
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
            setTimeout(() => {
                document.getElementById('palmUploadView').style.display = 'none';
                document.getElementById('palmScanningView').style.display = 'flex';
                setTimeout(() => {
                    document.getElementById('palmScanningView').style.display = 'none';
                    document.getElementById('palmResultsView').style.display = 'flex';
                    
                    // Injecting actual text with Math.random()
                    document.getElementById('txtFuture').innerText = palmFuture[Math.floor(Math.random() * palmFuture.length)];
                    document.getElementById('txtCareer').innerText = palmCareer[Math.floor(Math.random() * palmCareer.length)];
                    document.getElementById('txtStudy').innerText = palmStudy[Math.floor(Math.random() * palmStudy.length)];
                    document.getElementById('txtLove').innerText = palmLove[Math.floor(Math.random() * palmLove.length)];
                    document.getElementById('txtWealth').innerText = palmWealth[Math.floor(Math.random() * palmWealth.length)];
                }, 3000);
            }, 1500);
        }

        window.resetPalm = () => {
            document.getElementById('palmResultsView').style.display = 'none';
            document.getElementById('palmUploadView').style.display = 'flex';
            document.getElementById('palmImagePreview').style.display = 'none';
            document.getElementById('palm-video').style.display = 'none';
            document.getElementById('handPlaceholder').style.display = 'block';
            document.getElementById('scannerLine').style.display = 'none';
            document.getElementById('palm-auth-methods').style.display = 'flex';
            document.getElementById('capturePalmBtn').style.display = 'none';
        };

        // Meme Camera Logic
        let memeStream = null;
        let activeFilter = 'none';
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
            { name: 'Rona Dhona', icon: '😭', css: 'brightness(80%) contrast(120%) blue-tint' },
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
                    memeStream = stream;
                    const vid = document.getElementById('meme-video');
                    vid.srcObject = stream;
                    
                    const carousel = document.getElementById('filterCarousel');
                    carousel.innerHTML = '';
                    memeFilters.forEach(f => {
                        let btn = document.createElement('div');
                        btn.innerHTML = `<div style="width:70px; height:70px; border-radius:50%; border:3px solid #fff; display:flex; align-items:center; justify-content:center; font-size:2rem; background:rgba(255,255,255,0.1); margin-bottom:5px;">${f.icon}</div><div style="font-size:0.8rem; color:#fff; font-family:'Fredoka One';">${f.name}</div>`;
                        btn.style.cssText = "cursor:pointer !important; pointer-events:auto !important; flex-shrink:0; text-align:center; margin-right:20px; transition: transform 0.2s;";
                        btn.onclick = () => { 
                            vid.style.filter = f.css; 
                            activeFilter = f.css;
                            document.querySelectorAll('#filterCarousel > div').forEach(d => d.style.transform = 'scale(1)');
                            btn.style.transform = 'scale(1.1)';
                        };
                        carousel.appendChild(btn);
                    });
                })
                .catch(err => alert("Meme Camera Failed!"));
        };

        window.captureMemeSnapshot = () => {
            const vid = document.getElementById('meme-video');
            const canvas = document.getElementById('memeCanvas');
            const preview = document.getElementById('snapPreview');
            const popup = document.getElementById('snapPopup');
            
            canvas.width = vid.videoWidth;
            canvas.height = vid.videoHeight;
            const ctx = canvas.getContext('2d');
            
            // Apply filter to canvas context
            ctx.filter = activeFilter;
            ctx.drawImage(vid, 0, 0, canvas.width, canvas.height);
            
            const dataURL = canvas.toDataURL('image/png');
            preview.src = dataURL;
            
            vid.pause();
            popup.style.display = 'flex';
        };

        window.discardMemeSnapshot = () => {
            const vid = document.getElementById('meme-video');
            const popup = document.getElementById('snapPopup');
            popup.style.display = 'none';
            vid.play();
        };

        window.saveMemeToGallery = () => {
            const canvas = document.getElementById('memeCanvas');
            const link = document.getElementById('hiddenSaveLink');
            link.href = canvas.toDataURL('image/png');
            link.download = 'BawaalMeme_' + Date.now() + '.png';
            link.click();
            discardMemeSnapshot();
        };
    </script>
</body>
</html>
