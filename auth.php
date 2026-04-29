<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$pdo = require 'db.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'login') {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($username) || empty($password)) {
            $error = "All fields required for login!";
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
                $error = "Invalid credentials. Are you an imposter?";
            }
        }
    } elseif ($action === 'register') {
        $username = trim($_POST['reg_username'] ?? '');
        $password = $_POST['reg_password'] ?? '';
        $email = $username . "@bawaal.local"; // Placeholder email to satisfy DB schema
        $avatar = $_POST['avatar'] ?? '😎';
        $year = '1'; // Default year to satisfy schema
        
        if (empty($username) || empty($password)) {
            $error = "Name and password are required!";
        } else {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                $error = "Name already taken! Be more original.";
            } else {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (username, password, avatar_type, year) VALUES (?, ?, ?, ?)");
                if ($stmt->execute([$username, $hashed, $avatar, $year])) {
                    $_SESSION['user_id'] = $pdo->lastInsertId();
                    $_SESSION['username'] = $username;
                    header("Location: dashboard.php");
                    exit;
                } else {
                    $error = "System error. Chaos rejected you.";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gateway | BAWAAL</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .auth-wrapper { height: 100vh; display: flex; align-items: center; justify-content: center; position: relative; z-index: 10; flex-direction: column; }
        .auth-container { max-width: 500px; width: 90%; position: relative; }
        .auth-card { background: rgba(0,0,0,0.6); backdrop-filter: blur(20px); border: 2px solid #ff00ff; border-radius: 30px; padding: 40px; box-shadow: 0 0 30px rgba(255,0,255,0.4); text-align: center; }
        .error-msg { color: #ff0040; margin-bottom: 20px; font-size: 1.2rem; text-shadow: 0 0 5px #ff0000; }
        
        .toggle-box { display: flex; background: rgba(255,255,255,0.1); border-radius: 50px; margin-bottom: 30px; position: relative; overflow: hidden; border: 1px solid rgba(255,255,255,0.2); }
        .toggle-btn { flex: 1; padding: 15px; font-size: 1.5rem; color: #fff; cursor: pointer; text-align: center; position: relative; z-index: 2; transition: 0.3s; }
        .toggle-btn.active { color: #000; font-weight: bold; text-shadow: none; }
        .toggle-slider { position: absolute; top: 0; left: 0; width: 50%; height: 100%; background: #00ffff; border-radius: 50px; transition: 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55); z-index: 1; box-shadow: 0 0 15px #00ffff; }
        
        .form-section { display: none; }
        .form-section.active { display: block; animation: fadeIn 0.5s; }
        @keyframes fadeIn { from{opacity:0; transform:translateY(10px)} to{opacity:1; transform:translateY(0)} }
        
        .step { display: none; }
        .step.active { display: block; animation: fadeIn 0.5s; }
    </style>
</head>
<body>
    <div class="bg-gradient" id="bgGradient"></div>
    <div id="floatingEmojis"></div>

    <div class="auth-wrapper">
        <h1 class="bawaal-glitch" data-text="BAWAAL" style="font-size: 5rem; margin-bottom: 30px;">BAWAAL</h1>
        
        <div class="auth-container">
            <div class="auth-card">
                <?php if ($error): ?>
                    <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <div class="toggle-box">
                    <div class="toggle-slider" id="toggleSlider"></div>
                    <div class="toggle-btn active" onclick="switchTab('login')">LOGIN</div>
                    <div class="toggle-btn" onclick="switchTab('register')">REGISTER</div>
                </div>

                <!-- LOGIN FORM -->
                <form id="loginForm" class="form-section active" method="POST">
                    <input type="hidden" name="action" value="login">
                    <input type="text" name="username" class="glass-input" placeholder="Earth Name" required>
                    <input type="password" name="password" class="glass-input" placeholder="Password" required>
                    <button type="submit" class="glow-btn" style="width:100%; background:linear-gradient(45deg, #00ffff, #004d40); box-shadow:0 10px 0 #002d26, 0 10px 20px rgba(0,255,255,0.5);">ENTER SIMULATION</button>
                </form>

                <!-- REGISTER FORM (Multi-Step) -->
                <form id="registerForm" class="form-section" method="POST">
                    <input type="hidden" name="action" value="register">
                    <input type="hidden" name="avatar" id="avatarInput" value="😎">
                    
                    <div class="step active" id="step1">
                        <h2 style="margin-bottom:20px; font-size:2rem; color:#fff; text-shadow:2px 2px 0 #000;">Who Are You?</h2>
                        <input type="text" name="reg_username" class="glass-input" placeholder="Pick a cool name" required>
                        <input type="password" name="reg_password" class="glass-input" placeholder="Create Password" required>
                        <button type="button" class="glow-btn" style="width:100%;" onclick="nextStep(2)">NEXT ➔</button>
                    </div>
                    
                    <div class="step" id="step2">
                        <h2 style="margin-bottom:20px; font-size:2rem; color:#fff; text-shadow:2px 2px 0 #000;">Select Avatar</h2>
                        <div class="onboard-card-grid" style="margin-bottom:30px;">
                            <div class="onboard-card" onclick="selectAvatar('🤓', this)">
                                <div class="card-icon">🤓</div>
                                <div class="card-name">Topper</div>
                            </div>
                            <div class="onboard-card" onclick="selectAvatar('😎', this)">
                                <div class="card-icon">😎</div>
                                <div class="card-name">Backbencher</div>
                            </div>
                        </div>
                        <div style="display:flex; gap:10px;">
                            <button type="button" class="arcade-btn" style="padding:15px; font-size:1.2rem; background:#333; box-shadow:0 5px 0 #111;" onclick="nextStep(1)">⬅️ BACK</button>
                            <button type="submit" class="glow-btn" style="flex:1;">JOIN CHAOS ➔</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function switchTab(tab) {
            document.querySelectorAll('.form-section').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.toggle-btn').forEach(el => el.classList.remove('active'));
            
            if(tab === 'login') {
                document.getElementById('loginForm').classList.add('active');
                document.querySelectorAll('.toggle-btn')[0].classList.add('active');
                document.getElementById('toggleSlider').style.left = '0';
                document.getElementById('toggleSlider').style.background = '#00ffff';
                document.getElementById('toggleSlider').style.boxShadow = '0 0 15px #00ffff';
            } else {
                document.getElementById('registerForm').classList.add('active');
                document.querySelectorAll('.toggle-btn')[1].classList.add('active');
                document.getElementById('toggleSlider').style.left = '50%';
                document.getElementById('toggleSlider').style.background = '#ff00ff';
                document.getElementById('toggleSlider').style.boxShadow = '0 0 15px #ff00ff';
            }
        }
        
        function nextStep(step) {
            document.querySelectorAll('.step').forEach(e => e.classList.remove('active'));
            document.getElementById('step' + step).classList.add('active');
        }
        
        function selectAvatar(icon, el) {
            document.getElementById('avatarInput').value = icon;
            document.querySelectorAll('.onboard-card').forEach(c => c.classList.remove('selected'));
            el.classList.add('selected');
        }
    </script>
</body>
</html>
