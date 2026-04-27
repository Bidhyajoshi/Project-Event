<?php
session_start();
$pdo = require 'db.php';

header('Content-Type: application/json');

try {
    // Count users active in the last 5 minutes
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE last_activity > NOW() - INTERVAL 5 MINUTE");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'count' => (int)$result['count']]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
