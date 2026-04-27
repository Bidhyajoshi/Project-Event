<?php
session_start();
$pdo = require 'db.php';

// Initialize the table if it doesn't exist
$pdo->exec("CREATE TABLE IF NOT EXISTS webrtc_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    caller_id VARCHAR(50) NOT NULL,
    callee_id VARCHAR(50) DEFAULT NULL,
    caller_signal TEXT DEFAULT NULL,
    callee_signal TEXT DEFAULT NULL,
    status VARCHAR(20) DEFAULT 'waiting',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;");

// Clear old sessions (older than 10 minutes)
$pdo->exec("DELETE FROM webrtc_sessions WHERE created_at < NOW() - INTERVAL 10 MINUTE");

$action = $_POST['action'] ?? '';
$myId = session_id();

header('Content-Type: application/json');

if ($action === 'join') {
    // Check if I am already in a session
    $stmt = $pdo->prepare("SELECT id, status FROM webrtc_sessions WHERE caller_id = ? OR callee_id = ? ORDER BY id DESC LIMIT 1");
    $stmt->execute([$myId, $myId]);
    $session = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($session && $session['status'] !== 'closed') {
        echo json_encode(['success' => true, 'session_id' => $session['id'], 'role' => ($session['caller_id'] === $myId ? 'caller' : 'callee')]);
        exit;
    }

    // Try to find a waiting partner
    $stmt = $pdo->prepare("SELECT id FROM webrtc_sessions WHERE status = 'waiting' AND caller_id != ? LIMIT 1");
    $stmt->execute([$myId]);
    $waiting = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($waiting) {
        // Join as callee
        $stmt = $pdo->prepare("UPDATE webrtc_sessions SET callee_id = ?, status = 'paired' WHERE id = ?");
        $stmt->execute([$myId, $waiting['id']]);
        echo json_encode(['success' => true, 'session_id' => $waiting['id'], 'role' => 'callee']);
    } else {
        // Create new session as caller
        $stmt = $pdo->prepare("INSERT INTO webrtc_sessions (caller_id, status) VALUES (?, 'waiting')");
        $stmt->execute([$myId]);
        echo json_encode(['success' => true, 'session_id' => $pdo->lastInsertId(), 'role' => 'caller']);
    }
} elseif ($action === 'poll') {
    $sessionId = $_POST['session_id'] ?? 0;
    $stmt = $pdo->prepare("SELECT * FROM webrtc_sessions WHERE id = ?");
    $stmt->execute([$sessionId]);
    $session = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($session) {
        // If other person disconnected
        if ($session['status'] === 'closed') {
             echo json_encode(['status' => 'closed']);
             exit;
        }
        $isCaller = ($session['caller_id'] === $myId);
        
        $mySignal = $isCaller ? $session['caller_signal'] : $session['callee_signal'];
        $peerSignal = $isCaller ? $session['callee_signal'] : $session['caller_signal'];

        echo json_encode([
            'status' => $session['status'],
            'peer_signal' => $peerSignal ? json_decode($peerSignal) : null
        ]);
    } else {
        echo json_encode(['status' => 'error']);
    }
} elseif ($action === 'signal') {
    $sessionId = $_POST['session_id'] ?? 0;
    $signal = $_POST['signal'] ?? ''; // JSON string
    
    $stmt = $pdo->prepare("SELECT caller_id FROM webrtc_sessions WHERE id = ?");
    $stmt->execute([$sessionId]);
    $session = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($session) {
        $field = ($session['caller_id'] === $myId) ? 'caller_signal' : 'callee_signal';
        $stmt = $pdo->prepare("UPDATE webrtc_sessions SET $field = ? WHERE id = ?");
        $stmt->execute([$signal, $sessionId]);
        echo json_encode(['success' => true]);
    }
} elseif ($action === 'skip') {
    $sessionId = $_POST['session_id'] ?? 0;
    $stmt = $pdo->prepare("UPDATE webrtc_sessions SET status = 'closed' WHERE id = ?");
    $stmt->execute([$sessionId]);
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'Invalid action']);
}
