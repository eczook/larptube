<?php
session_start();
include 'db.php';

$video_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT * FROM videos WHERE id = ?");
$stmt->execute([$video_id]);
$video = $stmt->fetch();

if (!$video) {
    header("Location: index.php");
    exit();
}

$update = $pdo->prepare("UPDATE videos SET views = views + 1 WHERE id = ?");
$update->execute([$video_id]);

$comment_stmt = $pdo->prepare("SELECT c.*, u.username FROM comments c LEFT JOIN users u ON c.user_id = u.id WHERE c.video_id = ? ORDER BY c.created_at DESC");
$comment_stmt->execute([$video_id]);
$comments = $comment_stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $comment_text = $_POST['comment'];
    $insert = $pdo->prepare("INSERT INTO comments (video_id, user_id, comment) VALUES (?, ?, ?)");
    $insert->execute([$video_id, $_SESSION['user_id'], $comment_text]);
    header("Location: watch.php?id=" . $video_id);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($video['title'] ?? 'LarpTube'); ?> - LarpTube</title>
    <link rel="icon" type="image/png" href="images/favicon1.png" sizes="32x32">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div id="baseDiv">
    <div id="masthead">
        <div class="logo">
            <a href="index.php">
                <img src="images/larplogo.png" alt="LarpTube">
            </a>
        </div>
        <div class="user-info">
            <div id="util-links">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <span class="util-item"><a href="my_account.php"><?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?></a></span>
                    <span class="util-item"><a href="my_videos.php">Videos</a></span>
                    <span class="util-item"><a href="favorites.php">Favorites</a></span>
                    <span class="util-item"><a href="logout.php">Sign Out</a></span>
                <?php else: ?>
                    <span class="util-item"><a href="signup.php">Sign Up</a></span>
                    <span class="util-item"><a href="login.php">Sign In</a></span>
                <?php endif; ?>
            </div>
        </div>
        <div class="search-bar">
            <div class="nav">
                <div class="nav-item"><a href="index.php" class="content">Home</a></div>
                <div class="nav-item"><a href="videos.php" class="content">Videos</a></div>
                <div class="nav-item"><a href="channels.php" class="content">Channels</a></div>
                <div class="nav-item"><a href="community.php" class="content">Community</a></div>
            </div>
            <form id="search-form" action="search.php" method="GET">
                <input type="text" name="q" id="searchField">
                <input type="submit" value="Search">
            </form>
            <div id="upload-button">
                <a href="upload.php"><img src="https://youview.lol/img/new.gif" alt="Upload"></a>
            </div>
        </div>
    </div>

    <div id="watch-this-vid">
        <div id="watch-player-div">
            <video width="640" height="388" controls>
                <source src="<?php echo htmlspecialchars($video['file_path'] ?? ''); ?>" type="video/mp4">
            </video>
        </div>
        
        <div id="watch-vid-title"><?php echo htmlspecialchars($video['title'] ?? ''); ?></div>
        
        <div id="watch-ratings-views">
            <img src="https://youview.lol/img/star.gif"> <img src="https://youview.lol/img/star.gif"> <img src="https://youview.lol/img/star.gif"> <img src="https://youview.lol/img/star.gif"> <img src="https://youview.lol/img/star_half.gif">
            <span id="watch-views-div"><?php echo $video['views'] ?? 0; ?> views</span>
        </div>
        
        <div id="watch-actions-area">
            <table class="watch-tabs" cellpadding="0" cellspacing="0">
                <tr>
                    <td id="watch-tab-favorite" width="130"><span class="watch-action-text">Favorite</span></td>
                    <td id="watch-tab-playlists" width="130"><span class="watch-action-text">Playlists</span></td>
                    <td id="watch-tab-share" width="130"><span class="watch-action-text">Share</span></td>
                    <td id="watch-tab-flag" width="130"><span class="watch-action-text">Flag</span></td>
                </tr>
            </table>
        </div>
        
        <div class="watch-info">
            <div style="margin: 10px 0;">
                <strong>Added:</strong> <?php echo date('F j, Y', strtotime($video['created_at'] ?? 'now')); ?>
                <?php if (!empty($video['tags'])): ?>
                    | <strong>Tags:</strong> 
                    <?php 
                    $tags = explode(' ', $video['tags']);
                    foreach ($tags as $tag):
                    ?>
                        <a href="search.php?tag=<?php echo urlencode($tag); ?>"><?php echo htmlspecialchars($tag); ?></a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div style="margin: 10px 0; padding: 10px; background: #f5f5f5;">
                <?php echo nl2br(htmlspecialchars($video['description'] ?? '')); ?>
            </div>
        </div>
        
        <div id="watch-comments-stats">
            <div id="watch-tab-stats">Comments (<?php echo count($comments); ?>)</div>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <div id="watch-comment-post">
                    <form method="POST">
                        <textarea name="comment" id="comment_text" rows="3" placeholder="Add a comment..."></textarea>
                        <div style="margin-top: 5px;">
                            <input type="submit" value="Post Comment">
                        </div>
                    </form>
                </div>
            <?php else: ?>
                <div id="watch-comment-post" style="text-align: center;">
                    <a href="login.php">Log in</a> to post a comment.
                </div>
            <?php endif; ?>
            
            <?php foreach ($comments as $comment): ?>
                <div class="comment">
                    <div class="comment_user">
                        <?php echo htmlspecialchars($comment['username'] ?? 'Anonymous'); ?>
                        <span class="comment_date"><?php echo date('M j, Y g:i A', strtotime($comment['created_at'] ?? 'now')); ?></span>
                    </div>
                    <div class="comment_text">
                        <?php echo nl2br(htmlspecialchars($comment['comment'] ?? '')); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <div id="watch-other-vids">
        <div class="module_box">
            <div class="module_header">More Videos</div>
            <div class="module_content">
                <?php
                $other_stmt = $pdo->prepare("SELECT * FROM videos WHERE id != ? ORDER BY RAND() LIMIT 5");
                $other_stmt->execute([$video_id]);
                $other_videos = $other_stmt->fetchAll();
                ?>
                <?php foreach ($other_videos as $other): ?>
                <div class="featured_video">
                    <div class="featured_thumb">
                        <a href="watch.php?id=<?php echo $other['id']; ?>">
                            <img src="<?php echo htmlspecialchars($other['thumbnail_path'] ?: 'https://youview.lol/img/pixel.gif'); ?>" width="120" height="90">
                        </a>
                    </div>
                    <div class="featured_title">
                        <a href="watch.php?id=<?php echo $other['id']; ?>"><?php echo htmlspecialchars(substr($other['title'] ?? '', 0, 25)); ?></a>
                    </div>
                    <div class="featured_details">
                        <?php echo $other['views'] ?? 0; ?> views
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <div id="footer">
        <div class="links">
            <a href="index.php">Home</a> |
            <a href="videos.php">Videos</a> |
            <a href="channels.php">Channels</a> |
            <a href="community.php">Community</a> |
            <a href="terms.php">Terms of Use</a> |
            <a href="privacy.php">Privacy Policy</a>
        </div>
        <div style="text-align: center;">
            Copyright &copy; 2008 LarpTube, LLC™ | Broadcast Yourself™
        </div>
    </div>
</div>

</body>
</html>