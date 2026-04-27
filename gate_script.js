let currentStep = 1;
let characterData = {
    name: '',
    tool: '',
    identity: ''
};

const captions = [
    "Locating brain cells... 0%",
    "Scanning for sanity... 33%",
    "Downloading luck... 66%",
    "Finalizing legendary status... 100%"
];

window.goToStep = (step) => {
    if (step === 2) {
        const name = document.getElementById('username').value.trim();
        if (!name) {
            alert("Whoa! We need a name to address your legend.");
            return;
        }
        characterData.name = name;
    }

    // Hide current step with animation
    const currentEl = document.getElementById(`step${currentStep}`);
    currentEl.style.opacity = '0';
    currentEl.style.transform = 'translateY(-20px)';
    
    setTimeout(() => {
        currentEl.classList.remove('active');
        currentStep = step;
        const nextEl = document.getElementById(`step${currentStep}`);
        nextEl.classList.add('active');
        updateProgress();
    }, 300);
};

window.selectTool = (emoji, name, el) => {
    characterData.tool = name;
    
    // Highlight selection
    document.querySelectorAll('.snap-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    
    // Auto-proceed to step 3 with a slight delay for the 'selected' animation
    setTimeout(() => goToStep(3), 600);
};

window.finishCharacter = (emoji, name, el) => {
    characterData.identity = name;
    
    // Highlight selection
    document.querySelectorAll('.snap-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    
    updateProgress(4); // Final state

    // Show a quick success message
    setTimeout(() => {
        const portal = document.querySelector('.glass-portal');
        portal.innerHTML = `
            <div class="step-container active" style="animation: popIn 0.5s forwards;">
                <h1 class="step-title" style="font-size: 3rem;">WELCOME, ${characterData.name.toUpperCase()}</h1>
                <p class="step-desc">Your journey into the void begins now...</p>
                <div class="snap-emoji" style="font-size: 6rem;">${emoji}</div>
                <div class="progress-caption" style="margin-top: 40px; color: #fff;">REDIRECTING TO DASHBOARD...</div>
            </div>
        `;
        
        console.log("Character Created:", characterData);
        
        setTimeout(() => {
            window.location.href = 'index.php?registered=true';
        }, 2000);
    }, 600);
};

function updateProgress(forceStep = null) {
    const step = forceStep || currentStep;
    const progress = (step - 1) * 33.33;
    document.getElementById('progressBar').style.width = `${progress}%`;
    document.getElementById('progressCaption').innerText = captions[step - 1];
}

// Initial update
updateProgress();
