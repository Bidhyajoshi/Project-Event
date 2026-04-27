<?php
session_start();
$pdo = require 'db.php';
header('Content-Type: application/json');

$action = $_POST['action'] ?? 'chat';
$session_id = $_SESSION['user_id'] ?? session_id(); // Use user_id if logged in, fallback to session_id otherwise

// Create table if not exists
$pdo->exec("CREATE TABLE IF NOT EXISTS ai_chats (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    session_id VARCHAR(100), 
    message TEXT, 
    response TEXT, 
    mode VARCHAR(50), 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

if ($action === 'clear') {
    $stmt = $pdo->prepare("DELETE FROM ai_chats WHERE session_id = ?");
    $stmt->execute([$session_id]);
    echo json_encode(['success' => true]);
    exit;
}

if ($action === 'fetch') {
    $stmt = $pdo->prepare("SELECT * FROM ai_chats WHERE session_id = ? ORDER BY id ASC LIMIT 20");
    $stmt->execute([$session_id]);
    $chats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['chats' => $chats]);
    exit;
}

$input = strtolower($_POST['input'] ?? '');
$mode = $_POST['mode'] ?? 'savage';

if (!$input) {
    echo json_encode(['reply' => 'Say something, coward.', 'mode' => 'savage']);
    exit;
}

$reply = "";

function has($word, $str) {
    return strpos($str, $word) !== false;
}

if ($mode === 'savage') {
    if (has('exam', $input)) {
        $reply = "You should have tried studying instead of bothering a piece of silicon, Kevin.";
    } elseif (has('propose', $input)) {
        $reply = "Proposing? That's a brave way of choosing public humiliation.";
    } elseif (has('crush', $input)) {
        $reply = "Your crush thinks you are the IT support guy. Give up.";
    } elseif (has('money', $input)) {
        $reply = "Check your bank account. Then look in the mirror. Both are depressing.";
    } elseif (has('job', $input)) {
        $reply = "AI is taking your job anyway. Learn to farm.";
    } elseif (has('attendance', $input)) {
        $reply = "You show up to class less than my dad showed up to my birthdays.";
    } else {
        $reply = "Your words are as empty as your future.";
    }
} elseif ($mode === 'damage') {
    if (has('exam', $input)) {
        $reply = "Even if you pass the exam, you'll still fail at life.";
    } elseif (has('propose', $input) || has('crush', $input)) {
        $reply = "You really think someone could love that face? Honestly?";
    } elseif (has('money', $input)) {
        $reply = "Poverty is temporary, but your lack of talent is permanent.";
    } elseif (has('job', $input)) {
        $reply = "You're fighting for a job that barely pays enough to feed a dog.";
    } elseif (has('attendance', $input)) {
        $reply = "No one noticed you were absent. They probably preferred it.";
    } else {
        $reply = "Wait, you have a future to overthink? Delusional.";
    }
} elseif ($mode === 'brainless') {
    if (has('exam', $input)) {
        $reply = "Step 1: Become a goat. Goats don't take exams.";
    } elseif (has('propose', $input) || has('crush', $input)) {
        $reply = "Have you tried screaming at a pigeon? Very romantic.";
    } elseif (has('money', $input)) {
        $reply = "Just print more money using a toaster.";
    } elseif (has('job', $input)) {
        $reply = "Become a professional cloud watcher. Minimum wage: 3 raindrops.";
    } elseif (has('attendance', $input)) {
        $reply = "If you eat the attendance register, you can't be marked absent.";
    } else {
        $reply = "Potato chips are just angry potatoes.";
    }
}

$stmt = $pdo->prepare("INSERT INTO ai_chats (session_id, message, response, mode) VALUES (?, ?, ?, ?)");
$stmt->execute([$session_id, $input, $reply, $mode]);

echo json_encode(['reply' => $reply, 'mode' => $mode]);
