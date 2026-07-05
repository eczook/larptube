<?php
require_once 'config.php';

if(!isset($_GET['id'])) {
    redirect('index.php');
}

$post_id = $_GET['id'];

$pdo->prepare("UPDATE posts SET views = views + 1 WHERE id = ?")->execute([$post_id]);

$stmt = $pdo->prepare("SELECT p.*, u.username, g.name as group_name, g.id as group_id 
                       FROM posts p 
                       JOIN users u ON p.user_id = u.id 
                       JOIN groups g ON p.group_id = g.id 
                       WHERE p.id = ?");
$stmt->execute([$post_id]);
$post = $stmt->fetch();

if(!$post) {
    redirect('index.php');
}

$page_title = $post['title'];

$stmt = $pdo->prepare("SELECT c.*, u.username 
                       FROM comments c 
                       JOIN users u ON c.user_id = u.id 
                       WHERE c.post_id = ? 
                       ORDER BY c.created_at ASC");
$stmt->execute([$post_id]);
$comments = $stmt->fetchAll();

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment']) && isset($_SESSION['user_id'])) {
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id'];
    
    $stmt = $pdo->prepare("INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)");
    $stmt->execute([$post_id, $user_id, $content]);
    redirect("post.php?id=$post_id");
}

include 'header.php';
?>

<div style="background: white; border: 1px solid #e1e4e8; border-radius: 6px; padding: 1.5rem; margin-bottom: 2rem;">
    <h1><?= htmlspecialchars($post['title']) ?></h1>
    <div style="color: #586069; font-size: 0.875rem; margin-bottom: 1rem;">
        Posted by <?= htmlspecialchars($post['username']) ?> in 
        <a href="group.php?id=<?= $post['group_id'] ?>">/g/ <?= htmlspecialchars($post['group_name']) ?></a> • 
        <?= date('M d, Y H:i', strtotime($post['created_at'])) ?> • 
        <?= $post['views'] ?> views
    </div>
    
    <?php if($post['image']): ?>
        <div style="margin: 1rem 0;">
            <img src="<?= $post['image'] ?>" alt="Post image" style="max-width: 100%; max-height: 400px; border-radius: 6px;">
        </div>
    <?php endif; ?>
    
    <div><?= nl2br(htmlspecialchars($post['content'])) ?></div>
</div>

<h3>Comments (<?= count($comments) ?>)</h3>

<?php if(isset($_SESSION['user_id'])): ?>
    <div style="background: white; border: 1px solid #e1e4e8; border-radius: 6px; padding: 1rem; margin-bottom: 2rem;">
        <form method="POST">
            <div class="form-group">
                <textarea name="content" placeholder="Write a comment..." required style="width: 100%; padding: 0.5rem; border: 1px solid #e1e4e8; border-radius: 6px; min-height: 80px;"></textarea>
            </div>
            <button type="submit" name="comment" class="btn">Post Comment</button>
        </form>
    </div>
<?php else: ?>
    <div class="alert alert-info">
        <a href="login.php">Login</a> to comment
    </div>
<?php endif; ?>

<?php foreach($comments as $comment): ?>
    <div style="background: white; border: 1px solid #e1e4e8; border-radius: 6px; padding: 1rem; margin-bottom: 1rem;">
        <div style="color: #586069; font-size: 0.75rem; margin-bottom: 0.5rem;">
            <?= htmlspecialchars($comment['username']) ?> • 
            <?= date('M d, Y H:i', strtotime($comment['created_at'])) ?>
        </div>
        <div><?= nl2br(htmlspecialchars($comment['content'])) ?></div>
    </div>
<?php endforeach; ?>

<?php include 'footer.php'; ?>