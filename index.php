<?php
session_start();
$pdo = require 'db.php';

$isLoggedIn = isset($_SESSION['user_id']);

if (!$isLoggedIn && !isset($_GET['registered'])) {
    header("Location: gate.php");
    exit();
}

if ($isLoggedIn) {
    updateActivity($pdo);
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
</head>

<body>

<div class="bg-gradient" id="bgGradient"></div>
<div id="floatingEmojis"></div>

<nav class="top-nav">
    <a href="login.php" class="login-btn">Login</a>
    <a href="register.php" class="reg-btn">Join Chaos</a>
</nav>

<?php if (!$isLoggedIn): ?>

<!-- NOT LOGGED IN VIEW -->
<div class="hero">
    <div class="welcome-text">WELCOME TO</div>
    <h1 class="bawaal-glitch" data-text="BAWAAL" style="font-size: 8rem;">BAWAAL</h1>
    <p class="about-text">
        The ultimate digital playground for students. Create excuses, decide your bunks, and talk to strangers—all in one place.
    </p>
    <a href="register.php" class="arcade-btn" style="padding: 20px 40px; text-decoration:none; margin-top:20px; display:inline-block; font-size:2rem;">
        ENTER THE CHAOS
    </a>
</div>

<?php else: ?>

<!-- DASHBOARD -->
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
        <div class="glass-card tool" onclick="location.href='camera.php'">
            <div class="icon">📸</div>
            <h2>Identity Distorter</h2>
        </div>
        <div class="glass-card tool" onclick="location.href='destiny.php'">
            <div class="icon">🖐️</div>
            <h2>Destiny Scanner</h2>
        </div>
    </div>
</div>

<?php endif; ?>

<script src="script.js"></script>
</body>
</html>