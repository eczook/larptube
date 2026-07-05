<?php
require_once 'config.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$forum_id = isset($_GET['forum']) ? $_GET['forum'] : null;

if(!$forum_id) {
    header("Location: index.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM forums WHERE id = ?");
$stmt->execute([$forum_id]);
$forum = $stmt->fetch();

if(!$forum) {
    header("Location: index.php");
    exit();
}

$page_title = 'Create Thread';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id'];
    
    $stmt = $pdo->prepare("INSERT INTO threads (forum_id, user_id, title) VALUES (?, ?, ?)");
    $stmt->execute([$forum_id, $user_id, $title]);
    $thread_id = $pdo->lastInsertId();
    
    $stmt = $pdo->prepare("INSERT INTO replies (thread_id, user_id, content) VALUES (?, ?, ?)");
    $stmt->execute([$thread_id, $user_id, $content]);
    
    header("Location: thread.php?id=$thread_id");
    exit();
}

include 'header.php';
?>

<h1>Create New Thread in <?= htmlspecialchars($forum['name']) ?></h1>

<form method="POST">
    <div class="form-group">
        <label>Thread Title</label>
        <input type="text" name="title" required autofocus>
    </div>
    <div class="form-group">
        <label>Content</label>
        <textarea name="content" required style="min-height: 200px;"></textarea>
    </div>
    <button type="submit" class="btn">Create Thread</button>
    <a href="forum.php?id=<?= $forum_id ?>" style="margin-left: 1rem;">Cancel</a>
</form>

<?php include 'footer.php'; ?>