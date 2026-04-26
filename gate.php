<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Gate | Anti-Gravity</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div id="starfield"></div>
    <div id="custom-cursor"></div>

    <div class="auth-container">
        <div class="auth-card" id="authCard">
            <!-- Login Face -->
            <div class="auth-face glass-card">
                <h2 style="color: var(--electric-cyan); margin-bottom: 20px;">Identity Check</h2>
                <form id="loginForm">
                    <input type="text" name="username" placeholder="Drop your digital coordinate" required>
                    <input type="password" name="password" placeholder="Secret code to your brain" required>
                    <button type="submit" class="btn-neon">Access the Void</button>
                </form>
                <div class="toggle-auth" onclick="toggleAuth()">New soul? Enter the Void</div>
            </div>

            <!-- Register Face -->
            <div class="auth-face auth-back glass-card">
                <h2 style="color: var(--neon-violet); margin-bottom: 20px;">Vibe Check</h2>
                <form id="registerForm">
                    <!-- Step 1 -->
                    <div class="form-step active" id="step1">
                        <p style="margin-bottom: 10px;">What should the professors call you?</p>
                        <input type="text" name="reg_username" placeholder="Choose your moniker" id="reg_username">
                        <button type="button" class="btn-neon" onclick="nextStep(2)">Next Level</button>
                    </div>

                    <!-- Step 2 -->
                    <div class="form-step" id="step2">
                        <p style="margin-bottom: 10px;">Secret code to your brain?</p>
                        <input type="password" name="reg_password" placeholder="Don't use 12345, please" id="reg_password">
                        <button type="button" class="btn-neon" onclick="nextStep(3)">Lock it in</button>
                    </div>

                    <!-- Step 3 -->
                    <div class="form-step" id="step3">
                        <p style="margin-bottom: 10px;">Pick your digital skin:</p>
                        <div class="avatar-option" onclick="selectAvatar('Tech Legend')">
                            💻 Tech Legend (M/F/X)
                        </div>
                        <div class="avatar-option" onclick="selectAvatar('Professional Bunker')">
                            😴 Professional Bunker
                        </div>
                        <div class="avatar-option" onclick="selectAvatar('Overthinking Legend')">
                            🌀 Overthinking Legend
                        </div>
                        <input type="hidden" name="avatar_type" id="avatar_type">
                        <button type="button" class="btn-neon" onclick="nextStep(4)">Looking Sharp</button>
                    </div>

                    <!-- Step 4 -->
                    <div class="form-step" id="step4">
                        <p style="margin-bottom: 10px;">Which year are you currently wasting?</p>
                        <select name="year" id="year_select">
                            <option value="1st Year">1st Year (Fresh Meat)</option>
                            <option value="2nd Year">2nd Year (The Realization)</option>
                            <option value="3rd Year">3rd Year (The Breakdown)</option>
                            <option value="4th Year">4th Year (The Final Boss)</option>
                        </select>
                        <button type="submit" class="btn-neon">Initialize Ascension</button>
                    </div>
                </form>
                <div class="toggle-auth" onclick="toggleAuth()">Already encoded? Login</div>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
    <script>
        function toggleAuth() {
            document.getElementById('authCard').classList.toggle('flipped');
        }

        function nextStep(step) {
            // Basic validation
            if (step === 2 && !document.getElementById('reg_username').value) return;
            if (step === 3 && !document.getElementById('reg_password').value) return;
            if (step === 4 && !document.getElementById('avatar_type').value) {
                Swal.fire({
                    icon: 'error',
                    title: 'Pick a skin!',
                    text: 'You can\'t enter the void naked.',
                    background: '#050505',
                    color: '#fff',
                    confirmButtonColor: '#8A2BE2'
                });
                return;
            }

            document.querySelectorAll('.form-step').forEach(s => s.classList.remove('active'));
            document.getElementById('step' + step).classList.add('active');
        }

        function selectAvatar(type) {
            document.getElementById('avatar_type').value = type;
            document.querySelectorAll('.avatar-option').forEach(opt => {
                opt.classList.remove('selected');
                if (opt.innerText.includes(type)) opt.classList.add('selected');
            });
        }

        // AJAX for Login
        document.getElementById('loginForm').onsubmit = async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            formData.append('action', 'login');
            
            const res = await fetch('auth.php', { method: 'POST', body: formData });
            const data = await res.json();

            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Access Granted',
                    text: 'Welcome back to the Void.',
                    timer: 2000,
                    showConfirmButton: false,
                    background: '#050505',
                    color: '#00FFFF'
                }).then(() => window.location.href = 'index.php');
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Access Denied',
                    text: data.message,
                    background: '#050505',
                    color: '#fff'
                });
            }
        };

        // AJAX for Register
        document.getElementById('registerForm').onsubmit = async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            formData.append('action', 'register');
            
            const res = await fetch('auth.php', { method: 'POST', body: formData });
            const data = await res.json();

            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Souls Encoded',
                    text: 'Your digital presence has been initialized.',
                    background: '#050505',
                    color: '#8A2BE2'
                }).then(() => window.location.href = 'index.php');
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Registration Failed',
                    text: data.message,
                    background: '#050505',
                    color: '#fff'
                });
            }
        };
    </script>
</body>
</html>
