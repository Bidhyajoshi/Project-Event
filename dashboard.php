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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/simple-peer/9.11.1/simplepeer.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <script src="script.js" defer></script>
    <style>
        /* ── RESET & BASE ── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: #08001a; font-family: 'Fredoka One', cursive; overflow-x: hidden; }

        /* ── ANIMATED BACKGROUND ── */
        .chaos-bg {
            position: fixed; inset: 0; z-index: 0; overflow: hidden;
            background: radial-gradient(ellipse at 20% 40%, #1a003a 0%, #08001a 60%, #000d2a 100%);
        }
        .chaos-bg::before {
            content: ''; position: absolute; inset: 0;
            background: radial-gradient(circle at 70% 20%, rgba(255,0,255,0.12) 0%, transparent 50%),
                        radial-gradient(circle at 10% 80%, rgba(0,255,255,0.10) 0%, transparent 50%);
            animation: bgShift 8s ease-in-out infinite alternate;
        }
        @keyframes bgShift { from { opacity: 0.6; } to { opacity: 1; } }

        /* ── FLOATING EMOJIS ── */
        .float-emoji {
            position: fixed; font-size: 2.2rem; opacity: 0; pointer-events: none; z-index: 1;
            animation: floatUp linear infinite;
        }
        @keyframes floatUp {
            0%   { transform: translateY(100vh) rotate(0deg); opacity: 0; }
            10%  { opacity: 0.7; }
            90%  { opacity: 0.5; }
            100% { transform: translateY(-120px) rotate(360deg); opacity: 0; }
        }

        /* ── TOP NAV ── */
        .top-nav {
            position: fixed; top: 0; left: 0; right: 0; z-index: 500;
            display: flex; align-items: center; justify-content: space-between;
            padding: 14px 32px;
            background: rgba(8,0,26,0.75); backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(255,0,255,0.2);
        }
        .nav-logo {
            font-size: 2.4rem; color: #fff; letter-spacing: 3px;
            text-shadow: 0 0 15px #ff00ff, 0 0 30px #ff00ff;
            position: relative; cursor: default;
        }
        .nav-logo .skull { font-size: 1.4rem; margin-left: 4px; vertical-align: middle; }
        .nav-logo .crown { position: absolute; top: -12px; left: -4px; font-size: 1rem; }
        .nav-right { display: flex; align-items: center; gap: 16px; }
        .nav-greet {
            font-size: 1.1rem; color: #fff;
            text-shadow: 0 0 8px #00ffff;
        }
        .nav-greet span { color: #ff00ff; }
        .online-pill {
            display: flex; align-items: center; gap: 6px;
            background: rgba(0,255,0,0.1); border: 1.5px solid #00ff00;
            color: #00ff00; font-size: 1rem; padding: 6px 14px; border-radius: 20px;
            box-shadow: 0 0 10px rgba(0,255,0,0.3);
        }
        .online-dot { width: 8px; height: 8px; background: #00ff00; border-radius: 50%; animation: pulse 1.5s infinite; }
        @keyframes pulse { 0%,100%{box-shadow:0 0 0 0 rgba(0,255,0,0.6);} 50%{box-shadow:0 0 0 6px rgba(0,255,0,0);} }
        .logout-btn {
            display: flex; align-items: center; gap: 6px;
            background: transparent; border: 2px solid #ff0066;
            color: #fff; font-family: 'Fredoka One'; font-size: 1rem;
            padding: 7px 18px; border-radius: 20px; cursor: pointer;
            box-shadow: 0 0 12px rgba(255,0,102,0.4); transition: all 0.2s;
            text-decoration: none;
        }
        .logout-btn:hover { background: #ff0066; box-shadow: 0 0 20px #ff0066; }

        /* ── MAIN CONTAINER ── */
        .dash-main {
            position: relative; z-index: 10;
            min-height: 100vh; padding: 100px 24px 60px;
            display: flex; flex-direction: column; align-items: center;
        }

        /* ── TITLE ── */
        .chaos-title {
            font-size: clamp(2.5rem, 6vw, 4.5rem);
            font-family: 'Fredoka One', cursive;
            background: linear-gradient(90deg, #ff00ff, #00cfff, #ff00ff);
            background-size: 200% auto;
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            animation: titleShimmer 3s linear infinite;
            text-align: center; margin-bottom: 40px;
            filter: drop-shadow(0 0 20px rgba(255,0,255,0.5));
        }
        @keyframes titleShimmer { to { background-position: 200% center; } }

        /* ── CARD GRID ── */
        .chaos-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 22px;
            max-width: 960px; width: 100%;
        }

        /* ── CHAOS CARD ── */
        .chaos-card {
            position: relative;
            background: rgba(10,0,30,0.85);
            border-radius: 20px;
            padding: 30px 20px 22px;
            cursor: pointer;
            display: flex; flex-direction: column; align-items: center; text-align: center;
            gap: 12px;
            transition: transform 0.2s, box-shadow 0.2s;
            overflow: hidden;
        }
        .chaos-card::before {
            content: ''; position: absolute; inset: 0;
            border-radius: 20px;
            padding: 2px;
            background: var(--card-glow, linear-gradient(135deg, #ff00ff, #00cfff));
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: destination-out; mask-composite: exclude;
        }
        .chaos-card::after {
            content: ''; position: absolute; inset: 0; border-radius: 20px;
            background: var(--card-glow, linear-gradient(135deg, rgba(255,0,255,0.12), rgba(0,207,255,0.12)));
            opacity: 0; transition: opacity 0.2s;
        }
        .chaos-card:hover { transform: translateY(-6px) scale(1.02); }
        .chaos-card:hover::after { opacity: 1; }
        .chaos-card:hover { box-shadow: 0 0 40px var(--card-shadow, rgba(255,0,255,0.5)); }

        /* Card colour themes */
        .card-ai   { --card-glow: linear-gradient(135deg,#ff00ff,#7700ff); --card-shadow: rgba(255,0,255,0.55); }
        .card-ome  { --card-glow: linear-gradient(135deg,#00cfff,#0055ff); --card-shadow: rgba(0,207,255,0.55); }
        .card-bunk { --card-glow: linear-gradient(135deg,#ffcc00,#ff6600); --card-shadow: rgba(255,204,0,0.55); }
        .card-exc  { --card-glow: linear-gradient(135deg,#ff00ff,#ff0066); --card-shadow: rgba(255,0,102,0.55); }
        .card-meme { --card-glow: linear-gradient(135deg,#00ffcc,#00cfff); --card-shadow: rgba(0,255,204,0.55); }
        .card-ot   { --card-glow: linear-gradient(135deg,#cc00ff,#ff00aa); --card-shadow: rgba(200,0,255,0.55); }

        .card-icon { font-size: 4rem; line-height: 1; filter: drop-shadow(0 0 12px rgba(255,255,255,0.4)); }
        .card-title { font-size: 1.5rem; color: #fff; font-family: 'Fredoka One'; letter-spacing: 0.5px; }
        .card-sub { font-size: 0.95rem; color: #ccc; line-height: 1.4; }
        .card-sub .accent { font-weight: bold; }
        .card-sub .acc-pink  { color: #ff4daa; }
        .card-sub .acc-cyan  { color: #00cfff; }
        .card-sub .acc-yellow{ color: #ffcc00; }
        .card-sub .acc-green { color: #00ff99; }
        .card-sub .acc-mint  { color: #00ffcc; }
        .card-sub .acc-purple{ color: #cc00ff; }

        /* Dots indicator */
        .card-dots { display: flex; gap: 5px; margin-top: 4px; }
        .card-dots span { width: 6px; height: 6px; border-radius: 50%; background: rgba(255,255,255,0.25); }
        .card-dots span.active { background: #fff; }

        /* ── WARNING POPUP ── */
        .warning-popup {
            position: fixed; top: 90px; right: 24px; z-index: 600;
            background: rgba(20,5,40,0.95); border: 2px solid #ffcc00;
            border-radius: 14px; padding: 16px 20px 16px 16px;
            max-width: 190px; box-shadow: 0 0 20px rgba(255,204,0,0.4);
            animation: warnSlide 0.4s ease;
        }
        @keyframes warnSlide { from { transform: translateX(120%); } to { transform: translateX(0); } }
        .warning-popup .warn-close {
            position: absolute; top: 8px; right: 10px;
            background: none; border: none; color: #fff; font-size: 1rem; cursor: pointer;
        }
        .warning-popup .warn-icon { font-size: 1.8rem; display: block; text-align: center; margin-bottom: 6px; }
        .warning-popup .warn-title { color: #ffcc00; font-size: 1.1rem; text-align: center; }
        .warning-popup .warn-body { color: #ddd; font-size: 0.85rem; text-align: center; margin-top: 4px; font-family: 'Baloo 2', sans-serif; }

        /* ── FOOTER TAG ── */
        .chaos-footer {
            margin-top: 36px;
            display: flex; align-items: center; gap: 8px;
            color: #00cfff; font-size: 1.1rem;
            text-shadow: 0 0 8px #00cfff;
            opacity: 0.85;
        }
        .chaos-footer::before, .chaos-footer::after {
            content: ''; flex: 1; height: 1px;
            background: linear-gradient(90deg, transparent, #00cfff55, transparent);
            min-width: 40px;
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 700px) {
            .chaos-grid { grid-template-columns: repeat(2, 1fr); }
            .chaos-title { font-size: 2rem; }
        }
        @media (max-width: 440px) {
            .chaos-grid { grid-template-columns: 1fr; }
        }

        /* Card colour themes — extra */
        .card-palm { --card-glow: linear-gradient(135deg,#ffaa00,#ff6600); --card-shadow: rgba(255,170,0,0.6); }

        /* Centre the last card when grid has odd count */
        .chaos-grid .chaos-card:last-child:nth-child(3n+1) { grid-column: 2; }

        /* keep portal + other legacy styles working */
        .top-bar { display:none; }
        .dash-container { display:none; }

        /* ── DECORATIVE FIXED EMOJIS ── */
        .deco-emoji {
            position: fixed;
            font-size: 3.5rem;
            pointer-events: none;
            z-index: 2;
            filter: drop-shadow(0 0 12px rgba(255,255,255,0.3));
            user-select: none;
        }

        /* Corner / edge positions */
        .deco-tl  { top: 80px;  left: 20px; }
        .deco-tr  { top: 80px;  right: 20px; }
        .deco-bl  { bottom: 40px; left: 20px; }
        .deco-br  { bottom: 40px; right: 20px; }
        .deco-ml  { top: 50%;   left: 10px;  transform: translateY(-50%); }
        .deco-mr  { top: 50%;   right: 10px; transform: translateY(-50%); }

        /* Animation variants */
        .anim-bounce {
            animation: emojiBounce 3s ease-in-out infinite;
        }
        .anim-spin {
            animation: emojiSpin 6s linear infinite;
        }
        .anim-pulse {
            animation: emojiPulse 2s ease-in-out infinite;
        }
        .anim-shake {
            animation: emojiShake 1.5s ease-in-out infinite;
        }
        .anim-swing {
            animation: emojiSwing 2.5s ease-in-out infinite;
            transform-origin: top center;
        }
        .anim-float {
            animation: emojiFloat 4s ease-in-out infinite;
        }

        @keyframes emojiBounce {
            0%, 100% { transform: translateY(0) scale(1); }
            40%       { transform: translateY(-18px) scale(1.1); }
            60%       { transform: translateY(-10px) scale(1.05); }
        }
        @keyframes emojiSpin {
            from { transform: rotate(0deg); }
            to   { transform: rotate(360deg); }
        }
        @keyframes emojiPulse {
            0%, 100% { transform: scale(1);    opacity: 0.85; }
            50%      { transform: scale(1.25); opacity: 1; filter: drop-shadow(0 0 20px #ff00ff); }
        }
        @keyframes emojiShake {
            0%,100% { transform: rotate(0deg); }
            20%     { transform: rotate(-12deg); }
            40%     { transform: rotate(12deg); }
            60%     { transform: rotate(-8deg); }
            80%     { transform: rotate(8deg); }
        }
        @keyframes emojiSwing {
            0%,100% { transform: rotate(-15deg) translateY(0); }
            50%     { transform: rotate(15deg)  translateY(-8px); }
        }
        @keyframes emojiFloat {
            0%, 100% { transform: translateY(0) rotate(-5deg);  }
            50%      { transform: translateY(-20px) rotate(5deg); }
        }

        /* Orbiting emoji ring around a card */
        .orbit-container {
            position: absolute; inset: 0;
            pointer-events: none; overflow: hidden;
            border-radius: 20px;
        }
        .orbit-emoji {
            position: absolute;
            font-size: 1.2rem;
            animation: orbitRing 6s linear infinite;
            transform-origin: 50% 50%;
        }
        @keyframes orbitRing {
            from { transform: rotate(0deg)   translateX(80px) rotate(0deg); }
            to   { transform: rotate(360deg) translateX(80px) rotate(-360deg); }
        }

        /* Particle burst emojis (appear on hover) */
        .chaos-card:hover .orbit-container { opacity: 1; }
        .chaos-card .orbit-container        { opacity: 0; transition: opacity 0.3s; }

        /* Screen-edge animated strips */
        .emoji-strip {
            position: fixed;
            display: flex;
            flex-direction: column;
            gap: 30px;
            pointer-events: none;
            z-index: 2;
            opacity: 0.65;
        }
        .emoji-strip.left  { left: 4px;  top: 50%; transform: translateY(-50%); }
        .emoji-strip.right { right: 4px; top: 50%; transform: translateY(-50%); }
        .emoji-strip span {
            font-size: 2rem;
            animation: stripFloat 3s ease-in-out infinite;
            display: block;
        }
        .emoji-strip span:nth-child(2) { animation-delay: 0.5s; }
        .emoji-strip span:nth-child(3) { animation-delay: 1s; }
        .emoji-strip span:nth-child(4) { animation-delay: 1.5s; }
        .emoji-strip span:nth-child(5) { animation-delay: 2s; }
        @keyframes stripFloat {
            0%,100% { transform: translateX(0) scale(1); }
            50%     { transform: translateX(6px) scale(1.15); }
        }
    </style>
</head>
<body>

    <!-- CHAOS BACKGROUND -->
    <div class="chaos-bg"></div>

    <!-- FLOATING EMOJIS (spawned by JS) -->
    <div id="floatingEmojis"></div>

    <!-- ══ DECORATIVE FIXED EMOJIS ══ -->
    <!-- Corners -->
    <div class="deco-emoji deco-tl anim-bounce">😂</div>
    <div class="deco-emoji deco-tr anim-shake" style="font-size:2.8rem;">⚡</div>
    <div class="deco-emoji deco-bl anim-float" style="font-size:3rem;">🎮</div>
    <div class="deco-emoji deco-br anim-pulse" style="font-size:3.2rem;">😈</div>

    <!-- Mid sides -->
    <div class="deco-emoji deco-ml anim-swing" style="font-size:2.6rem; left:30px;">🔥</div>
    <div class="deco-emoji deco-mr anim-bounce" style="font-size:2.4rem; right:30px; animation-delay:0.8s;">💀</div>

    <!-- Extra scattered decoratives (hidden on small screens via opacity) -->
    <div class="deco-emoji anim-spin"   style="top:160px; left:90px;  font-size:2rem; opacity:0.5;">✨</div>
    <div class="deco-emoji anim-float"  style="top:200px; right:110px; font-size:2rem; opacity:0.5; animation-delay:1s;">💜</div>
    <div class="deco-emoji anim-bounce" style="bottom:120px; left:110px; font-size:1.8rem; opacity:0.45; animation-delay:0.4s;">🚀</div>
    <div class="deco-emoji anim-shake"  style="bottom:100px; right:90px; font-size:1.8rem; opacity:0.45; animation-delay:1.2s;">🤡</div>

    <!-- Left strip -->
    <div class="emoji-strip left">
        <span>⚡</span>
        <span>🔥</span>
        <span>💸</span>
        <span>🧠</span>
        <span>🎲</span>
    </div>

    <!-- Right strip -->
    <div class="emoji-strip right">
        <span>😈</span>
        <span>💀</span>
        <span>🚀</span>
        <span>👾</span>
        <span>🤖</span>
    </div>

    <!-- WARNING POPUP -->
    <div class="warning-popup" id="warnPopup">
        <button class="warn-close" onclick="document.getElementById('warnPopup').style.display='none'">✕</button>
        <span class="warn-icon">⚠️</span>
        <div class="warn-title">Warning!</div>
        <div class="warn-body">Too much<br>Bawaal detected 📕</div>
    </div>

    <!-- TOP NAV -->
    <nav class="top-nav">
        <div class="nav-logo">
            <span class="crown">👑</span>
            BAWAAL <span class="skull">💀</span>
        </div>
        <div class="nav-right">
            <div class="nav-greet">Sup, <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>? 😏</div>
            <div class="online-pill">
                <div class="online-dot"></div>
                <span id="onlineCount"><?php echo $userCount + rand(1,10); ?></span> Online
            </div>
            <a href="logout.php" class="logout-btn">⏻ Log Out</a>
        </div>
    </nav>

    <!-- MAIN DASHBOARD -->
    <main class="dash-main">
        <h1 class="chaos-title">Choose Your Chaos 😈</h1>

        <div class="chaos-grid">

            <!-- Savage AI -->
            <div class="chaos-card card-ai" onclick="openPortal('ai-portal')">
                <div class="orbit-container"><span class="orbit-emoji" style="animation-delay:0s;">🔥</span><span class="orbit-emoji" style="animation-delay:-3s;">💀</span></div>
                <div class="card-icon">🤖</div>
                <div class="card-title">Savage AI</div>
                <div class="card-sub">Get roasted <span class="accent acc-pink">brutally</span> 🔥</div>
                <div class="card-dots"><span class="active"></span><span></span><span></span><span></span><span></span></div>
            </div>

            <!-- Ome-Gravity -->
            <div class="chaos-card card-ome" onclick="openPortal('ome-portal')">
                <div class="orbit-container"><span class="orbit-emoji" style="animation-delay:0s;">🎧</span><span class="orbit-emoji" style="animation-delay:-2s;">👀</span></div>
                <div class="card-icon">🌐</div>
                <div class="card-title">Ome-Gravity</div>
                <div class="card-sub">Talk to <span class="accent acc-cyan">random chaos</span> 🎧</div>
                <div class="card-dots"><span class="active"></span><span></span><span></span><span></span><span></span></div>
            </div>

            <!-- Bunk Decision -->
            <div class="chaos-card card-bunk" onclick="openPortal('bunk-portal')">
                <div class="card-icon">🎲</div>
                <div class="card-title">Bunk Decision</div>
                <div class="card-sub">Risk it or <span class="accent acc-yellow">regret it</span> 😤</div>
                <div class="card-dots"><span class="active"></span><span></span><span></span><span></span><span></span></div>
            </div>

            <!-- Excuse Generator -->
            <div class="chaos-card card-exc" onclick="openPortal('excuse-portal')">
                <div class="card-icon">😇</div>
                <div class="card-title">Excuse Generator</div>
                <div class="card-sub">Smart lies <span class="accent acc-green">loading</span> • • •</div>
                <div class="card-dots"><span class="active"></span><span></span><span></span><span></span><span></span></div>
            </div>

            <!-- Meme Camera -->
            <div class="chaos-card card-meme" onclick="openPortal('meme-portal')">
                <div class="card-icon">🎭</div>
                <div class="card-title">Meme Camera</div>
                <div class="card-sub">Capture. Meme. <span class="accent acc-mint">Share.</span> 😂</div>
                <div class="card-dots"><span class="active"></span><span></span><span></span><span></span><span></span></div>
            </div>

            <!-- Overthinker 3000 -->
            <div class="chaos-card card-ot" onclick="openPortal('overthink-portal')">
                <div class="card-icon">🧠</div>
                <div class="card-title">Overthinker 3000</div>
                <div class="card-sub">Overthinking in <span class="accent acc-purple">4K</span> 💀</div>
                <div class="card-dots"><span class="active"></span><span></span><span></span><span></span><span></span></div>
            </div>

            <!-- Palm Reader -->
            <div class="chaos-card card-palm" onclick="openPortal('palm-portal')" style="grid-column: 2;">
                <div class="card-icon">🔮</div>
                <div class="card-title">Palm Reader</div>
                <div class="card-sub">Your fate <span class="accent" style="color:#ffaa00;">decoded</span> brutally 🌟</div>
                <div class="card-dots"><span class="active"></span><span></span><span></span><span></span><span></span></div>
            </div>

        </div>

        <!-- Footer -->
        <div class="chaos-footer">➤ Choose wisely... or don't 😈 ↙</div>
    </main>


    <!-- PORTALS -->
    
    <!-- Savage AI Portal -->
    <section class="portal ai-neon-theme" id="ai-portal">
        <div id="aiParticles" class="ai-particles"></div>
        <button class="close-btn neon-btn" onclick="closePortal('ai-portal')">Back to Reality</button>
        
        <div class="ai-centered-container">
            <h1 class="ai-main-title">Savage AI: Emotional Damage Center</h1>
            
            <div class="ai-flex-layout glassmorphism-card">
                <div class="ai-chat-section">
                    <div class="ai-header-row">
                        <div class="ai-modes flex-row">
                            <label class="neon-toggle"><input type="radio" name="aiMode" value="savage" checked> Savage</label>
                            <label class="neon-toggle"><input type="radio" name="aiMode" value="damage"> Damage</label>
                            <label class="neon-toggle"><input type="radio" name="aiMode" value="brainless"> Brainless</label>
                        </div>
                        <button class="neon-btn" style="font-size: 0.9rem;" onclick="clearAIChat()">🗑️ Clear</button>
                    </div>

                    <div id="aiChatBox" class="chat-box neon-glass"></div>
                    
                    <div class="chat-input-row">
                        <input type="text" id="aiInput" class="neon-input" placeholder="Crush? Exams? Money?" onkeypress="if(event.key==='Enter') sendAIMsg()">
                        <button class="arcade-btn" onclick="sendAIMsg()">SEND 🔥</button>
                    </div>
                </div>

                <div class="burn-meter-section neon-glass">
                    <h3>BURN METER</h3>
                    <div class="lava-tube">
                        <div class="lava-fill" id="burnFill">
                            <div class="bubble b1"></div>
                            <div class="bubble b2"></div>
                            <div class="bubble b3"></div>
                        </div>
                    </div>
                    <div id="burnText" class="burn-text glitch-text" data-text="0% Roast">0% Roast</div>
                    <div id="heatLevelText" class="heat-level-text">Mildly Toasted</div>
                </div>
            </div>
            
            <div id="memeSticker" class="meme-sticker"></div>
        </div>
    </section>

    <!-- Ome-Gravity Portal -->
    <section class="portal" id="ome-portal" style="background:#0a0a0a !important; padding:0;">
        <button class="close-btn" onclick="closePortal('ome-portal')" style="z-index:1000001; position:absolute; top:15px; right:15px; background:rgba(255,0,64,0.8); color:#fff; border:none; padding:10px 20px; border-radius:20px; font-family:'Fredoka One'; font-size:1.1rem; cursor:pointer;">✖ Leave</button>

        <div style="display:flex; flex-direction:column; height:100vh; width:100%; max-width:1400px; margin:0 auto; padding:60px 20px 20px;">

            <!-- Title Bar -->
            <div style="text-align:center; margin-bottom:15px;">
                <h1 style="font-family:'Fredoka One'; font-size:2.5rem; color:#00ffff; text-shadow:0 0 20px #00ffff; margin:0;">🌐 Ome-Gravity</h1>
                <p id="omeStatus" style="color:#aaa; font-size:1rem; margin:5px 0;">Connecting your camera...</p>
            </div>

            <!-- Error -->
            <div id="ome-error" style="display:none; text-align:center; color:#ff0040; font-size:1.5rem; padding:20px; background:rgba(255,0,64,0.1); border-radius:10px; margin-bottom:15px;">📷 Camera access denied! Allow camera & mic to use Ome-Gravity.</div>

            <!-- Video Grid -->
            <div id="omeVideoGrid" style="display:none; flex:1; gap:15px; min-height:0;">

                <!-- Left: Stranger Video + Chat -->
                <div style="flex:1; display:flex; flex-direction:column; gap:10px;">
                    <div style="position:relative; flex:1; background:#111; border-radius:15px; overflow:hidden; border:2px solid #00ffff; box-shadow:0 0 20px rgba(0,255,255,0.3);">
                        <div style="position:absolute; top:10px; left:12px; background:rgba(0,0,0,0.7); color:#00ffff; font-family:'Fredoka One'; font-size:1rem; padding:4px 12px; border-radius:20px; z-index:5;">👤 Stranger</div>
                        <div id="radarPulse" class="radar-pulse" style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); z-index:4;"></div>
                        <video id="remoteVideo" autoplay playsinline style="width:100%; height:100%; object-fit:cover; display:block;"></video>
                        <div id="strangerOffline" style="position:absolute; inset:0; display:flex; align-items:center; justify-content:center; flex-direction:column; color:#666; font-size:3rem;">👤<br><span style="font-family:'Fredoka One'; font-size:1rem; color:#555;">Looking for stranger...</span></div>
                    </div>

                    <!-- Chat -->
                    <div style="background:#111; border-radius:12px; border:1px solid #222; padding:12px; display:flex; flex-direction:column; gap:8px; height:200px;">
                        <div id="omeChatBox" style="flex:1; overflow-y:auto; font-size:0.95rem; color:#ccc; scrollbar-width:thin;"></div>
                        <div style="display:flex; gap:8px;">
                            <input type="text" id="omeChatInput" placeholder="Type a message... (Enter to send)" onkeypress="handleOmeChatInline(event)"
                                style="flex:1; background:#1a1a1a; border:1px solid #333; color:#fff; padding:10px 14px; border-radius:8px; font-size:1rem; outline:none;">
                            <button onclick="document.getElementById('omeChatInput').dispatchEvent(new KeyboardEvent('keypress',{key:'Enter',bubbles:true}))" style="background:#00ffff; color:#000; border:none; padding:10px 18px; border-radius:8px; font-family:'Fredoka One'; cursor:pointer;">SEND</button>
                        </div>
                    </div>
                </div>

                <!-- Right: Your Video + Controls -->
                <div style="width:320px; display:flex; flex-direction:column; gap:10px;">
                    <div style="position:relative; flex:1; background:#111; border-radius:15px; overflow:hidden; border:2px solid #ff00ff; box-shadow:0 0 20px rgba(255,0,255,0.3);">
                        <div style="position:absolute; top:10px; left:12px; background:rgba(0,0,0,0.7); color:#ff00ff; font-family:'Fredoka One'; font-size:1rem; padding:4px 12px; border-radius:20px; z-index:5;">🎥 You</div>
                        <video id="localVideo" autoplay muted playsinline style="width:100%; height:100%; object-fit:cover; display:block;"></video>
                    </div>

                    <!-- Controls -->
                    <div style="display:flex; flex-direction:column; gap:10px;">
                        <button id="nextBtn" onclick="nextOmePartnerInline()" style="background:linear-gradient(135deg,#00ffff,#0080ff); color:#000; border:none; padding:16px; border-radius:12px; font-family:'Fredoka One'; font-size:1.4rem; cursor:pointer; width:100%;">⏭ NEXT STRANGER</button>
                        <div style="display:flex; gap:10px;">
                            <button id="muteBtn" onclick="toggleOmeMute()" style="flex:1; background:rgba(255,255,255,0.1); color:#fff; border:1px solid #333; padding:12px; border-radius:10px; font-size:1.5rem; cursor:pointer;">🎤</button>
                            <button id="vidBtn" onclick="toggleOmeVideo()" style="flex:1; background:rgba(255,255,255,0.1); color:#fff; border:1px solid #333; padding:12px; border-radius:10px; font-size:1.5rem; cursor:pointer;">📷</button>
                        </div>
                        <div style="background:rgba(255,255,255,0.05); border-radius:10px; padding:12px; text-align:center;">
                            <div style="color:#aaa; font-size:0.8rem;">CONNECTION</div>
                            <div id="omeConnStatus" style="color:#ffcc00; font-family:'Fredoka One'; font-size:1rem;">🔍 Searching...</div>
                        </div>
                    </div>
                </div>
            </div>
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

    <!-- Overthinker 3000 Portal -->
    <section class="portal ai-neon-theme" id="overthink-portal" style="background: #0a000f !important;">
        <button class="close-btn neon-btn" onclick="closePortal('overthink-portal')" style="z-index: 1000000; position:absolute; top:20px; right:20px;">Back to Reality</button>
        <div class="portal-content" style="display:flex; flex-direction:column; align-items:center; justify-content:center; min-height:100vh; text-align:center; max-width: 900px; margin: 0 auto; padding: 40px 20px;">

            <h1 style="font-size:3.5rem; font-family:'Fredoka One',cursive; color:#ff00ff; text-shadow:0 0 30px #ff00ff, 0 0 60px #ff00ff; margin-bottom:10px;">🧠 OVERTHINKER 3000</h1>
            <p style="color:#aaa; font-size:1.2rem; margin-bottom:40px;">Type what you're overthinking. We'll make it 10x worse.</p>

            <div style="display:flex; gap:15px; width:100%; max-width:700px; margin-bottom:30px;">
                <input type="text" id="otInput"
                    placeholder="e.g. I sent a text and they haven't replied..."
                    class="neon-input"
                    style="flex:1; font-size:1.3rem; padding:18px 20px;"
                    onkeypress="if(event.key==='Enter') document.getElementById('start-panic').click()">
                <button id="start-panic"
                    style="background: linear-gradient(135deg, #ff0040, #ff00ff);
                           color: #fff;
                           border: none;
                           padding: 18px 30px;
                           font-size: 1.4rem;
                           font-family: 'Fredoka One', cursive;
                           border-radius: 12px;
                           cursor: pointer;
                           box-shadow: 0 0 20px #ff0040;
                           transition: all 0.1s;
                           white-space: nowrap;"
                    onmousedown="this.style.transform='scale(0.93)'; this.style.boxShadow='0 0 5px #ff0040'"
                    onmouseup="this.style.transform='scale(1)'; this.style.boxShadow='0 0 20px #ff0040'"
                    onclick="(function(){
                        var input = document.getElementById('otInput').value.trim();
                        if(!input){ alert('Type something! Your brain cannot explode over nothing.'); return; }
                        startEscalatingPanic(input);
                    })()">
                    💥 START PANIC
                </button>
            </div>

            <div id="otList" style="width:100%; max-width:800px; display:flex; flex-direction:column-reverse; gap:12px; max-height:65vh; overflow-y:auto; padding:10px;"></div>

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
    <section class="portal ai-neon-theme" id="overthink-portal">
        <button class="close-btn neon-btn" onclick="closePortal('overthink-portal')">Back to Reality</button>
        
        <div class="ai-centered-container">
            <div class="glassmorphism-card centered-ot-card">
                <h1 class="ai-main-title bawaal-glitch" data-text="Overthinker 3000">Overthinker 3000</h1>
                <p style="color:#00ffff; font-size:1.2rem; margin-bottom:30px; opacity:0.8; text-align:center;">The Simulation of Endless Anxiety</p>
                
                <div style="display:flex; flex-direction:column; gap:20px;">
                    <input type="text" id="otInput" class="neon-input" placeholder="What did they text or say to you?" style="width:100%; text-align:center;">
                    <button id="start-panic" class="glow-btn" style="width: 100%; background:#ff0000; color:#fff; border:none; padding:20px; font-size:1.5rem; font-family:'Fredoka One';">⚠️ START PANIC ATTACK</button>
                </div>
                
                <div id="otList" class="ot-list neon-glass" style="margin-top:30px; width:100%; height:350px; overflow-y:auto; display:flex; flex-direction:column; gap:15px; background:rgba(0,0,0,0.3); border-color:#ff0000;">
                    <!-- Scenarios will pop up here with shaky animation -->
                </div>
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
                if(id === 'ome-portal' && typeof startOmeCamera === 'function') startOmeCamera();
                if(id === 'meme-portal' && typeof startMemeCamera === 'function') startMemeCamera();
                if(id === 'ai-portal' && typeof fetchAIChatHistory === 'function') fetchAIChatHistory();
            }
        };

        window.closePortal = function(id) {
            var el = document.getElementById(id);
            if(el) {
                el.classList.remove('active');
                document.body.classList.remove('no-scroll');
                if(id === 'ome-portal' && typeof localStream !== 'undefined' && localStream) {
                    localStream.getTracks().forEach(t => t.stop());
                }
                if(id === 'meme-portal' && typeof memeStream !== 'undefined' && memeStream) {
                    memeStream.getTracks().forEach(t => t.stop());
                }
                if(id === 'overthink-portal') {
                    var otList = document.getElementById('otList');
                    if(otList) otList.innerHTML = '';
                }
            }
        };

        // ============================================
        // OVERTHINKER 3000 - INLINE ENGINE (GUARANTEED)
        // ============================================
        var otPool = {
            social: [
                "They definitely screenshotted your message and sent it to the 'real' group chat.",
                "That 'seen' wasn't an accident. They are currently voting on how to ignore you.",
                "Everyone went quiet when you walked in. They were definitely talking about you.",
                "Your crush just added a story to show how much fun they're having without you.",
                "That joke you made 3 years ago? Yeah, they still cringe about it.",
                "You're not the main character. You're the comic relief that isn't even funny."
            ],
            life: [
                "Your future employer just saw your search history. You're never getting a job.",
                "That mistake you made in 10th grade? It's officially on your permanent record.",
                "Your parents are having a meeting right now about how you turned out.",
                "The person you like is laughing at your profile picture with their ex.",
                "You just sent a text to the wrong person. It's too late to unsend it.",
                "You'll never be successful because you spent 3 hours overthinking this one text."
            ],
            cosmic: [
                "Your cringe level just created a localized black hole. Universe is collapsing.",
                "Aliens decided not to visit Earth specifically because of your last story.",
                "The simulation glitched because your anxiety levels broke the server.",
                "You are the reason entropy is accelerating. You're literally killing the universe.",
                "God screenshotted your life to show the angels 'what not to do'.",
                "Your existence is the punchline of a cosmic joke you'll never understand."
            ]
        };

        window.startEscalatingPanic = function(input) {
            var list = document.getElementById('otList');
            if(!list) { alert('ERROR: otList not found!'); return; }
            list.innerHTML = '';

            // 💓 Play panic heartbeat
            if(typeof playPanicHeartbeat === 'function') playPanicHeartbeat();

            var prefixes = ["What if...", "Suddenly...", "In 5 minutes...", "By next year...", "Actually...", "Plot twist:", "Worst part is...", "Basically...", "Deep down...", "FACT:"];
            var levelColors = ['#00ff88','#00ff88','#aaff00','#aaff00','#ffcc00','#ffcc00','#ff8800','#ff4400','#ff0000','#ff0000'];
            var scenarios = [];

            for(var i = 1; i <= 10; i++) {
                var pool = i <= 3 ? otPool.social : (i <= 7 ? otPool.life : otPool.cosmic);
                var base = pool[Math.floor(Math.random() * pool.length)];
                scenarios.push({ text: prefixes[i-1] + ' ' + base, lvl: i, color: levelColors[i-1] });
            }

            var idx = 0;
            function next() {
                if(idx >= scenarios.length) return;
                var s = scenarios[idx];
                var item = document.createElement('div');
                item.style.cssText = 'background:rgba(255,0,80,0.1); border-left:4px solid ' + s.color + '; border-radius:10px; padding:14px 18px; display:flex; align-items:flex-start; gap:12px; margin-bottom:8px;';

                var badge = document.createElement('span');
                badge.style.cssText = 'background:' + s.color + '; color:#000; font-family:"Fredoka One",cursive; font-size:0.85rem; font-weight:bold; padding:3px 10px; border-radius:20px; white-space:nowrap; flex-shrink:0; margin-top:2px;';
                badge.textContent = 'LVL ' + s.lvl;

                var textEl = document.createElement('span');
                textEl.style.cssText = 'color:#fff; font-size:1.05rem; line-height:1.5; text-align:left;';

                item.appendChild(badge);
                item.appendChild(textEl);
                list.appendChild(item);
                list.scrollTop = list.scrollHeight;

                window.typewriterEffect(textEl, s.text, 18, function() {
                    idx++;
                    setTimeout(next, 350);
                });
            }
            next();
        };

        window.typewriterEffect = function(el, text, speed, cb) {
            var i = 0;
            function type() {
                if(i < text.length) {
                    el.textContent += text.charAt(i++);
                    setTimeout(type, speed);
                } else if(cb) cb();
            }
            type();
        };

        // ============================================
        // SAVAGE AI - INLINE ENGINE (GUARANTEED)
        // ============================================
        var savageDB = {
            "Exams/Study": [
                "You're studying? Even the textbook is surprised.",
                "Your GPA called. It's currently in the ICU.",
                "Studying now is like putting a band-aid on a bullet wound.",
                "I've seen more potential in an unplugged toaster.",
                "Your brain cells are on unpaid leave.",
                "The only thing you'll pass is out from stress.",
                "Are you reading the textbook or waiting for the movie adaptation?",
                "You call that studying? That's just staring at paper.",
                "Your pen ran out of ink from writing excuses, not answers.",
                "Even Google doesn't want to help you with this."
            ],
            "Crush/Love": [
                "Your crush saw your message and decided to stay single forever.",
                "You have a better chance of flying than getting a text back.",
                "Their 'seen' is a polite way of saying 'never again'.",
                "You're not in the friendzone — you're in the 'who are you' zone.",
                "They took 3 days to reply and you answered in 3 seconds. Have some shame.",
                "Your love life is as dry as college canteen food.",
                "They are typing... a restraining order.",
                "They opened your message and immediately called their therapist.",
                "Your crush added you to their 'Do Not Answer' list.",
                "You manifested rejection at a professional level."
            ],
            "Money/Broke": [
                "Your bank balance is a cry for help disguised as a number.",
                "You're so broke, even 'free' is too expensive for you.",
                "Your wallet has more cobwebs than cash.",
                "You couldn't afford to pay attention right now.",
                "Your net worth is basically an apology letter.",
                "You check your bank app just to watch the loading screen.",
                "If crying paid, you'd be Elon Musk by now.",
                "Your financial plan is 'hope for the best and eat Maggi'.",
                "Your card got declined at a free sample stall.",
                "You're not broke, you're just pre-wealthy. Very pre."
            ],
            "Default": [
                "I'd agree with you, but then we'd both be wrong.",
                "You bring everyone joy... when you leave the room.",
                "I'm not insulting you, I'm describing you.",
                "Keep rolling your eyes. Maybe you'll find a brain back there.",
                "You're like a cloud. When you disappear, it's a beautiful day.",
                "You are the human equivalent of a participation trophy.",
                "You are more disappointing than an unsalted pretzel.",
                "I'm jealous of people who don't know you.",
                "You're proof that evolution can go in reverse.",
                "I thought of you today. It reminded me to take out the trash.",
                "You're a grey sprinkle on a rainbow cupcake.",
                "If ignorance is bliss, you must be the happiest person alive.",
                "You are the reason shampoo has instructions.",
                "You look like a 'Before' picture.",
                "I'd love to roast you, but my mom told me not to burn trash.",
                "You're about as useful as a screen door on a submarine.",
                "You are a pizza crust. Everybody leaves you behind.",
                "Some babies were dropped on their heads. You were thrown at a wall.",
                "Did you fall from heaven? Because so did Satan.",
                "You sound reasonable. Time to up my medication."
            ]
        };

        var recentRoasts = [];
        var burnLevel = 0;

        // ============================================
        // MEME SOUND ENGINE (Web Audio API)
        // ============================================
        var audioCtx = null;
        function getAudioCtx() {
            if(!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            return audioCtx;
        }

        // 🔊 VINE BOOM - Deep bass drop (plays after each roast)
        function playVineBoom() {
            try {
                var ctx = getAudioCtx();
                var osc = ctx.createOscillator();
                var gain = ctx.createGain();
                osc.connect(gain); gain.connect(ctx.destination);
                osc.type = 'sine';
                osc.frequency.setValueAtTime(180, ctx.currentTime);
                osc.frequency.exponentialRampToValueAtTime(30, ctx.currentTime + 0.5);
                gain.gain.setValueAtTime(1.0, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.6);
                osc.start(ctx.currentTime);
                osc.stop(ctx.currentTime + 0.6);
            } catch(e) {}
        }

        // 📯 AIRHORN - For 100% burn meter
        function playAirhorn() {
            try {
                var ctx = getAudioCtx();
                [587, 740, 880].forEach(function(freq, i) {
                    var osc = ctx.createOscillator();
                    var gain = ctx.createGain();
                    osc.connect(gain); gain.connect(ctx.destination);
                    osc.type = 'sawtooth';
                    osc.frequency.setValueAtTime(freq, ctx.currentTime + i * 0.08);
                    gain.gain.setValueAtTime(0.4, ctx.currentTime + i * 0.08);
                    gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + i * 0.08 + 0.5);
                    osc.start(ctx.currentTime + i * 0.08);
                    osc.stop(ctx.currentTime + i * 0.08 + 0.5);
                });
            } catch(e) {}
        }

        // 😐 BRUH - Low descending tone (plays on clear)
        function playBruh() {
            try {
                var ctx = getAudioCtx();
                var osc = ctx.createOscillator();
                var gain = ctx.createGain();
                osc.connect(gain); gain.connect(ctx.destination);
                osc.type = 'triangle';
                osc.frequency.setValueAtTime(300, ctx.currentTime);
                osc.frequency.exponentialRampToValueAtTime(80, ctx.currentTime + 0.8);
                gain.gain.setValueAtTime(0.7, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.9);
                osc.start(ctx.currentTime);
                osc.stop(ctx.currentTime + 0.9);
            } catch(e) {}
        }

        // 💓 PANIC HEARTBEAT - For Overthinker 3000 start
        function playPanicHeartbeat() {
            try {
                var ctx = getAudioCtx();
                [0, 0.15, 0.8, 0.95].forEach(function(t) {
                    var osc = ctx.createOscillator();
                    var gain = ctx.createGain();
                    osc.connect(gain); gain.connect(ctx.destination);
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(60, ctx.currentTime + t);
                    gain.gain.setValueAtTime(0.9, ctx.currentTime + t);
                    gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + t + 0.12);
                    osc.start(ctx.currentTime + t);
                    osc.stop(ctx.currentTime + t + 0.12);
                });
            } catch(e) {}
        }

        function getSavageReplyInline(input) {
            var cat = "Default";
            var lc = input.toLowerCase();
            if(lc.includes('exam') || lc.includes('study') || lc.includes('gpa') || lc.includes('marks') || lc.includes('test') || lc.includes('fail') || lc.includes('book')) cat = "Exams/Study";
            else if(lc.includes('crush') || lc.includes('love') || lc.includes('text') || lc.includes('reply') || lc.includes('date') || lc.includes('single') || lc.includes('gf') || lc.includes('bf')) cat = "Crush/Love";
            else if(lc.includes('money') || lc.includes('broke') || lc.includes('poor') || lc.includes('bank') || lc.includes('rich') || lc.includes('wallet')) cat = "Money/Broke";

            var pool = savageDB[cat];
            var available = pool.filter(function(r){ return recentRoasts.indexOf(r) === -1; });
            if(available.length === 0) { available = pool; recentRoasts = []; }
            var chosen = available[Math.floor(Math.random() * available.length)];
            recentRoasts.push(chosen);
            if(recentRoasts.length > 5) recentRoasts.shift();
            return chosen;
        }

        window.sendAIMsg = function() {
            var box = document.getElementById('aiChatBox');
            var inputField = document.getElementById('aiInput');
            var val = inputField.value.trim();
            if(!val) return;

            // Show user message
            var userMsg = document.createElement('div');
            userMsg.className = 'msg user';
            userMsg.textContent = val;
            box.appendChild(userMsg);
            inputField.value = '';
            box.scrollTop = box.scrollHeight;

            // Show thinking
            var thinking = document.createElement('div');
            thinking.className = 'msg ai';
            thinking.style.opacity = '0.6';
            thinking.style.fontStyle = 'italic';
            thinking.textContent = 'Generating Emotional Damage...';
            box.appendChild(thinking);
            box.scrollTop = box.scrollHeight;
            inputField.disabled = true;

            setTimeout(function() {
                thinking.remove();
                inputField.disabled = false;
                inputField.focus();

                var reply = getSavageReplyInline(val);

                var aiMsg = document.createElement('div');
                aiMsg.className = 'msg ai';
                aiMsg.textContent = reply;
                box.appendChild(aiMsg);
                box.scrollTop = box.scrollHeight;

                // Burn Meter
                burnLevel = Math.min(burnLevel + 25, 100);
                var burnFill = document.getElementById('burnFill');
                var burnText = document.getElementById('burnText');
                var heatText = document.getElementById('heatLevelText');
                if(burnFill) { burnFill.style.height = burnLevel + '%'; burnFill.style.background = '#ff0040'; }
                if(burnText) { burnText.innerText = burnLevel + '% Roast'; burnText.setAttribute('data-text', burnLevel + '% Roast'); }
                if(heatText) {
                    if(burnLevel <= 33) heatText.innerText = 'Mildly Toasted';
                    else if(burnLevel <= 66) heatText.innerText = 'Third Degree Burn';
                    else heatText.innerText = '🔥 ABSOLUTE ASHES';
                }

                // 🔊 Play meme sound
                if(burnLevel >= 100) {
                    setTimeout(playAirhorn, 200); // 📯 Airhorn at max burn!
                } else {
                    playVineBoom(); // 💥 Vine boom on each roast
                }

                // Meme sticker random pop
                if(Math.random() < 0.3) {
                    var meme = document.getElementById('memeSticker');
                    if(meme) { meme.innerHTML = '🔥 SAVAGE'; meme.style.display = 'block'; setTimeout(function(){ meme.style.display='none'; }, 2000); }
                }
            }, 1000);
        };

        window.clearAIChat = function() {
            playBruh(); // 😐 BRUH sound on clear
            var box = document.getElementById('aiChatBox');
            if(box) box.innerHTML = '';
            burnLevel = 0;
            var burnFill = document.getElementById('burnFill');
            var burnText = document.getElementById('burnText');
            var heatText = document.getElementById('heatLevelText');
            if(burnFill) burnFill.style.height = '0%';
            if(burnText) { burnText.innerText = '0% Roast'; burnText.setAttribute('data-text', '0% Roast'); }
            if(heatText) heatText.innerText = 'Mildly Toasted';
        };

        // ============================================
        // OME-GRAVITY - INLINE WebRTC ENGINE
        // ============================================
        var omeLocalStream = null;
        var omePeer = null;
        var omeSessionId = null;
        var omeRole = null;
        var omePollInterval = null;
        var omeLastSignal = '';
        var omeIsMuted = false;
        var omeIsVideoOff = false;

        function omeSetStatus(msg, color) {
            var s = document.getElementById('omeStatus');
            var c = document.getElementById('omeConnStatus');
            if(s) s.textContent = msg;
            if(c) c.textContent = msg;
            if(c && color) c.style.color = color;
        }

        function omeAddChat(who, msg, color) {
            var box = document.getElementById('omeChatBox');
            if(!box) return;
            var d = document.createElement('div');
            d.style.cssText = 'padding:4px 0; border-bottom:1px solid #1a1a1a;';
            d.innerHTML = '<span style="color:' + (color||'#aaa') + '; font-weight:bold;">' + who + ':</span> <span style="color:#ddd;">' + msg + '</span>';
            box.appendChild(d);
            box.scrollTop = box.scrollHeight;
        }

        function omeJoinWaiting() {
            if(omePeer) { try { omePeer.destroy(); } catch(e){} omePeer = null; }
            clearInterval(omePollInterval);
            omeLastSignal = '';
            var remVid = document.getElementById('remoteVideo');
            if(remVid) remVid.srcObject = null;
            var radar = document.getElementById('radarPulse');
            if(radar) radar.style.display = 'block';
            var offline = document.getElementById('strangerOffline');
            if(offline) offline.style.display = 'flex';
            var chatBox = document.getElementById('omeChatBox');
            if(chatBox) chatBox.innerHTML = '';
            omeSetStatus('🔍 Looking for a stranger...', '#ffcc00');
            omeAddChat('System', 'Searching for someone...', '#888');

            fetch('ome-gravity.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'action=join'
            }).then(function(r){ return r.json(); }).then(function(data) {
                if(data.success) {
                    omeSessionId = data.session_id;
                    omeRole = data.role;
                    omeInitPeer();
                    omePollInterval = setInterval(omePoll, 1500);
                }
            }).catch(function(e){ console.error('ome join error:', e); });
        }

        function omeInitPeer() {
            if(typeof SimplePeer === 'undefined') {
                setTimeout(omeInitPeer, 300);
                return;
            }
            omePeer = new SimplePeer({
                initiator: omeRole === 'caller',
                stream: omeLocalStream,
                trickle: false,
                config: { iceServers: [{ urls: 'stun:stun.l.google.com:19302' }] }
            });

            omePeer.on('signal', function(data) {
                fetch('ome-gravity.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'action=signal&session_id=' + omeSessionId + '&signal=' + encodeURIComponent(JSON.stringify(data))
                });
            });

            omePeer.on('stream', function(stream) {
                var remVid = document.getElementById('remoteVideo');
                if(remVid) remVid.srcObject = stream;
                var radar = document.getElementById('radarPulse');
                if(radar) radar.style.display = 'none';
                var offline = document.getElementById('strangerOffline');
                if(offline) offline.style.display = 'none';
                omeSetStatus('🟢 Stranger connected!', '#00ff88');
                omeAddChat('System', '⚡ Stranger connected! Say something!', '#00ff88');
            });

            omePeer.on('data', function(data) {
                var msg = new TextDecoder('utf-8').decode(data);
                omeAddChat('Stranger', msg, '#00ffff');
            });

            omePeer.on('close', function() {
                omeAddChat('System', '👋 Stranger disconnected.', '#ff6666');
                omeSetStatus('💔 Stranger left. Finding next...', '#ff6666');
                setTimeout(omeJoinWaiting, 1500);
            });

            omePeer.on('error', function(e) {
                console.error('ome peer error:', e);
                setTimeout(omeJoinWaiting, 2000);
            });
        }

        function omePoll() {
            if(!omeSessionId) return;
            fetch('ome-gravity.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'action=poll&session_id=' + omeSessionId
            }).then(function(r){ return r.json(); }).then(function(data) {
                if(data.status === 'closed') {
                    clearInterval(omePollInterval);
                    omeJoinWaiting();
                    return;
                }
                if(data.peer_signal) {
                    var sigStr = JSON.stringify(data.peer_signal);
                    if(sigStr !== omeLastSignal && omePeer) {
                        omeLastSignal = sigStr;
                        try { omePeer.signal(data.peer_signal); } catch(e){}
                    }
                }
            }).catch(function(e){ console.error('ome poll error:', e); });
        }

        window.startOmeCamera = function() {
            if(omeLocalStream) { omeJoinWaiting(); return; }
            omeSetStatus('📷 Requesting camera...', '#ffcc00');
            navigator.mediaDevices.getUserMedia({ video: true, audio: true })
                .then(function(stream) {
                    omeLocalStream = stream;
                    var localVid = document.getElementById('localVideo');
                    if(localVid) localVid.srcObject = stream;
                    var grid = document.getElementById('omeVideoGrid');
                    if(grid) { grid.style.display = 'flex'; }
                    var errEl = document.getElementById('ome-error');
                    if(errEl) errEl.style.display = 'none';
                    omeJoinWaiting();
                })
                .catch(function(e) {
                    console.error('camera error:', e);
                    var errEl = document.getElementById('ome-error');
                    if(errEl) errEl.style.display = 'block';
                    omeSetStatus('❌ Camera denied!', '#ff0040');
                });
        };

        window.nextOmePartnerInline = function() {
            if(!omeSessionId) return;
            omeAddChat('System', 'Skipping stranger...', '#888');
            fetch('ome-gravity.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'action=skip&session_id=' + omeSessionId
            }).then(function(){ omeJoinWaiting(); }).catch(function(e){ console.error(e); });
        };

        window.handleOmeChatInline = function(e) {
            if(e.key !== 'Enter') return;
            var input = document.getElementById('omeChatInput');
            var msg = input.value.trim();
            if(!msg) return;
            if(omePeer && omePeer.connected) {
                omePeer.send(msg);
                omeAddChat('You', msg, '#ff00ff');
            } else {
                omeAddChat('System', 'Not connected yet. Wait for a stranger!', '#ff6666');
            }
            input.value = '';
        };

        window.toggleOmeMute = function() {
            if(!omeLocalStream) return;
            omeIsMuted = !omeIsMuted;
            omeLocalStream.getAudioTracks().forEach(function(t){ t.enabled = !omeIsMuted; });
            var btn = document.getElementById('muteBtn');
            if(btn) { btn.textContent = omeIsMuted ? '🔇' : '🎤'; btn.style.background = omeIsMuted ? '#ff3300' : 'rgba(255,255,255,0.1)'; }
        };

        window.toggleOmeVideo = function() {
            if(!omeLocalStream) return;
            omeIsVideoOff = !omeIsVideoOff;
            omeLocalStream.getVideoTracks().forEach(function(t){ t.enabled = !omeIsVideoOff; });
            var btn = document.getElementById('vidBtn');
            if(btn) { btn.textContent = omeIsVideoOff ? '🚫' : '📷'; btn.style.background = omeIsVideoOff ? '#ff3300' : 'rgba(255,255,255,0.1)'; }
        };

        // Update openPortal to use inline camera function
        window.openPortal = function(id) {
            console.log('Opening Portal: ' + id);
            var el = document.getElementById(id);
            if(el) {
                el.classList.add('active');
                document.body.classList.add('no-scroll');
                if(id === 'ome-portal') startOmeCamera();
                if(id === 'meme-portal' && typeof startMemeCamera === 'function') startMemeCamera();
                if(id === 'ai-portal' && typeof fetchAIChatHistory === 'function') fetchAIChatHistory();
            }
        };


        // ============================================
        // EXCUSE GENERATOR - INLINE ENGINE (GUARANTEED)
        // ============================================
        var excuseDB = {
            Professor: {
                Late: {
                    Emotional:     ["Sir, my dog ate my alarm clock and then cried about it. I couldn't just leave him like that.", "My mother called crying because she saw me in her dream failing. I had to console her for 45 minutes.", "I was on my way but a stray kitten followed me and I couldn't abandon it. I'm not a monster, sir."],
                    Savage:        ["Traffic was invented specifically to ruin my morning, sir.", "I was here spiritually. My body had some commitments.", "Google Maps had an existential crisis and took me on a tour of the city."],
                    Professional:  ["I encountered an unforeseen logistical delay in my morning commute, sir. It has been duly noted and corrective measures are being implemented.", "There was a critical infrastructure failure on my primary route. I arrived at the earliest possible opportunity.", "My schedule optimization algorithm failed to account for external variables. This will not recur."],
                    Confused:      ["Sir I thought today was Sunday? No wait... what day is it?", "I came, I think? Maybe that was yesterday. Time is a construct, sir.", "I was here but I was invisible. Did you not see me wave?"]
                },
                Assignment: {
                    Emotional:     ["Sir, my grandmother called while I was on page 2 and told me about her knee pain for 3 hours. I couldn't just hang up.", "I was doing it sir but I started crying because I understood nothing and had to restart 6 times.", "My WiFi cut off mid-submission and I sat in the dark for an hour questioning my choices."],
                    Savage:        ["The assignment was done. My laptop decided to factory reset itself out of spite.", "I submitted it sir. Maybe check your spam folder. Or your soul.", "I did it but it was so good I was afraid to submit it. Imposter syndrome."],
                    Professional:  ["The deliverable encountered a technical impediment during the final submission phase. I have version 2 ready for immediate handover.", "Due to system downtime, the document could not be transmitted within the stipulated timeframe.", "The assignment is complete but requires final quality assurance before formal submission, sir."],
                    Confused:      ["Wait, we had an assignment?? Since when? In which subject?", "I submitted it... I think. Or maybe I dreamed that. Both are equally possible.", "I thought the deadline was next week? No? Are you sure? Can we double-check?"]
                },
                Bunked: {
                    Emotional:     ["Sir I was outside the door but I heard you were teaching something hard and I panicked and went home and cried.", "I had a breakdown, sir. Not for any reason. Just general life reasons.", "I really wanted to come but my body said no and we had a long argument about it."],
                    Savage:        ["Sir I was doing independent research. At home. In bed. It was very productive.", "I was there in spirit, sir. My spirit takes attendance, not my body.", "I was preserving my mental health as per WHO guidelines."],
                    Professional:  ["I was attending to an urgent personal matter that required immediate attention and prevented campus attendance.", "I was engaged in self-directed learning on the subject matter to maximize retention.", "There was a scheduling conflict with a prior commitment. I have obtained notes from a peer."],
                    Confused:      ["I came but I went to the wrong room and by the time I found the right one it was over?", "Was there class yesterday? I thought it was cancelled. Who told me that? I don't know.", "I was there. Are you sure you didn't see me? I was wearing a blue shirt. Or maybe grey."]
                },
                ForgotBirthday: {
                    Emotional:     ["Sir this is unrelated to academics but I'm emotionally unavailable right now.", "I haven't forgotten. I'm just... processing. Slowly.", "My heart remembered but my phone didn't. We had a fight about it."],
                    Savage:        ["Age is just a number, sir. And numbers are just math. And math is your department.", "I celebrate birthdays on the lunar calendar. You just wouldn't understand.", "I sent my wishes via eye contact last Tuesday. Did you not feel it?"],
                    Professional:  ["I acknowledge the oversight and would like to formally extend belated greetings.", "I have flagged this in my personal calendar to ensure timely acknowledgement next year.", "Please accept my retrospective acknowledgement of your annual milestone, sir."],
                    Confused:      ["Wait, your birthday was yesterday? I thought it was... hmm. When did we discuss this?", "I remembered but then I forgot and then I remembered again but it was too late.", "Happy Birthday? No wait is it today? Oh no. Or is it tomorrow? I'm so confused."]
                }
            },
            HOD: {
                Late: {
                    Emotional:     ["Ma'am, there was a very moving scene in the lift and I couldn't leave until it resolved.", "Sir, I was emotionally preparing myself to face the day. It took longer than expected.", "The weight of my academic responsibilities briefly paralysed me at the gate, ma'am."],
                    Savage:        ["The institution's infrastructure does not support my punctuality, sir.", "I was here. The clock was wrong.", "Traffic laws and I have a complicated relationship, ma'am."],
                    Professional:  ["I encountered an unavoidable delay. It will not impact my performance metrics.", "My time management system experienced a temporary failure. It has been recalibrated.", "I was attending to a prior obligation that overran its scheduled duration, sir."],
                    Confused:      ["Sir, I thought the meeting was at a different time? Wasn't it shifted? No?", "I came on time, I think. Or was I early? Time is relative, ma'am.", "I was waiting downstairs. Did someone not tell me to come up?"]
                },
                Assignment: { Emotional: ["Ma'am it's 90% done and the last 10% is just my soul giving up."], Savage: ["It's submitted. Check the parallel universe where emails work, sir."], Professional: ["The deliverable is pending final review before submission, ma'am."], Confused: ["Which assignment, ma'am? The one from last week or last month?"] },
                Bunked: { Emotional: ["Ma'am I had a personal crisis. I can't elaborate without crying."], Savage: ["I took a mental health day. The WHO supports this, sir."], Professional: ["I was unable to attend due to a prior engagement, ma'am."], Confused: ["I thought class was rescheduled, sir? Wasn't there an announcement?"] },
                ForgotBirthday: { Emotional: ["Ma'am I remembered but I was too nervous to say it and then the moment passed."], Savage: ["Age is confidential, ma'am. I was protecting your privacy."], Professional: ["Please accept my belated acknowledgement of your milestone, sir."], Confused: ["Is it today? I had it marked as next week, ma'am. My calendar betrayed me."] }
            },
            Parents: {
                Late: {
                    Emotional:     ["Maa, I left on time but the auto driver started telling me his life story and I couldn't be rude. He was going through a lot.", "Papa, I was literally running but then I saw an uncle who looked lost and I helped him. You raised me right!", "I called you but my phone was on silent. I was crying about it the whole way home."],
                    Savage:        ["The concept of 'on time' is a societal construct. I was here eventually.", "Traffic happened. As it always does. At predictable times. Which I ignored.", "I started late. I know. Let's not make this a thing."],
                    Professional:  ["I was delayed due to an external logistical factor beyond my immediate control.", "My expected time of arrival was affected by peak-hour congestion.", "The delay was 12 minutes, well within acceptable variance, statistically speaking."],
                    Confused:      ["Wait, I thought you said to come at 7? Or was it 6? What time is it now even?", "I came at the right time! You said 6, right? Or... hmm. Was that yesterday?", "Maa, I'm confused. I left when you told me. I think."]
                },
                Assignment: { Emotional: ["Papa, I was doing it but my eyes started hurting and I didn't want to go blind for you."], Savage: ["I did it. My teacher lost it. This is not my problem anymore."], Professional: ["The task is in progress and will be completed within a revised timeline."], Confused: ["Wait, I had homework? In which subject? What class is this again?"] },
                Bunked: { Emotional: ["Maa, I went but I started feeling sick and I didn't want to collapse in public. I thought of you."], Savage: ["College is optional. Knowledge is not. I was self-studying. At home. In bed."], Professional: ["I was unable to attend due to unforeseen health-related circumstances."], Confused: ["Wasn't it a holiday? Someone in my class said it was a holiday. I trusted them."] },
                ForgotBirthday: {
                    Emotional:     ["Maa I didn't forget. I was just waiting for the perfect moment and then it got late and I'm so sorry please don't cry I love you.", "Papa, I remembered at midnight but you were asleep and I didn't want to wake you. Happy birthday from my whole heart.", "I was collecting myself emotionally to say it properly. I love you so much. Happy birthday. Please forgive me."],
                    Savage:        ["I'm your child. Every day is your gift. A birthday is just capitalism.", "I forgot. But I'm here now. That counts.", "You know I love you. Do you really need a date on a calendar to confirm that?"],
                    Professional:  ["Please accept my formal yet heartfelt recognition of your annual milestone.", "I acknowledge the oversight and would like to retrospectively celebrate this occasion.", "Happy Birthday. I have noted this date for future reference with appropriate reminders."],
                    Confused:      ["Wait, your birthday was today?? Why didn't anyone tell me?! This family has a communication problem!", "I thought it was next week! Someone told me it was next week! Who said that?!", "Maa, are you sure? Because I had written 3rd in my notes. Or was it 13th?"]
                }
            },
            Crush: {
                Late: {
                    Emotional:     ["I was getting ready for 2 hours because I wanted to look perfect for you. You're worth it. Sorry I'm late.", "I was nervous and I dropped my phone in the sink. This is your fault for being so important to me.", "I rehearsed what to say to you 47 times and lost track of time. I take full emotional responsibility."],
                    Savage:        ["Good things take time. I am a good thing. Do the math.", "I made you wait because anticipation is a love language. You're welcome.", "I was fashionably late. The fashion was worth it."],
                    Professional:  ["My arrival was delayed due to preparation activities. The output quality justifies the timeline.", "I optimised my pre-meeting routine which resulted in a minor schedule variance.", "The delay was intentional for quality assurance purposes."],
                    Confused:      ["Wait, were we meeting at 5 or 6? I swear you said 6. Didn't you? My heart says 6.", "I got here at the right time. I think. What time did you get here? Was I supposed to be here first?", "I'm confused. Are we meeting? I thought we were. Am I at the right place?"]
                },
                Assignment: { Emotional: ["I was helping someone else with their assignment and then completely forgot mine. That someone was thinking about you and I blame you."], Savage: ["I didn't do it because you occupy 90% of my brain and there's no room for syllabus."], Professional: ["I was engaged in an interpersonal priority that temporarily superseded academic deliverables."], Confused: ["Did you do the assignment? Can I see yours? Not to copy. Just for... inspiration. And to be near it."] },
                Bunked: {
                    Emotional:     ["I bunked because you weren't going to be there and what is a lecture without you honestly.", "I saw you leave and my motivation left with you. I physically couldn't enter the building.", "I waited outside for 10 minutes hoping you'd come back and then just went home feeling things."],
                    Savage:        ["I optimised my time by spending it thinking about you instead of attending a lecture I'd forget.", "The lecture was not you. Therefore I did not attend.", "I was doing fieldwork. The field was near your building. Purely coincidental."],
                    Professional:  ["I reallocated my time to higher-priority interpersonal engagement opportunities.", "The lecture content was available in documented form. I prioritised relationship development.", "Attendance was not optimal for my current strategic objectives."],
                    Confused:      ["I thought you were bunking too! You said maybe! I took that as a yes! Did you go??", "Wait, was there class? I thought we had agreed to meet instead? Didn't we almost plan something?", "I'm so confused. Did I miss something important? Were you there? This is chaos."]
                },
                ForgotBirthday: {
                    Emotional:     ["I didn't forget, I was planning something perfect but life got in the way and now I hate myself. Happy Birthday. You deserve the world.", "I kept it in my heart but forgot to say it out loud. That's worse than forgetting, I know. I'm so sorry.", "I thought about you all day. I just panicked every time I tried to say it. Happy Birthday. You're incredible."],
                    Savage:        ["I was testing to see if you'd notice. You passed. Happy Birthday.", "Birthdays are for regular people. I celebrate you every day in my mind. Today is no different.", "I forgot. But here I am now. Is this not better than a generic 'HBD' at midnight?"],
                    Professional:  ["I would like to formally acknowledge your birthday and express my sincerest felicitations, albeit retrospectively.", "I have noted this date with high priority in my personal management system.", "Please accept this belated but entirely genuine acknowledgement of your special day."],
                    Confused:      ["WAIT. Is it TODAY?! Why didn't you tell me?! I would have— oh my god. Happy Birthday! I'm spiralling!", "Someone told me it was next week!! I've been preparing mentally for next week!! This is sabotage!", "Today is your birthday?? But I— I thought— oh no. Oh no no no. Happy Birthday. I'm a disaster."]
                }
            },
            Boss: {
                Late: {
                    Emotional:     ["Sir, I had a minor personal emergency this morning that I'm still processing. I'm here now and fully committed.", "Ma'am, there was a family matter that required my attention. I appreciate your understanding.", "I was on my way but encountered an emotional situation that required resolution. I'm present and focused now."],
                    Savage:        ["Traffic in this city is a war crime, sir. I survived it for you.", "The infrastructure of this city does not support my schedule, ma'am.", "I was on time in my mind. Physically, there were complications."],
                    Professional:  ["I encountered an unforeseen logistical delay during my commute. I have already implemented measures to prevent recurrence.", "My arrival was delayed by 14 minutes due to external traffic conditions beyond my control.", "I apologise for the delay. It will not impact today's deliverables."],
                    Confused:      ["Sir, I thought the shift started at 10? Did it change? Nobody told me. Did someone send a message?", "Ma'am, I logged my arrival. The system might have glitched. Can we check the records?", "I'm confused, sir. I thought today was a late start. Is there a schedule change I missed?"]
                },
                Assignment: { Emotional: ["Sir, I was working on it but had a rough night and couldn't finish. I'll have it by EOD, I promise."], Savage: ["The report is done. My laptop is not. Please extend the deadline by 2 hours."], Professional: ["The deliverable is at 90% completion. I will submit by EOD with full quality assurance."], Confused: ["Sir, which report? The Q3 one or the new format one? I started both. Which one first?"] },
                Bunked: { Emotional: ["Ma'am I had a health concern I wasn't comfortable disclosing but I'm feeling better now."], Savage: ["I took a mental health day. I'm 40% more productive today as a result."], Professional: ["I was absent due to a personal medical matter. Documentation is available upon request."], Confused: ["Sir, I applied for leave through the portal? It didn't go through? This is a system issue."] },
                ForgotBirthday: { Emotional: ["Sir, I remembered but I was in back-to-back calls and the moment passed. I feel terrible."], Savage: ["Happy belated birthday, sir. I celebrated internally. Very enthusiastically."], Professional: ["Please accept my belated birthday wishes. I have flagged this for next year with a 3-day advance reminder."], Confused: ["Sir, today is your birthday?! I had it saved as next week! Happy Birthday! The calendar lied to me."] }
            }
        };

        function getCreativeExcuse(target, situation, vibe) {
            var t = excuseDB[target];
            if(!t) t = excuseDB['Professor'];
            var s = t[situation];
            if(!s) s = t['Late'];
            var pool = s[vibe];
            if(!pool || !pool.length) pool = s['Savage'] || ["I have no excuse. Just vibes."];
            return pool[Math.floor(Math.random() * pool.length)];
        }

        window.brewExcuse = function() {
            var target = document.getElementById('exTarget').value;
            var situation = document.getElementById('exSituation').value;
            var vibe = document.getElementById('exVibe').value;

            if(!target || !situation || !vibe) {
                alert('🍲 Select ALL 3 ingredients before brewing the lie!');
                return;
            }

            // Show brewing view
            document.getElementById('exInputView').style.display = 'none';
            document.getElementById('exBrewingView').style.display = 'flex';
            document.getElementById('exResultView').style.display = 'none';

            if(typeof playVineBoom === 'function') playVineBoom();

            setTimeout(function() {
                var excuse = getCreativeExcuse(target, situation, vibe);
                document.getElementById('exResultText').textContent = excuse;
                document.getElementById('exBrewingView').style.display = 'none';
                document.getElementById('exResultView').style.display = 'flex';
                if(typeof playAirhorn === 'function') setTimeout(playAirhorn, 100);
            }, 2500);
        };

        window.copyExcuse = function() {
            var text = document.getElementById('exResultText').textContent;
            navigator.clipboard.writeText(text).then(function() {
                alert('✅ Copied! Go paste it on WhatsApp before you lose your nerve 😂');
            }).catch(function() {
                // Fallback
                var ta = document.createElement('textarea');
                ta.value = text;
                document.body.appendChild(ta);
                ta.select();
                document.execCommand('copy');
                document.body.removeChild(ta);
                alert('✅ Copied the lie! Deploy wisely.');
            });
        };

        window.resetExcuse = function() {
            document.getElementById('exResultView').style.display = 'none';
            document.getElementById('exBrewingView').style.display = 'none';
            document.getElementById('exInputView').style.display = 'flex';
            // Reset selects
            document.getElementById('exTarget').selectedIndex = 0;
            document.getElementById('exSituation').selectedIndex = 0;
            document.getElementById('exVibe').selectedIndex = 0;
        };

        // ============================================
        // BUNK DECISION - INLINE ENGINE (GUARANTEED)
        // ============================================

        var bunkData = { importance: '', friends: 0, attendance: 50 };

        window.nextBunkStep = function(currentStep, val) {
            if(currentStep === 1) {
                bunkData.importance = val || '';
                document.getElementById('bunkStep1').style.display = 'none';
                document.getElementById('bunkStep2').style.display = 'flex';
            } else if(currentStep === 2) {
                var fr = document.getElementById('bunkFriends').value;
                bunkData.friends = parseInt(fr) || 0;
                document.getElementById('bunkStep2').style.display = 'none';
                document.getElementById('bunkStep3').style.display = 'flex';
            }
        };

        window.calculateBunkDestiny = function() {
            bunkData.attendance = parseInt(document.getElementById('bunkAtt').value) || 50;

            // Hide step 3, show loading
            document.getElementById('bunkStep3').style.display = 'none';
            var loading = document.getElementById('bunkLoading');
            loading.style.display = 'flex';

            // Animate loading bar
            var bar = document.getElementById('bunkBar');
            bar.style.width = '0%';
            setTimeout(function(){ bar.style.width = '100%'; }, 80);

            setTimeout(function() {
                loading.style.display = 'none';
                document.getElementById('bunkResult').style.display = 'flex';

                var verdicts = {
                    danger: [
                        { v: '⚠️ DANGER!', r: 'Your attendance is already dead. Go to class or start selling chai.' },
                        { v: '🚨 RISKY AF!', r: 'You\'re one bunk away from a letter home. Behave.' },
                        { v: '💀 GAME OVER', r: 'Below 75%? Bro, you\'re not bunking — you\'re already expelled in spirit.' }
                    ],
                    bunk: [
                        { v: '✅ BUNK IT!', r: 'The squad is waiting. Bunking is now a moral obligation.' },
                        { v: '🎉 100% BUNK!', r: 'Your attendance is solid. Today belongs to the streets.' },
                        { v: '🏖️ FREEDOM!', r: '5+ homies bunking? This is not a choice, it\'s a movement.' }
                    ],
                    sleep: [
                        { v: '😴 SLEEP IN!', r: 'Sleep > This lecture. Go back to bed, legend.' },
                        { v: '🛋️ STAY HOME!', r: 'Who is the prof anyway? Your pillow knows more.' },
                        { v: '☕ Netflix Time!', r: 'Mid lecture + good attendance = guilt-free Netflix session.' }
                    ],
                    attend: [
                        { v: '📚 ATTEND.', r: 'Life or death? Bruh go. We can\'t help you here.' },
                        { v: '🎯 GO IN.', r: 'This one actually matters. Reluctantly, attend.' },
                        { v: '😤 JUST GO.', r: 'You already know the answer. Stop asking an app.' }
                    ]
                };

                var pool, color;
                if(bunkData.attendance < 75) {
                    pool = verdicts.danger; color = '#ff0040';
                } else if(bunkData.friends > 4) {
                    pool = verdicts.bunk; color = '#00ff88';
                } else if(bunkData.importance === 'Mid' || bunkData.importance === 'Who is the Prof?') {
                    pool = verdicts.sleep; color = '#00ffff';
                } else {
                    pool = verdicts.attend; color = '#ffaa00';
                }

                var pick = pool[Math.floor(Math.random() * pool.length)];
                var verdEl = document.getElementById('bunkVerdict');
                var reasonEl = document.getElementById('bunkReason');

                verdEl.innerText = pick.v;
                verdEl.style.color = color;
                verdEl.style.textShadow = '0 0 20px ' + color;
                reasonEl.innerText = pick.r;

                // Play a sound for fun
                if(typeof playVineBoom === 'function') playVineBoom();
            }, 1600);
        };

        window.resetBunk = function() {
            bunkData = { importance: '', friends: 0, attendance: 50 };
            document.getElementById('bunkFriends').value = '';
            document.getElementById('bunkAtt').value = 50;
            document.getElementById('bunkAttVal').innerText = '50%';
            document.getElementById('bunkBar').style.width = '0%';
            document.getElementById('bunkResult').style.display = 'none';
            document.getElementById('bunkStep1').style.display = 'flex';
        };

        // ============================================
        // DASHBOARD UI — ANIMATIONS & LIVE FEATURES
        // ============================================
        document.addEventListener('DOMContentLoaded', function() {

            // --- Floating Emojis ---
            var emojis = ['⚡','🔥','😈','😂','💜','🌀','🎮','💀','🤡','💸','🧠','🚀','🤓','👾','✨','🎲','🤖','🌐'];
            var container = document.getElementById('floatingEmojis');
            if (container) {
                for(var i = 0; i < 18; i++) {
                    var el = document.createElement('div');
                    el.className = 'float-emoji';
                    el.innerText = emojis[i % emojis.length];
                    el.style.left = (Math.random() * 95) + 'vw';
                    el.style.animationDuration = (12 + Math.random() * 18) + 's';
                    el.style.animationDelay = (Math.random() * 12) + 's';
                    el.style.fontSize = (1.4 + Math.random() * 1.4) + 'rem';
                    container.appendChild(el);
                }
            }

            // --- Staggered Card Entry Animation ---
            var cards = document.querySelectorAll('.chaos-card');
            cards.forEach(function(card, idx) {
                card.style.opacity = '0';
                card.style.transform = 'translateY(40px) scale(0.95)';
                card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                setTimeout(function() {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0) scale(1)';
                }, 150 + idx * 100);
            });

            // --- Title Entry Animation ---
            var title = document.querySelector('.chaos-title');
            if(title) {
                title.style.opacity = '0';
                title.style.transform = 'translateY(-20px)';
                title.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                setTimeout(function() {
                    title.style.opacity = '1';
                    title.style.transform = 'translateY(0)';
                }, 50);
            }

            // --- Auto-hide Warning Popup after 6s ---
            var popup = document.getElementById('warnPopup');
            if(popup) {
                setTimeout(function() {
                    popup.style.transition = 'opacity 0.8s ease, transform 0.8s ease';
                    popup.style.opacity = '0';
                    popup.style.transform = 'translateX(120%)';
                    setTimeout(function() { popup.style.display = 'none'; }, 800);
                }, 6000);
            }

            // --- Live Online Count (polls every 30s) ---
            function updateOnlineCount() {
                fetch('online_count.php')
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        if(data.success) {
                            var el = document.getElementById('onlineCount');
                            if(el) el.textContent = data.count;
                        }
                    }).catch(function() {}); // silent fail
            }
            updateOnlineCount();
            setInterval(updateOnlineCount, 30000);

            // --- Card click ripple effect ---
            document.querySelectorAll('.chaos-card').forEach(function(card) {
                card.addEventListener('mousedown', function(e) {
                    var ripple = document.createElement('span');
                    ripple.style.cssText = [
                        'position:absolute',
                        'border-radius:50%',
                        'background:rgba(255,255,255,0.15)',
                        'width:10px','height:10px',
                        'top:' + (e.offsetY - 5) + 'px',
                        'left:' + (e.offsetX - 5) + 'px',
                        'transform:scale(0)',
                        'animation:rippleEffect 0.5s ease-out forwards',
                        'pointer-events:none','z-index:10'
                    ].join(';');
                    card.appendChild(ripple);
                    setTimeout(function() { ripple.remove(); }, 500);
                });
            });

            // Ripple keyframe (injected once)
            if(!document.getElementById('rippleStyle')) {
                var s = document.createElement('style');
                s.id = 'rippleStyle';
                s.textContent = '@keyframes rippleEffect { to { transform: scale(25); opacity: 0; } }';
                document.head.appendChild(s);
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
