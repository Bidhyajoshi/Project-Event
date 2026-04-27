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
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = "All fields are required!";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid username or password!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | BAWAAL</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .auth-container { height: 100vh; display: flex; align-items: center; justify-content: center; z-index: 10; position: relative; }
        .auth-card { max-width: 400px; width: 90%; text-align: center; }
        .error-msg { color: #ff0040; margin-bottom: 15px; font-size: 1.2rem; text-shadow: 0 0 5px #ff0000; }
        .auth-link { color: #00ffff; text-decoration: none; margin-top: 15px; display: inline-block; }
    </style>
</head>
<body>
    <div class="bg-gradient" id="bgGradient"></div>
    
    <div class="auth-container">
        <div class="glass-card auth-card">
            <h1 class="bawaal-glitch" data-text="BAWAAL" style="font-size: 4rem; margin-bottom: 20px;">BAWAAL</h1>
            <?php if ($error): ?>
                <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form method="POST">
                <input type="text" name="username" class="glass-input" placeholder="Username" required>
                <input type="password" name="password" class="glass-input" placeholder="Password" required>
                <button type="submit" class="glow-btn" style="width: 100%;">Enter Simulation</button>
            </form>
            <a href="register.php" class="auth-link">New here? Register now.</a>
        </div>
    </div>
</body>
</html>
