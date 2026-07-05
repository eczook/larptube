<?php
require_once 'config.php';

$user_id = isset($_GET['id']) ? $_GET['id'] : null;

if(!$user_id) {
    header("Location: users.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$profile_user = $stmt->fetch();

if(!$profile_user) {
    header("Location: users.php");
    exit();
}

$page_title = $profile_user['username'];

$stmt = $pdo->prepare("SELECT COUNT(*) as thread_count FROM threads WHERE user_id = ?");
$stmt->execute([$user_id]);
$thread_count = $stmt->fetch()['thread_count'];

$stmt = $pdo->prepare("SELECT COUNT(*) as reply_count FROM replies WHERE user_id = ?");
$stmt->execute([$user_id]);
$reply_count = $stmt->fetch()['reply_count'];

$stmt = $pdo->prepare("SELECT t.*, f.name as forum_name 
                       FROM threads t 
                       JOIN forums f ON t.forum_id = f.id 
                       WHERE t.user_id = ? 
                       ORDER BY t.created_at DESC 
                       LIMIT 10");
$stmt->execute([$user_id]);
$recent_threads = $stmt->fetchAll();

include 'header.php';
?>

<style>
    .profile-container {
        display: flex;
        gap: 2rem;
        flex-wrap: wrap;
    }
    
    .profile-sidebar {
        flex: 0 0 250px;
    }
    
    .profile-main {
        flex: 1;
        min-width: 200px;
    }
    
    .profile-avatar-large {
        width: 200px;
        height: 200px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #0366d6;
    }
    
    .profile-avatar-placeholder-large {
        width: 200px;
        height: 200px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2