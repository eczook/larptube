<?php
session_start();

$host = 'sql112.infinityfree.com';
$dbname = 'if0_41580700_oneweb';
$username = 'if0_41580700';
$password = 'EVTTvHrZdAl';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function getUserAvatar($user) {

    if(isset($user['avatar']) && $user['avatar'] && file_exists($user['avatar'])) {
        return $user['avatar'];
    }

    return 'assets/default.png';
}
?>
