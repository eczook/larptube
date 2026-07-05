<?php
session_start();
include 'db.php';

$query = $pdo->query("SELECT * FROM videos ORDER BY created_at DESC LIMIT 20");
$videos = $query->fetchAll();

$tag_query = $pdo->query("SELECT tags FROM videos WHERE tags IS NOT NULL AND tags != ''");
$all_tags = [];
while ($row = $tag_query->fetch()) {
    $tags = explode(' ', $row['tags']);
    foreach ($tags as $tag) {
        $tag = trim($tag);
        if ($tag) $all_tags[] = $tag;
    }
}
$tag_counts = array_count_values($all_tags);
arsort($tag_counts);
$popular_tags = array_slice($tag_counts, 0, 25, true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>LarpTube - Broadcast Yourself</title>
    <link rel="icon" type="image/png" href="images/favicon1.png">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div id="baseDiv">
    <div id="masthead">
        <div class="bar">
            <div class="search-bar">
                <div class="logo">
                    <a href="index.php"><img src="images/larplogo.png" alt="LarpTube"></a>
                </div>
                <div class="user-info">
                    <div id="util-links">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <span class="util-item"><a href="my_account.php"><?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?></a></span>
                            <span class="util-item"><a href="my_videos.php">My Videos</a></span>
                            <span class="util-item"><a href="favorites.php">Favorites</a></span>
                            <span class="util-item"><a href="logout.php">Sign Out</a></span>
                        <?php else: ?>
                            <span class="util-item"><a href="signup.php">Sign Up</a></span>
                            <span class="util-item"><a href="login.php">Sign In</a></span>
                            <span class="util-item"><a href="help.php">Help</a></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="nav">
                    <div class="nav-item"><a href="index.php" class="content">Home</a></div>
                    <div class="nav-item"><a href="videos.php" class="content">Videos</a></div>
                    <div class="nav-item"><a href="channels.php" class="content">Channels</a></div>
                    <div class="nav-item"><a href="community.php" class="content">Community</a></div>
                </div>
                <form id="search-form" action="search.php" method="GET">
                    <input type="text" name="q" id="search-term">
                    <input type="submit" value="Search" id="search-button">
                </form>
                <div id="upload-button">
                    <a href="upload.php">Upload</a>
                </div>
            </div>
        </div>
    </div>

    <div id="homepage-main-content">
        <div class="hpBlockHeading">Featured Videos</div>
        
        <?php if (count($videos) > 0): ?>
            <?php $featured = $videos[0]; ?>
            <div class="v120hEntry">
                <div class="vstill">
                    <div class="video-thumb-large">
                        <a href="watch.php?id=<?php echo $featured['id']; ?>">
                            <img src="<?php echo htmlspecialchars($featured['thumbnail_path'] ?: 'images/default_thumb.jpg'); ?>">
                        </a>
                    </div>
                </div>
                <div class="vinfo">
                    <div class="vtitlelink"><a href="watch.php?id=<?php echo $featured['id']; ?>"><?php echo htmlspecialchars($featured['title'] ?? ''); ?></a></div>
                    <div class="vdesc"><?php echo htmlspecialchars(substr($featured['description'] ?? '', 0, 150)); ?>...</div>
                    <div class="vfacets"><?php echo $featured['views'] ?? 0; ?> views | Added: <?php echo date('M j, Y', strtotime($featured['created_at'] ?? 'now')); ?></div>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="hpBlockHeading" style="margin-top: 20px;">What's New</div>
        
        <div class="hpWNentry">
            <div class="hpWNdesc"><strong>LarpTube Greeting Cards</strong><br>Send video greeting cards to your friends and family</div>
        </div>
        <div class="hpWNentry">
            <div class="hpWNdesc"><strong>Captions and Subtitles</strong><br>Add multi-track captions and subtitles to your videos</div>
        </div>
        <div class="hpWNentry">
            <div class="hpWNdesc"><strong>Video Annotations</strong><br>Add interactive commentary and links to your videos</div>
        </div>
        
        <div class="hpBlockHeading" style="margin-top: 20px;">Recently Added Videos</div>
        
        <?php foreach ($videos as $video): ?>
            <div class="vEntry">
                <div class="vstill">
                    <div class="video-thumb-normal">
                        <a href="watch.php?id=<?php echo $video['id']; ?>">
                            <img src="<?php echo htmlspecialchars($video['thumbnail_path'] ?: 'images/default_thumb.jpg'); ?>">
                        </a>
                    </div>
                </div>
                <div class="vinfo">
                    <div class="vtitle"><a href="watch.php?id=<?php echo $video['id']; ?>"><?php echo htmlspecialchars($video['title'] ?? ''); ?></a></div>
                    <div class="vdesc"><?php echo htmlspecialchars(substr($video['description'] ?? '', 0, 100)); ?>...</div>
                    <div class="vfacets">Added: <?php echo date('M j, Y', strtotime($video['created_at'] ?? 'now')); ?> | Views: <?php echo $video['views'] ?? 0; ?></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <div id="hpSideContent">
        <div class="tags_box">
            <h3>Popular Tags</h3>
            <?php foreach ($popular_tags as $tag => $count): ?>
                <a href="search.php?tag=<?php echo urlencode($tag); ?>"><?php echo htmlspecialchars($tag); ?></a>
            <?php endforeach; ?>
        </div>
        
        <div class="module_box">
            <div class="module_header">Promoted Videos</div>
            <div class="module_content">
                <?php $promoted = array_slice($videos, 0, 4); ?>
                <?php foreach ($promoted as $video): ?>
                <div class="featured_video">
                    <div class="featured_thumb">
                        <a href="watch.php?id=<?php echo $video['id']; ?>">
                            <img src="<?php echo htmlspecialchars($video['thumbnail_path'] ?: 'images/default_thumb.jpg'); ?>">
                        </a>
                    </div>
                    <div class="featured_title">
                        <a href="watch.php?id=<?php echo $video['id']; ?>"><?php echo htmlspecialchars(substr($video['title'] ?? '', 0, 25)); ?></a>
                    </div>
                    <div class="featured_details"><?php echo $video['views'] ?? 0; ?> views</div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="module_box">
            <div class="module_header">Your Account</div>
            <div class="module_content">
                <div><a href="my_videos.php">My Videos</a></div>
                <div><a href="favorites.php">My Favorites</a></div>
                <div><a href="playlists.php">My Playlists</a></div>
                <div><a href="my_account.php">Account Settings</a></div>
            </div>
        </div>
        
        <div class="module_box">
            <div class="module_header">Help & Info</div>
            <div class="module_content">
                <div><a href="help.php">Help Resources</a></div>
                <div><a href="guidelines.php">Community Guidelines</a></div>
                <div><a href="terms.php">Terms of Use</a></div>
                <div><a href="privacy.php">Privacy Policy</a></div>
            </div>
        </div>
    </div>
    
    <div id="footer">
        <div class="links">
            <a href="index.php">Home</a> |
            <a href="videos.php">Videos</a> |
            <a href="channels.php">Channels</a> |
            <a href="community.php">Community</a> |
            <a href="signup.php">Sign Up</a> |
            <a href="help.php">Help</a> |
            <a href="terms.php">Terms of Use</a> |
            <a href="privacy.php">Privacy Policy</a>
        </div>
        <div id="copyright">Copyright &copy; 2005 LarpTube, LLC | Broadcast Yourself</div>
    </div>
</div>

</body>
</html>