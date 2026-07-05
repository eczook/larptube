<?php
session_start();
include 'db.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

$total = $pdo->query("SELECT COUNT(*) FROM videos")->fetchColumn();
$total_pages = ceil($total / $per_page);

$stmt = $pdo->prepare("SELECT * FROM videos ORDER BY created_at DESC LIMIT ? OFFSET ?");
$stmt->execute([$per_page, $offset]);
$videos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Videos - LarpTube</title>
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
        <div class="page_title">All Videos</div>
        
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
                <div class="video_desc">
                    <?php echo htmlspecialchars(substr($video['description'] ?? '', 0, 100)); ?>...
                </div>
                <div class="video_meta">
                    Added: <?php echo date('M j, Y', strtotime($video['created_at'] ?? 'now')); ?> | 
                    Views: <?php echo $video['views'] ?? 0; ?>
                </div>
            </div>
        <?php endforeach; ?>
        
        <div style="margin-top: 20px; text-align: center;">
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page-1; ?>">« Previous</a>
            <?php endif; ?>
            Page <?php echo $page; ?> of <?php echo $total_pages; ?>
            <?php if ($page < $total_pages): ?>
                <a href="?page=<?php echo $page+1; ?>">Next »</a>
            <?php endif; ?>
        </div>
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