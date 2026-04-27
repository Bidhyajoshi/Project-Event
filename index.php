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
        .hero { min-height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; position: relative; z-index: 10; padding: 20px; }
        .welcome-text { font-size: 2.5rem; color: #00ffff; letter-spacing: 5px; margin-bottom: -15px; text-shadow: 0 0 10px #00ffff; font-weight: bold; text-transform: uppercase; }
        .about-text { max-width: 800px; font-size: 1.8rem; margin: 40px auto; color: #e0c3fc; text-shadow: 1px 1px 0 #000; line-height: 1.6; }
        .enter-btn { padding: 25px 60px; font-size: 2.5rem; text-decoration: none; border-radius: 50px; animation: pulse-glow 2s infinite; display: inline-block; }
        @keyframes pulse-glow { 0% { box-shadow: 0 0 20px #ff00ff; } 50% { box-shadow: 0 0 50px #ff00ff, 0 0 20px #fff; } 100% { box-shadow: 0 0 20px #ff00ff; } }
    </style>
</head>
<body>
    <div class="bg-gradient" id="bgGradient"></div>
    <div id="floatingEmojis"></div>

    <div class="hero">
        <div class="welcome-text">WELCOME TO</div>
        <h1 class="bawaal-glitch" data-text="BAWAAL" style="font-size: 10rem;">BAWAAL</h1>
        <p class="about-text">The only place where your excuses are smarter than your assignments.<br>Bunk classes, roast your friends, and find your vibe.</p>
        <a href="auth.php" class="arcade-btn enter-btn">ENTER THE CHAOS</a>
    </div>

    <script src="script.js"></script>
</body>
</html>