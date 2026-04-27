<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BAWAAL | The Student Playground</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .top-nav { position: absolute; top: 20px; right: 30px; z-index: 100; display: flex; gap: 15px; }
        .top-nav a { text-decoration: none; font-size: 1.2rem; padding: 10px 20px; border-radius: 20px; color: #fff; font-weight: bold; border: 2px solid transparent; transition: 0.3s; }
        .top-nav a.login-btn { border-color: #00ffff; text-shadow: 0 0 5px #00ffff; box-shadow: 0 0 10px rgba(0,255,255,0.2); }
        .top-nav a.login-btn:hover { background: #00ffff; color: #000; box-shadow: 0 0 20px #00ffff; text-shadow: none; }
        .top-nav a.reg-btn { background: #ff00ff; box-shadow: 0 0 15px rgba(255,0,255,0.5); }
        .top-nav a.reg-btn:hover { background: #fff; color: #ff00ff; box-shadow: 0 0 30px #ff00ff; }
        
        .hero { min-height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; position: relative; z-index: 10; padding: 20px; }
        .welcome-text { font-size: 2rem; color: #00ffff; letter-spacing: 5px; margin-bottom: -10px; text-shadow: 0 0 10px #00ffff; }
        .about-text { max-width: 600px; font-size: 1.5rem; margin: 30px auto; color: #e0c3fc; text-shadow: 1px 1px 0 #000; line-height: 1.5; }
        
        .locked-dashboard { position: relative; padding: 50px 20px; text-align: center; }
        .locked-overlay { position: absolute; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.6); backdrop-filter: blur(8px); z-index: 20; display: flex; align-items: center; justify-content: center; flex-direction: column; }
        .lock-icon { font-size: 5rem; margin-bottom: 20px; animation: float 3s infinite ease-in-out; }
        .lock-text { font-size: 2.5rem; color: #fff; text-shadow: 0 0 15px #ff00ff; }
        @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-20px)} }
    </style>
</head>
<body>
    <div class="bg-gradient" id="bgGradient"></div>
    <div id="floatingEmojis"></div>
    
    <nav class="top-nav">
        <a href="login.php" class="login-btn">Login</a>
        <a href="register.php" class="reg-btn">Join Chaos</a>
    </nav>

    <div class="hero">
        <div class="welcome-text">WELCOME TO</div>
        <h1 class="bawaal-glitch" data-text="BAWAAL" style="font-size: 8rem;">BAWAAL</h1>
        <p class="about-text">The ultimate digital playground for students. Create excuses, decide your bunks, and talk to strangers—all in one place.</p>
        <a href="register.php" class="arcade-btn" style="padding: 20px 40px; text-decoration:none; margin-top:20px; display:inline-block; font-size:2rem;">ENTER THE CHAOS</a>
    </div>

    <div class="locked-dashboard">
        <div class="locked-overlay">
            <div class="lock-icon">🔒</div>
            <h2 class="lock-text">Login to unlock the chaos!</h2>
        </div>
        <div class="grid" style="opacity: 0.5; filter: grayscale(50%); pointer-events: none;">
            <div class="glass-card tool"><div class="icon">🌐</div><h2>Ome-Gravity</h2></div>
            <div class="glass-card tool"><div class="icon">🤖</div><h2>Savage AI</h2></div>
            <div class="glass-card tool"><div class="icon">🎲</div><h2>Bunk Decision</h2></div>
            <div class="glass-card tool"><div class="icon">🤥</div><h2>Excuse Generator</h2></div>
            <div class="glass-card tool"><div class="icon">🧠</div><h2>Overthinker 3000</h2></div>
        </div>
    </div>
    
    <script src="script.js"></script>
</body>
</html>
