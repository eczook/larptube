<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$upload_dir = 'uploads/';
$thumbnail_dir = 'thumbnails/';

if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}
if (!file_exists($thumbnail_dir)) {
    mkdir($thumbnail_dir, 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $tags = $_POST['tags'] ?? '';
    
    $filename = time() . '_' . basename($_FILES['video']['name']);
    $file_path = $upload_dir . $filename;
    
    if (move_uploaded_file($_FILES['video']['tmp_name'], $file_path)) {
        $thumbnail_path = $thumbnail_dir . pathinfo($filename, PATHINFO_FILENAME) . '.jpg';
        $ffmpeg_cmd = "ffmpeg -i " . escapeshellarg($file_path) . " -ss 00:00:02 -vframes 1 " . escapeshellarg($thumbnail_path) . " 2>&1";
        exec($ffmpeg_cmd, $output, $return_var);
        
        if (!file_exists($thumbnail_path)) {
            $thumbnail_path = 'https://youview.lol/img/pixel.gif';
        }
        
        $stmt = $pdo->prepare("INSERT INTO videos (title, description, tags, file_path, thumbnail_path, user_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $description, $tags, $file_path, $thumbnail_path, $_SESSION['user_id']]);
        
        header("Location: index.php");
        exit();
    } else {
        $error = "Failed to upload video.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>LarpTube</title>
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
        <div class="page_title">Upload Your Video</div>
        
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="form_box">
            <form method="POST" enctype="multipart/form-data">
                <div class="form_row">
                    <div class="form_label">Video Title:</div>
                    <input type="text" name="title" required>
                </div>
                
                <div class="form_row">
                    <div class="form_label">Description:</div>
                    <textarea name="description" rows="4"></textarea>
                </div>
                
                <div class="form_row">
                    <div class="form_label">Tags:</div>
                    <input type="text" name="tags" placeholder="lol funny comedy">
                </div>
                
                <div class="form_row">
                    <div class="form_label">Video File:</div>
                    <input type="file" name="video" accept="video/mp4" required>
                </div>
                
                <div class="form_row">
                    <input type="submit" value="Upload Video">
                </div>
            </form>
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