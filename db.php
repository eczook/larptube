<?php
// Database configuration
$host = 'sql308.infinityfree.com';
$db = 'if0_41027000_larptube'; // Note: Added prefix to match InfinityFree convention
$user = 'if0_41027000';
$pass = 'JdX4tbpNObk0wU';

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Create PDO connection with charset
    $pdo = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
    
    // Optional: Test connection
    // echo "Connected successfully";
    
} catch (PDOException $e) {
    // Log error instead of displaying directly (security)
    error_log("Connection failed: " . $e->getMessage());
    
    // Show user-friendly message
    die("Unable to connect to database. Please try again later.");
}
?>