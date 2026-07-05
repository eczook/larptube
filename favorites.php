<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$stmt = $pdo->prepare("SELECT v.* FROM videos v JOIN favorites f ON v.id = f.video_id WHERE f.user_id = ? ORDER BY f.created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$favorites = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Favorites - LarpTube</title>
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
                <span class="util-item"><a href="my_account.php"><?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?></a></span>
                <span class="util-item"><a href="my_videos.php">Videos</a></span>
                <span class="util-item"><a href="favorites.php">Favorites</a></span>
                <span class="util-item"><a href="logout.php">Sign Out</a></span>
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
        <div class="page_title">My Favorites</div>
        
        <?php if (count($favorites) > 0): ?>
            <?php foreach ($favorites as $video): ?>
                <div class="video_entry clearfix">
                    <div class="video_thumb">
                        <a href="watch.php?id=<?php echo $video['id']; ?>">
                            <img src="<?php echo htmlspecialchars($video['thumbnail_path'] ?: 'https://youview.lol/img/pixel.gif'); ?>">
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
        <?php else: ?>
            <div class="video_entry">
                You haven't added any favorites yet. Browse videos and click "Favorite" to add them here.
            </div>
        <?php endif; ?>
    </div>
    
    <div id="footer">
        <div class="links">
            <a href="index.php">Home</a> |
            <a href="videos.php">Videos</a> |
            <a href="channels.php">Channels</a> |
            <a href="terms.php">Terms of Use</a>
        </div>
        <div style="text-align: center;">
            Copyright &copy; 2008 LarpTube, LLC™
        </div>
    </div>
</div>

</body>
</html>
