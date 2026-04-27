<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Destiny Scanner | Cyberpunk Palm Reader</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        body { background: #000; color: #0f0; font-family: 'Orbitron', sans-serif; overflow: hidden; }
        .scanner-page { position: relative; width: 100vw; height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center; }
        
        #videoFeed { width: 100%; height: 100%; object-fit: cover; position: absolute; top: 0; left: 0; opacity: 0.6; filter: sepia(1) hue-rotate(90deg) brightness(0.8); }
        
        .hand-guide { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 300px; height: 400px; border: 2px dashed #0f0; border-radius: 50% 50% 10px 10px; z-index: 5; pointer-events: none; box-shadow: 0 0 20px rgba(0,255,0,0.2); }
        .hand-guide::before { content: '✋ PLACE PALM HERE'; position: absolute; top: -40px; width: 100%; text-align: center; color: #0f0; font-weight: bold; font-size: 1.2rem; }

        .laser { position: absolute; top: 20%; left: 0; width: 100%; height: 5px; background: #0f0; box-shadow: 0 0 20px #0f0, 0 0 40px #0f0; z-index: 10; display: none; }
        @keyframes scan { 0% { top: 20%; } 50% { top: 80%; } 100% { top: 20%; } }
        .scanning .laser { display: block; animation: scan 2s linear infinite; }

        .ui-overlay { position: absolute; top: 20px; left: 20px; z-index: 20; }
        .back-btn { background: rgba(0,255,0,0.1); border: 2px solid #0f0; color: #0f0; padding: 10px 20px; cursor: pointer; font-family: 'Orbitron'; }

        .scan-btn { position: absolute; bottom: 50px; z-index: 20; background: transparent; border: 4px solid #0f0; color: #0f0; padding: 20px 40px; font-size: 1.5rem; cursor: pointer; transition: 0.3s; box-shadow: 0 0 15px #0f0; }
        .scan-btn:hover { background: #0f0; color: #000; box-shadow: 0 0 30px #0f0; }

        .result-modal { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%) scale(0.8); background: rgba(0,20,0,0.9); border: 3px solid #0f0; padding: 40px; border-radius: 10px; z-index: 1000; display: none; text-align: center; max-width: 500px; width: 90%; box-shadow: 0 0 50px #0f0; }
        .result-modal.active { display: block; animation: popIn 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards; }
        @keyframes popIn { to { transform: translate(-50%, -50%) scale(1); } }

        .res-cat { font-size: 0.8rem; opacity: 0.7; margin-top: 20px; text-transform: uppercase; }
        .res-val { font-size: 1.2rem; color: #fff; text-shadow: 0 0 10px #0f0; margin-bottom: 10px; }

        .data-stream { position: absolute; right: 20px; top: 20px; width: 200px; height: 300px; font-size: 0.7rem; color: #0f0; opacity: 0.5; overflow: hidden; pointer-events: none; }
    </style>
</head>
<body>
    <div class="scanner-page" id="page">
        <video id="videoFeed" autoplay playsinline></video>
        <div class="hand-guide"></div>
        <div class="laser"></div>

        <div class="ui-overlay">
            <button class="back-btn" onclick="location.href='index.php'">< RETURN TO REALITY</button>
        </div>

        <div class="data-stream" id="dataStream"></div>

        <button class="scan-btn" id="scanBtn" onclick="startScan()">INITIATE DESTINY SCAN</button>

        <div id="resultModal" class="result-modal">
            <h1 style="color: #0f0; margin-bottom: 30px;">DESTINY DOWNLOADED</h1>
            
            <div class="res-cat">Career Trajectory</div>
            <div class="res-val" id="resCareer">...</div>
            
            <div class="res-cat">Love Protocol</div>
            <div class="res-val" id="resLove">...</div>
            
            <div class="res-cat">Probability of Bunking</div>
            <div class="res-val" id="resLuck">...</div>

            <button class="scan-btn" style="position: static; margin-top: 30px; font-size: 1rem;" onclick="location.reload()">RE-SCAN LINEAGE</button>
        </div>
    </div>

    <script>
        const video = document.getElementById('videoFeed');
        const streamContainer = document.getElementById('dataStream');

        async function initCamera() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' }, audio: false });
                video.srcObject = stream;
            } catch (err) { alert("Camera failed. Destiny is obscured."); location.href='index.php'; }
        }

        // Random data stream effect
        setInterval(() => {
            const line = document.createElement('div');
            line.innerText = `> ${Math.random().toString(16).substring(2, 10).toUpperCase()} ANALYZING...`;
            streamContainer.prepend(line);
            if(streamContainer.childNodes.length > 20) streamContainer.removeChild(streamContainer.lastChild);
        }, 100);

        function startScan() {
            document.getElementById('page').classList.add('scanning');
            document.getElementById('scanBtn').style.display = 'none';
            
            // Audio-like feedback would go here
            setTimeout(() => {
                showResults();
            }, 3000); // 3 seconds of scanning drama
        }

        function showResults() {
            document.getElementById('page').classList.remove('scanning');
            
            const careers = [
                "You will become a CEO... of a WhatsApp meme group.",
                "Your resume will be used as a 'How-to' guide for backbenchers.",
                "You are destined to become a professional napper in corporate offices.",
                "High chance of becoming a billionaire (in GTA only).",
                "Your future boss is currently failing their exams too."
            ];
            
            const loves = [
                "Error 404: Not Found. Try again after placements.",
                "Your soulmate is currently blocking you on Instagram.",
                "100% compatibility with 'Cold Coffee' and 'Sleep'.",
                "Your crush liked your photo from 2019. It's a sign (of stalking).",
                "You will find love in the college canteen, likely over a samosa."
            ];
            
            const lucks = [
                "99% chance of bunking tomorrow's first lecture.",
                "Luck is high! You will find a 10 rupee note in your old jeans.",
                "Probability of getting caught by the HOD: UNCERTAIN.",
                "A proxy is in your future. Use it wisely.",
                "Your luck is linked to your battery percentage. Charge up!"
            ];

            document.getElementById('resCareer').innerText = careers[Math.floor(Math.random()*careers.length)];
            document.getElementById('resLove').innerText = loves[Math.floor(Math.random()*loves.length)];
            document.getElementById('resLuck').innerText = lucks[Math.floor(Math.random()*lucks.length)];

            document.getElementById('resultModal').classList.add('active');
        }

        initCamera();
    </script>
</body>
</html>
