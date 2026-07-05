<?php
require_once 'config.php';

$forum_id = isset($_GET['id']) ? $_GET['id'] : (isset($_SERVER['PATH_INFO']) ? explode('/', $_SERVER['PATH_INFO'])[1] : null);

if(!$forum_id) {
    redirect('/');
}

$stmt = $pdo->prepare("SELECT f.*, c.name as category_name 
                       FROM forums f 
                       JOIN categories c ON f.category_id = c.id 
                       WHERE f.id = ?");
$stmt->execute([$forum_id]);
$forum = $stmt->fetch();

if(!$forum) {
    redirect('/');
}

$page_title = $forum['name'];

$stmt = $pdo->prepare("SELECT t.*, u.username, COUNT(r.id) as reply_count 
                       FROM threads t 
                       JOIN users u ON t.user_id = u.id 
                       LEFT JOIN replies r ON t.id = r.thread_id 
                       WHERE t.forum_id = ? 
                       GROUP BY t.id 
                       ORDER BY t.created_at DESC");
$stmt->execute([$forum_id]);
$threads = $stmt->fetchAll();

include 'header.php';
?>

<div style="margin-bottom: 2rem;">
    <a href="/" style="color: #0366d6; text-decoration: none;">← Forums</a> / 
    <span style="color: #586069;"><?= htmlspecialchars($forum['name']) ?></span>
</div>

<div style="background: white; border: 1px solid #e1e4e8; border-radius: 6px; padding: 1.5rem; margin-bottom: 2rem;">
    <h1><?= htmlspecialchars($forum['name']) ?></h1>
    <p><?= htmlspecialchars($forum['description']) ?></p>
</div>

<?php if(isset($_SESSION['user_id'])): ?>
    <div style="margin-bottom: 2rem;">
        <a href="/create-thread?forum=<?= $forum_id ?>" class="btn">Create New Thread</a>
    </div>
<?php endif; ?>

<h2>Threads</h2>

<?php if(count($threads) > 0): ?>
    <?php foreach($threads as $thread): ?>
        <div style="background: white; border: 1px solid #e1e4e8; border-radius: 6px; padding: 1rem; margin-bottom: 1rem;">
            <div style="display: flex; justify-content: space-between; align-items: start;">
                <div style="flex: 1;">
                    <div style="font-size: 1.125rem; font-weight: 600; margin-bottom: 0.5rem;">
                        <a href="/thread/<?= $thread['id'] ?>" style="color: #0366d6; text-decoration: none;"><?= htmlspecialchars($thread['title']) ?></a>
                    </div>
                    <div style="color: #586069; font-size: 0.75rem;">
                        Started by <?= htmlspecialchars($thread['username']) ?> • 
                        <?= date('M d, Y', strtotime($thread['created_at'])) ?> • 
                        <?= $thread['views'] ?> views
                    </div>
                </div>
                <div style="text-align: right;">
                    <div style="font-weight: 600;"><?= $thread['reply_count'] ?></div>
                    <div style="color: #586069; font-size: 0.75rem;">replies</div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="alert alert-info">No threads yet. Be the first to create a thread!</div>
<?php endif; ?>

<?php include 'footer.php'; ?>