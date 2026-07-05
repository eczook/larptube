<?php
require_once 'config.php';

if(!isset($_SESSION['user_id'])) {
    redirect('login.php');
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $group_id = $_POST['group_id'];
    $title = $_POST['title'];
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id'];
    
    $image_path = null;
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if(in_array($ext, $allowed)) {
            if(!file_exists('uploads')) {
                mkdir('uploads', 0777, true);
            }
            $image_path = 'uploads/' . time() . '_' . basename($filename);
            move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
        }
    }
    
    $stmt = $pdo->prepare("INSERT INTO posts (group_id, user_id, title, content, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$group_id, $user_id, $title, $content, $image_path]);
    
    redirect("group.php?id=$group_id");
}
?>