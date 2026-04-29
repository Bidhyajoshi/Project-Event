<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$pdo = require 'db.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $avatar = $_POST['avatar'] ?? '😎';
    $year = $_POST['year'] ?? '1';

    if (empty($username) || empty($email) || empty($password)) {
        $error = "All fields are required!";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $error = "Username or Email already exists!";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password, avatar_type, year) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$username, $hashedPassword, $avatar, $year])) {
                $_SESSION['user_id'] = $pdo->lastInsertId();
                $_SESSION['username'] = $username;
                header("Location: dashboard.php");
                exit;
            } else {
                $error = "Registration failed!";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register | BAWAAL</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .auth-container { height: 100vh; display: flex; align-items: center; justify-content: center; z-index: 10; position: relative; }
        .auth-card { max-width: 500px; width: 90%; text-align: center; }
        .error-msg { color: #ff0040; margin-bottom: 15px; font-size: 1.2rem; text-shadow: 0 0 5px #ff0000; }
        .step { display: none; }
        .step.active { display: block; animation: popIn 0.5s; }
        .avatar-grid { display: flex; gap: 10px; justify-content: center; margin-bottom: 20px;}
        .avatar-card { padding: 20px; background: rgba(255,255,255,0.3); border-radius: 20px; cursor: pointer; font-size: 1.5rem; transition: 0.3s; color: #fff; text-shadow: 1px 1px 0 #000; }
        .avatar-card:hover, .avatar-card.selected { background: rgba(255,255,255,0.8); color: #000; transform: scale(1.1); }
        .struggle-btn { display: block; width: 100%; margin-bottom: 15px; font-size: 1.2rem; }
    </style>
</head>
<body>
    <div class="bg-gradient" id="bgGradient"></div>
    
    <div class="auth-container">
        <form class="glass-card auth-card" method="POST" id="regForm">
            <h1 class="bawaal-glitch" data-text="BAWAAL" style="font-size: 4rem; margin-bottom: 20px;">BAWAAL</h1>
            <?php if ($error): ?>
                <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <input type="hidden" name="avatar" id="avatarInput" value="😎">
            <input type="hidden" name="year" id="yearInput" value="1">
            
            <div class="step active" id="step1">
                <h2>Step 1: Identity</h2>
                <input type="text" name="username" class="glass-input" placeholder="Username (Earth Name)" required>
                <input type="email" name="email" class="glass-input" placeholder="Email" required>
                <input type="password" name="password" class="glass-input" placeholder="Password" required>
                <button type="button" class="glow-btn" style="width: 100%;" onclick="nextStep(2)">Next ➔</button>
                <a href="login.php" style="color:#00ffff; display:block; margin-top:15px; text-decoration:none;">Already a legend? Login</a>
            </div>
            
            <div class="step" id="step2">
                <h2>Step 2: Choose Avatar</h2>
                <div class="avatar-grid">
                    <div class="avatar-card" onclick="selectAvatar('🤓', this)">🤓 Topper</div>
                    <div class="avatar-card" onclick="selectAvatar('😎', this)">😎 Backbencher</div>
                    <div class="avatar-card" onclick="selectAvatar('👻', this)">👻 Ghost</div>
                </div>
            </div>
            
            <div class="step" id="step3">
                <h2>Step 3: Choose Struggle</h2>
                <button type="button" class="glow-btn struggle-btn" onclick="finishReg(1)">Year 1 (Clueless)</button>
                <button type="button" class="glow-btn struggle-btn" onclick="finishReg(2)">Year 2 (Depressed)</button>
                <button type="button" class="glow-btn struggle-btn" onclick="finishReg(3)">Year 3 (Dead Inside)</button>
                <button type="button" class="glow-btn struggle-btn" onclick="finishReg(4)">Year 4 (Let me out)</button>
            </div>
        </form>
    </div>
    
    <script>
        function nextStep(step) {
            document.querySelectorAll('.step').forEach(e => e.classList.remove('active'));
            document.getElementById('step' + step).classList.add('active');
        }
        function selectAvatar(icon, el) {
            document.getElementById('avatarInput').value = icon;
            document.querySelectorAll('.avatar-card').forEach(c => c.classList.remove('selected'));
            el.classList.add('selected');
            setTimeout(() => nextStep(3), 300);
        }
        function finishReg(year) {
            document.getElementById('yearInput').value = year;
            document.getElementById('regForm').submit();
        }
    </script>
</body>
</html>
