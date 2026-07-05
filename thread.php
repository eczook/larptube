<?php
require_once 'config.php';

$thread_id = isset($_GET['id']) ? $_GET['id'] : (isset($_SERVER['PATH_INFO']) ? explode('/', $_SERVER['PATH_INFO'])[1] : null);

if(!$thread_id) {
    redirect('/');
}

$pdo->prepare("UPDATE threads SET views = views + 1 WHERE id = ?")->execute([$thread_id]);

$stmt = $pdo->prepare("SELECT t.*, u.username, u.avatar, u.id as user_id, f.name as forum_name, f.id as forum_id 
                       FROM threads t 
                       JOIN users u ON t.user_id = u.id 
                       JOIN forums f ON t.forum_id = f.id 
                       WHERE t.id = ?");
$stmt->execute([$thread_id]);
$thread = $stmt->fetch();

if(!$thread) {
    redirect('/');
}

$page_title = $thread['title'];

$stmt = $pdo->prepare("SELECT r.*, u.username, u.avatar, u.id as user_id, u.bio
                       FROM replies r 
                       JOIN users u ON r.user_id = u.id 
                       WHERE r.thread_id = ? 
                       ORDER BY r.created_at ASC");
$stmt->execute([$thread_id]);
$replies = $stmt->fetchAll();

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reply']) && isset($_SESSION['user_id'])) {
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
    
    $stmt = $pdo->prepare("INSERT INTO replies (thread_id, user_id, content, image) VALUES (?, ?, ?, ?)");
    $stmt->execute([$thread_id, $user_id, $content, $image_path]);
    redirect("/thread/$thread_id");
}

include 'header.php';
?>

<style>
    .reply {
        background: white;
        border: 1px solid #e1e4e8;
        border-radius: 6px;
        margin-bottom: 1rem;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }
    
    .reply-sidebar {
        background: #f6f8fa;
        padding: 1rem;
        border-bottom: 1px solid #e1e4e8;
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .reply-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
    }
    
    .reply-avatar-placeholder {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        font-weight: bold;
        color: white;
    }
    
    .reply-author {
        font-weight: 600;
    }
    
    .reply-author a {
        color: #0366d6;
        text-decoration: none;
    }
    
    .reply-date {
        font-size: 0.75rem;
        color: #586069;
    }
    
    .reply-content {
        padding: 1rem;
    }
    
    .thread-header {
        background: white;
        border: 1px solid #e1e4e8;
        border-radius: 6px;
        margin-bottom: 2rem;
        overflow: hidden;
    }
    
    .thread-sidebar {
        background: #f6f8fa;
        padding: 1rem;
        border-bottom: 1px solid #e1e4e8;
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .thread-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
    }
    
    .thread-title {
        padding: 1rem;
        border-bottom: 1px solid #e1e4e8;
    }
    
    @media (min-width: 768px) {
        .reply {
            flex-direction: row;
        }
        
        .reply-sidebar {
            flex: 0 0 200px;
            border-bottom: none;
            border-right: 1px solid #e1e4e8;
            flex-direction: column;
            text-align: center;
        }
        
        .thread-sidebar {
            flex: 0 0 200px;
            border-bottom: none;
            border-right: 1px solid #e1e4e8;
            flex-direction: column;
            text-align: center;
        }
    }
</style>

<div style="margin-bottom: 2rem;">
    <a href="/" style="color: #0366d6; text-decoration: none;">Forums</a> / 
    <a href="/forum/<?= $thread['forum_id'] ?>" style="color: #0366d6; text-decoration: none;"><?= htmlspecialchars($thread['forum_name']) ?></a> / 
    <span style="color: #586069;"><?= htmlspecialchars($thread['title']) ?></span>
</div>

<div class="thread-header">
    <div class="thread-sidebar">
        <a href="/user/<?= $thread['user_id'] ?>">
            <?php if($thread['avatar'] && file_exists($thread['avatar'])): ?>
                <img src="<?= $thread['avatar'] ?>" alt="<?= htmlspecialchars($thread['username']) ?>" class="thread-avatar">
            <?php else: ?>
                <div class="reply-avatar-placeholder" style="width: 50px; height: 50px; font-size: 1.5rem; margin: 0 auto;">
                    <?= strtoupper(substr($thread['username'], 0, 1)) ?>
                </div>
            <?php endif; ?>
        </a>
        <div>
            <div class="reply-author">
                <a href="/user/<?= $thread['user_id'] ?>"><?= htmlspecialchars($thread['username']) ?></a>
            </div>
            <div class="reply-date"><?= date('M d, Y H:i', strtotime($thread['created_at'])) ?></div>
        </div>
    </div>
    <div style="flex: 1;">
        <div class="thread-title">
            <h1 style="margin-bottom: 0.5rem;"><?= htmlspecialchars($thread['title']) ?></h1>
            <div style="color: #586069; font-size: 0.875rem;">
                <?= $thread['views'] ?> views
            </div>
        </div>
    </div>
</div>

<h3>Replies (<?= count($replies) ?>)</h3>

<?php foreach($replies as $reply): ?>
    <div class="reply">
        <div class="reply-sidebar">
            <a href="/user/<?= $reply['user_id'] ?>">
                <?php if($reply['avatar'] && file_exists($reply['avatar'])): ?>
                    <img src="<?= $reply['avatar'] ?>" alt="<?= htmlspecialchars($reply['username']) ?>" class="reply-avatar">
                <?php else: ?>
                    <div class="reply-avatar-placeholder">
                        <?= strtoupper(substr($reply['username'], 0, 1)) ?>
                    </div>
                <?php endif; ?>
            </a>
            <div>
                <div class="reply-author">
                    <a href="/user/<?= $reply['user_id'] ?>"><?= htmlspecialchars($reply['username']) ?></a>
                </div>
                <div class="reply-date"><?= date('M d, Y H:i', strtotime($reply['created_at'])) ?></div>
            </div>
        </div>
        <div class="reply-content">
            <?php if($reply['image']): ?>
                <div style="margin-bottom: 1rem;">
                    <img src="<?= $reply['image'] ?>" alt="Reply image" style="max-width: 100%; max-height: 300px; border-radius: 6px;">
                </div>
            <?php endif; ?>
            <div><?= nl2br(htmlspecialchars($reply['content'])) ?></div>
        </div>
    </div>
<?php endforeach; ?>

<?php if(isset($_SESSION['user_id'])): ?>
    <div style="background: white; border: 1px solid #e1e4e8; border-radius: 6px; padding: 1.5rem; margin-top: 2rem;">
        <h3>Post a Reply</h3>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <textarea name="content" placeholder="Write your reply..." required style="width: 100%; padding: 0.5rem; border: 1px solid #e1e4e8; border-radius: 6px; min-height: 120px;"></textarea>
            </div>
            <div class="form-group">
                <input type="file" name="image" accept="image/*">
                <small style="color: #586069;">Optional: Upload an image</small>
            </div>
            <button type="submit" name="reply" class="btn">Post Reply</button>
        </form>
    </div>
<?php else: ?>
    <div class="alert alert-info" style="margin-top: 2rem;">
        <a href="/login">Login</a> to post a reply
    </div>
<?php endif; ?>

<?php include 'footer.php'; ?>