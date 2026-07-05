<?php
$host = 'sql308.infinityfree.com';
$db = 'if0_41027000_larptube'; 
$user = 'if0_41027000';
$pass = 'JdX4tbpNObk0wU';


error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    
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
    

    
} catch (PDOException $e) {
  
    error_log("Connection failed: " . $e->getMessage());
    
  
    die("fuck i couldnt connect tot he databse.");
}
?>
