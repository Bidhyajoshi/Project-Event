<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anti-Gravity | Digital Playground</title>
    <meta name="description" content="Cyber-Minimalism meets Chaos in the Anti-Gravity digital playground. Built for Gen-Z.">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Starfield Background -->
    <div id="starfield"></div>

    <!-- Custom Cursor -->
    <div id="custom-cursor"></div>

    <!-- Header / Logo -->
    <header style="position: fixed; top: 40px; left: 50%; transform: translateX(-50%); z-index: 100;">
        <h1 class="logo" id="logo">Antigravity</h1>
    </header>

    <!-- Main Viewport -->
    <main id="gravity-well">
        <div class="glass-card">
            <h2 style="color: var(--electric-cyan); margin-bottom: 1rem;">Welcome to the Void</h2>
            <p style="opacity: 0.8; line-height: 1.6;">Gravity is just a suggestion here. Explore the chaos below.</p>
        </div>
    </main>

    <!-- Navbar Dock -->
    <nav class="navbar-dock">
        <div class="nav-item" onclick="loadFeature('bunk')" title="Bunk">
            <span id="icon-bunk">🎲</span>
        </div>
        <div class="nav-item" onclick="loadFeature('excuses')" title="Excuses">
            <span id="icon-excuses">🤥</span>
        </div>
        <div class="nav-item" onclick="loadFeature('overthink')" title="Overthink">
            <span id="icon-overthink">🧠</span>
        </div>
        <div class="nav-item" onclick="loadFeature('chat')" title="Live Chat">
            <span id="icon-chat">💬</span>
        </div>
    </nav>

    <script src="script.js"></script>
</body>
</html>
