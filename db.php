<?php
$host = 'localhost';
$dbname = 'antigravity_db';
$username = 'root'; // Default XAMPP username
$password = '';     // Default XAMPP password

try {
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$dbname` text-align: center;");

    // Create users table
    $tableSql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        avatar_type VARCHAR(50) NOT NULL,
        year VARCHAR(20) NOT NULL,
        streak INT DEFAULT 0,
        last_activity DATETIME DEFAULT CURRENT_TIMESTAMP,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;";
    $pdo->exec($tableSql);

} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Function to update user activity
function updateActivity($pdo) {
    if (isset($_SESSION['user_id'])) {
        $stmt = $pdo->prepare("UPDATE users SET last_activity = NOW() WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
    }
}

// Return the PDO instance
return $pdo;
?>
