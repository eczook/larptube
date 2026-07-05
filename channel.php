<?php
session_start();
include 'db.php';

$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$channel = $stmt->fetch();

if (!$channel) {
    header("Location: index.php");
    exit();
}

$video_stmt = $pdo->prepare("SELECT * FROM videos WHERE user_id = ? ORDER BY created_at DESC");
$video_stmt->execute([$user_id]);
$videos = $video_stmt->fetchAll();

$total_views = $pdo->prepare("SELECT SUM(views) as total FROM videos WHERE user_id = ?");
$total_views->execute([$user_id]);
$views = $total_views->fetch();

if (isset($_POST['subscribe']) && isset($_SESSION['user_id']) && $_SESSION['user_id'] != $user_id) {
    $sub_stmt = $pdo->prepare("UPDATE users SET subscribers = subscribers + 1 WHERE id = ?");
    $sub_stmt->execute([$user_id]);
    header("Location: channel.php?id=" . $user_id);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($channel['username'] ?? ''); ?> - LarpTube</title>
    <link rel="icon" type="image/png" href="images/favicon1.png" sizes="32x32">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div id="baseDiv">
    <div id="masthead">
        <div class="logo">
            <a href="index.php"><img src="images/larplogo.png" alt="LarpTube"></a>
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

    <div id="homepage-main-content">
        <div style="background: #f5f5f5; padding: 20px; margin-bottom: 20px; border: 1px solid #e0e0e0;">
            <div style="float: left; margin-right: 20px;">
                <img src="<?php echo htmlspecialchars($channel['profile_pic'] ?? 'images/default_avatar.png'); ?>" width="100" height="100" style="border: 3px double #999;">
            </div>
            <div>
                <h1 style="margin: 0;"><?php echo htmlspecialchars($channel['username'] ?? ''); ?></h1>
                <div class="video_meta">
                    Subscribers: <?php echo $channel['subscribers'] ?? 0; ?> | 
                    Total Views: <?php echo $views['total'] ?? 0; ?> | 
                    Videos: <?php echo count($videos); ?>
                </div>
                <div style="margin-top: 10px;">
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != $user_id): ?>
                        <form method="POST" style="display: inline;">
                            <input type="submit" name="subscribe" value="Subscribe" style="background: #FF0000; color: #FFF; border: none; padding: 5px 15px; cursor: pointer;">
                        </form>
                    <?php endif; ?>
                </div>
            </div>
            <div style="clear: both;"></div>
        </div>
        
        <div class="page_title">Videos</div>
        
        <?php foreach ($videos as $video): ?>
            <div class="video_entry clearfix">
                <div class="video_thumb">
                    <a href="watch.php?id=<?php echo $video['id']; ?>">
                        <img src="<?php echo htmlspecialchars($video['thumbnail_path'] ?: 'https://youview.lol/img/pixel.gif'); ?>">
                        <span class="runtime">00:00</span>
                    </a>
                </div>
                <div class="video_title">
                    <a href="watch.php?id=<?php echo $video['id']; ?>"><?php echo htmlspecialchars($video['title'] ?? ''); ?></a>
                </div>
                <div class="video_meta">
                    Added: <?php echo date('M j, Y', strtotime($video['created_at'] ?? 'now')); ?> | 
                    Views: <?php echo $video['views'] ?? 0; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <div id="footer">
        <div class="links">
            <a href="index.php">Home</a> |
            <a href="videos.php">Videos</a> |
            <a href="channels.php">Channels</a> |
            <a href="community.php">Community</a> |
            <a href="terms.php">Terms of Use</a>
        </div>
        <div style="text-align: center;">
            Copyright &copy; 2008 LarpTube, LLC™
        </div>
    </div>
</div>

</body>
</html>