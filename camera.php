<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Identity Distorter | ANTI-GRAVITY</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        body { background: #000; overflow: hidden; margin: 0; }
        .camera-page { position: relative; width: 100vw; height: 100vh; display: flex; flex-direction: column; justify-content: center; align-items: center; background: #050505; }
        
        .video-container { width: 100%; height: 100%; position: absolute; top: 0; left: 0; z-index: 1; overflow: hidden; display: flex; align-items: center; justify-content: center; }
        #videoFeed { min-width: 100%; min-height: 100%; object-fit: cover; transition: transform 0.1s, border-radius 0.3s; }
        #canvasOverlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 2; pointer-events: none; }
        
        .camera-ui { position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 10; display: flex; flex-direction: column; justify-content: space-between; pointer-events: none; }
        .camera-ui * { pointer-events: auto; }
        
        .cam-header { padding: 20px; display: flex; justify-content: space-between; align-items: center; background: linear-gradient(to bottom, rgba(0,0,0,0.9), transparent); }
        .cam-footer { padding: 20px; background: linear-gradient(to top, rgba(0,0,0,0.9), transparent); }
        
        .filter-container { width: 100%; overflow-x: auto; padding-bottom: 10px; scrollbar-width: none; }
        .filter-grid { display: grid; grid-auto-flow: column; grid-auto-columns: 85px; gap: 10px; padding: 0 15px; }
        
        .filter-item { width: 75px; height: 75px; border-radius: 15px; border: 2px solid rgba(255,255,255,0.3); background: rgba(255,255,255,0.15); backdrop-filter: blur(8px); display: flex; flex-direction: column; align-items: center; justify-content: center; cursor: pointer; transition: 0.2s; font-size: 1.4rem; }
        .filter-item:hover { transform: translateY(-5px); border-color: #00f2ff; }
        .filter-item.active { border-color: #ff007f; background: rgba(255, 0, 127, 0.2); box-shadow: 0 0 20px rgba(255, 0, 127, 0.4); }
        .filter-name { font-size: 0.6rem; color: #fff; margin-top: 5px; text-transform: uppercase; font-weight: bold; }
        
        .controls { display: flex; justify-content: center; align-items: center; gap: 30px; margin-top: 10px; }
        .intensity-panel { display: flex; flex-direction: column; align-items: center; gap: 5px; background: rgba(0,0,0,0.5); padding: 10px 20px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.1); width: 180px; }
        .intensity-panel label { font-size: 0.7rem; color: #aaa; text-transform: uppercase; letter-spacing: 1px; }
        
        .capture-btn { width: 85px; height: 85px; border-radius: 50%; background: #fff; border: 10px solid rgba(255,255,255,0.2); cursor: pointer; transition: 0.2s; box-shadow: 0 0 30px rgba(255,255,255,0.3); }
        .capture-btn:active { transform: scale(0.85); box-shadow: 0 0 10px #fff; }
        
        .chaos-btn { background: #ff007f; color: #fff; padding: 10px 20px; border: none; border-radius: 25px; font-weight: bold; cursor: pointer; font-size: 0.8rem; letter-spacing: 1px; }

        /* Animations */
        @keyframes shake { 0% { transform: translate(1px, 1px) rotate(0deg); } 10% { transform: translate(-1px, -2px) rotate(-1deg); } 20% { transform: translate(-3px, 0px) rotate(1deg); } 30% { transform: translate(3px, 2px) rotate(0deg); } 40% { transform: translate(1px, -1px) rotate(1deg); } 50% { transform: translate(-1px, 2px) rotate(-1deg); } 60% { transform: translate(-3px, 1px) rotate(0deg); } 70% { transform: translate(3px, 1px) rotate(-1deg); } 80% { transform: translate(-1px, -1px) rotate(1deg); } 90% { transform: translate(1px, 2px) rotate(0deg); } 100% { transform: translate(1px, -2px) rotate(-1deg); } }
        .shaking { animation: shake 0.1s infinite !important; }

        .modal { position: fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.98); z-index: 1000; display:none; flex-direction: column; justify-content: center; align-items: center; }
        #previewImg { max-width: 85%; max-height: 70%; border-radius: 15px; border: 4px solid #fff; }
        
        .toast { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: #ff007f; color: #fff; padding: 20px 40px; border-radius: 10px; font-weight: bold; z-index: 2000; display: none; font-size: 1.5rem; text-align: center; }
    </style>
</head>
<body>
    <div class="camera-page">
        <div class="video-container" id="videoCont">
            <video id="videoFeed" autoplay playsinline></video>
        </div>
        <canvas id="canvasOverlay"></canvas>

        <div class="camera-ui">
            <header class="cam-header">
                <button class="chaos-btn" style="background: #333;" onclick="location.href='index.php'">EXIT CLOWNER</button>
                <button class="chaos-btn" id="chaosBtn" onclick="toggleChaos()">ACTIVATE CHAOS 🌀</button>
            </header>

            <footer class="cam-footer">
                <div class="filter-container">
                    <div class="filter-grid" id="filterCarousel">
                        <div class="filter-item active" data-filter="none" data-icon="✨"><span class="filter-name">Normal</span></div>
                        <div class="filter-item" data-filter="squash" data-icon="🥞"><span class="filter-name">Squash</span></div>
                        <div class="filter-item" data-filter="stretch" data-icon="🦒"><span class="filter-name">Stretch</span></div>
                        <div class="filter-item" data-filter="spiral" data-icon="🌀"><span class="filter-name">Spiral</span></div>
                        <div class="filter-item" data-filter="fisheye" data-icon="👁️"><span class="filter-name">Fish Eye</span></div>
                        <div class="filter-item" data-filter="angry" data-icon="🤬"><span class="filter-name">Angry Prof</span></div>
                        <div class="filter-item" data-filter="ghost" data-icon="👻"><span class="filter-name">Ghost</span></div>
                        <div class="filter-item" data-filter="hacker" data-icon="📟"><span class="filter-name">Hacker</span></div>
                        <div class="filter-item" data-filter="alien" data-icon="👽"><span class="filter-name">Alien</span></div>
                        <div class="filter-item" data-filter="potato" data-icon="🥔"><span class="filter-name">Potato</span></div>
                        <div class="filter-item" data-filter="wasted" data-icon="💀"><span class="filter-name">Wasted</span></div>
                        <div class="filter-item" data-filter="bubble" data-icon="🫧"><span class="filter-name">Bubble</span></div>
                        <div class="filter-item" data-filter="invert" data-icon="🎭"><span class="filter-name">Invert</span></div>
                    </div>
                </div>

                <div class="controls">
                    <div class="intensity-panel">
                        <label>Chaos Level: <span id="intVal">50</span>%</label>
                        <input type="range" id="intensity" min="1" max="100" value="50" style="width: 100%;" oninput="updateIntensity(this.value)">
                    </div>
                    <button class="capture-btn" id="captureBtn" onclick="takePhoto()"></button>
                    <div style="width: 180px; display: flex; justify-content: flex-end;">
                        <button class="chaos-btn" style="background: #444;" onclick="location.reload()">REFRESH</button>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Preview Modal -->
    <div id="previewModal" class="modal">
        <h1 style="color: #fff;">SAVAGE SHOT 📸</h1>
        <img id="previewImg" src="">
        <div style="display: flex; gap: 20px; margin-top: 30px;">
            <button class="chaos-btn" onclick="downloadPhoto()">💾 SAVE TO GALLERY</button>
            <button class="chaos-btn" style="background: #444;" onclick="closePreview()">DISCARD</button>
        </div>
    </div>

    <div id="toast" class="toast">PHOTO CLOWNED! 🤡</div>

    <script>
        const video = document.getElementById('videoFeed');
        const videoCont = document.getElementById('videoCont');
        const canvas = document.getElementById('canvasOverlay');
        const ctx = canvas.getContext('2d');
        const carousel = document.getElementById('filterCarousel');
        let currentFilter = 'none';
        let intensity = 0.5;
        let chaosInterval = null;

        async function initCamera() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' }, audio: false });
                video.srcObject = stream;
                requestAnimationFrame(renderLoop);
            } catch (err) { alert("Camera access denied! Destiny obscured."); }
        }

        function resizeCanvas() { canvas.width = window.innerWidth; canvas.height = window.innerHeight; }
        window.addEventListener('resize', resizeCanvas);
        resizeCanvas();

        function updateIntensity(val) {
            intensity = val / 100;
            document.getElementById('intVal').innerText = val;
            applyTransforms();
        }

        function applyTransforms() {
            video.style.transform = '';
            video.style.filter = '';
            video.style.borderRadius = '0';
            video.classList.remove('shaking');

            const s = intensity * 2; // Multiplier for chaos

            switch(currentFilter) {
                case 'squash': video.style.transform = `scale(${1 + s}, ${1 - intensity})`; break;
                case 'stretch': video.style.transform = `scale(${1 - intensity}, ${1 + s})`; break;
                case 'spiral': video.style.transform = `rotate(${intensity * 360}deg) skew(${intensity * 45}deg)`; break;
                case 'fisheye': 
                    video.style.borderRadius = `${intensity * 100}%`;
                    video.style.filter = `saturate(${1 + intensity * 5}) contrast(${1 + intensity * 2})`;
                    break;
                case 'angry': 
                    video.classList.add('shaking');
                    video.style.filter = `sepia(1) hue-rotate(-50deg) saturate(${intensity * 10})`;
                    break;
                case 'ghost': video.style.filter = `opacity(${1 - intensity * 0.8}) blur(${intensity * 20}px)`; break;
                case 'hacker': video.style.filter = `blur(${intensity * 5}px) contrast(${100 + intensity * 400}%) hue-rotate(90deg)`; break;
                case 'alien': video.style.transform = `scaleY(${1 + intensity * 2}) translateY(-${intensity * 10}%)`; video.style.filter = `hue-rotate(120deg)`; break;
                case 'potato': video.style.transform = `scaleX(0.7)`; video.style.filter = `sepia(${intensity})`; break;
                case 'wasted': video.style.filter = `grayscale(1) contrast(1.5) blur(${intensity * 2}px)`; break;
                case 'invert': video.style.filter = `invert(${intensity})`; break;
                case 'bubble': video.style.filter = `contrast(1.5) brightness(1.2)`; break;
            }
        }

        function renderLoop() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            // Draw any additional canvas overlays here if needed
            requestAnimationFrame(renderLoop);
        }

        carousel.addEventListener('click', (e) => {
            const item = e.target.closest('.filter-item');
            if (item) {
                document.querySelectorAll('.filter-item').forEach(el => el.classList.remove('active'));
                item.classList.add('active');
                currentFilter = item.dataset.filter;
                applyTransforms();
            }
        });

        function toggleChaos() {
            if (chaosInterval) { clearInterval(chaosInterval); chaosInterval = null; document.getElementById('chaosBtn').innerText = "ACTIVATE CHAOS 🌀"; }
            else { document.getElementById('chaosBtn').innerText = "STOP CHAOS 🛑"; chaosInterval = setInterval(() => { const items = document.querySelectorAll('.filter-item'); items[Math.floor(Math.random() * items.length)].click(); }, 300); }
        }

        function takePhoto() {
            const saveCanvas = document.createElement('canvas');
            const rect = video.getBoundingClientRect();
            saveCanvas.width = video.videoWidth;
            saveCanvas.height = video.videoHeight;
            const sctx = saveCanvas.getContext('2d');

            // Apply all current CSS filters and transforms manually to canvas
            const computed = getComputedStyle(video);
            sctx.filter = computed.filter;
            
            // Handle transforms manually
            sctx.translate(saveCanvas.width/2, saveCanvas.height/2);
            const matrix = new DOMMatrix(computed.transform);
            sctx.transform(matrix.a, matrix.b, matrix.c, matrix.d, 0, 0);
            sctx.translate(-saveCanvas.width/2, -saveCanvas.height/2);

            sctx.drawImage(video, 0, 0, saveCanvas.width, saveCanvas.height);
            
            document.getElementById('previewImg').src = saveCanvas.toDataURL('image/png');
            document.getElementById('previewModal').style.display = 'flex';
        }

        function closePreview() { document.getElementById('previewModal').style.display = 'none'; }
        function downloadPhoto() {
            const link = document.createElement('a'); link.download = `clowned_${Date.now()}.png`;
            link.href = document.getElementById('previewImg').src; link.click();
            showToast(); closePreview();
        }
        function showToast() { const t = document.getElementById('toast'); t.style.display = 'block'; setTimeout(() => t.style.display = 'none', 3000); }

        initCamera();
    </script>
</body>
</html>
