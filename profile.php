<?php
require_once 'config.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$page_title = 'My Profile';
$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['upload_avatar'])) {
        if(isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $filename = $_FILES['avatar']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if(in_array($ext, $allowed)) {
                if(!file_exists('uploads/avatars')) {
                    mkdir('uploads/avatars', 0777, true);
                }
                
                $new_filename = 'avatar_' . $user_id . '_' . time() . '.' . $ext;
                $upload_path = 'uploads/avatars/' . $new_filename;
                
                if($user['avatar'] && file_exists($user['avatar'])) {
                    unlink($user['avatar']);
                }
                
                if(move_uploaded_file($_FILES['avatar']['tmp_name'], $upload_path)) {
                    $stmt = $pdo->prepare("UPDATE users SET avatar = ? WHERE id = ?");
                    $stmt->execute([$upload_path, $user_id]);
                    $success = 'Avatar updated successfully!';
                    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
                    $stmt->execute([$user_id]);
                    $user = $stmt->fetch();
                } else {
                    $error = 'Failed to upload image';
                }
            } else {
                $error = 'Only JPG, PNG, GIF, and WEBP files are allowed';
            }
        } else {
            $error = 'Please select an image file';
        }
    }
    
    if(isset($_POST['remove_avatar'])) {
        if($user['avatar'] && file_exists($user['avatar'])) {
            unlink($user['avatar']);
        }
        $stmt = $pdo->prepare("UPDATE users SET avatar = NULL WHERE id = ?");
        $stmt->execute([$user_id]);
        $success = 'Avatar removed successfully!';
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();
    }
    
    if(isset($_POST['update_profile'])) {
        $new_username = $_POST['username'];
        $new_email = $_POST['email'];
        $bio = $_POST['bio'];
        
        try {
            $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, bio = ? WHERE id = ?");
            $stmt->execute([$new_username, $new_email, $bio, $user_id]);
            $_SESSION['username'] = $new_username;
            $success = 'Profile updated successfully!';
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch();
        } catch(PDOException $e) {
            $error = 'Username already taken';
        }
    }
    
    if(isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        if($new_password !== $confirm_password) {
            $error = 'New passwords do not match';
        } else {
            $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $db_user = $stmt->fetch();
            
            if(password_verify($current_password, $db_user['password'])) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$hashed_password, $user_id]);
                $success = 'Password changed successfully!';
            } else {
                $error = 'Current password is incorrect';
            }
        }
    }
}

$stmt = $pdo->prepare("SELECT COUNT(*) as thread_count FROM threads WHERE user_id = ?");
$stmt->execute([$user_id]);
$thread_count = $stmt->fetch()['thread_count'];

$stmt = $pdo->prepare("SELECT COUNT(*) as reply_count FROM replies WHERE user_id = ?");
$stmt->execute([$user_id]);
$reply_count = $stmt->fetch()['reply_count'];

$stmt = $pdo->prepare("SELECT t.*, f.name as forum_name 
                       FROM threads t 
                       JOIN forums f ON t.forum_id = f.id 
                       WHERE t.user_id = ? 
                       ORDER BY t.created_at DESC 
                       LIMIT 10");
$stmt->execute([$user_id]);
$recent_threads = $stmt->fetchAll();

include 'header.php';
?>

<style>
    .profile-header {
        background: white;
        border: 1px solid #e1e4e8;
        border-radius: 6px;
        padding: 2rem;
        margin-bottom: 2rem;
        display: flex;
        gap: 2rem;
        flex-wrap: wrap;
        align-items: center;
    }
    
    .avatar-section {
        text-align: center;
        flex-shrink: 0;
    }
    
    .avatar {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #0366d6;
        background: #f6f8fa;
    }
    
    .avatar-placeholder {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        font-weight: bold;
        color: white;
        border: 3px solid #0366d6;
    }
    
    .profile-info {
        flex: 1;
    }
    
    .profile-stats {
        display: flex;
        gap: 2rem;
        margin: 1rem 0;
        flex-wrap: wrap;
    }
    
    .stat {
        text-align: center;
    }
    
    .stat-number {
        font-size: 1.5rem;
        font-weight: bold;
        color: #0366d6;
    }
    
    .stat-label {
        font-size: 0.875rem;
        color: #586069;
    }
    
    .profile-form {
        background: white;
        border: 1px solid #e1e4e8;
        border-radius: 6px;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .post-item {
        background: white;
        border: 1px solid #e1e4e8;
        border-radius: 6px;
        padding: 1rem;
        margin-bottom: 1rem;
    }
    
    .avatar-upload-form input[type="file"] {
        margin-bottom: 0.5rem;
    }
    
    .button-group {
        display: flex;
        gap: 0.5rem;
        justify-content: center;
    }
    
    .btn-small {
        padding: 0.25rem 0.75rem;
        font-size: 0.875rem;
    }
</style>

<h1>My Profile</h1>

<?php if($success): ?>
    <div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>

<?php if($error): ?>
    <div class="alert alert-error"><?= $error ?></div>
<?php endif; ?>

<div class="profile-header">
    <div class="avatar-section">
        <?php if($user['avatar'] && file_exists($user['avatar'])): ?>
            <img src="<?= $user['avatar'] ?>" alt="Avatar" class="avatar">
        <?php else: ?>
            <div class="avatar-placeholder">
                <?= strtoupper(substr($user['username'], 0, 1)) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data" class="avatar-upload-form" style="margin-top: 1rem;">
            <input type="file" name="avatar" accept="image/*">
            <div class="button-group">
                <button type="submit" name="upload_avatar" class="btn btn-small">Upload</button>
                <?php if($user['avatar']): ?>
                    <button type="submit" name="remove_avatar" class="btn btn-danger btn-small" onclick="return confirm('Remove avatar?')">Remove</button>
                <?php endif; ?>
            </div>
        </form>
    </div>
    
    <div class="profile-info">
        <h2><?= htmlspecialchars($user['username']) ?></h2>
        <div class="profile-stats">
            <div class="stat">
                <div class="stat-number"><?= $thread_count ?></div>
                <div class="stat-label">Threads</div>
            </div>
            <div class="stat">
                <div class="stat-number"><?= $reply_count ?></div>
                <div class="stat-label">Replies</div>
            </div>
            <div class="stat">
                <div class="stat-number"><?= date('M Y', strtotime($user['created_at'])) ?></div>
                <div class="stat-label">Joined</div>
            </div>
        </div>
    </div>
</div>

<div class="profile-form">
    <h3>Edit Profile Information</h3>
    <form method="POST">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>">
        </div>
        <div class="form-group">
            <label>Bio</label>
            <textarea name="bio" placeholder="Tell us about yourself..." rows="3"><?= htmlspecialchars($user['bio']) ?></textarea>
        </div>
        <button type="submit" name="update_profile" class="btn">Update Profile</button>
    </form>
</div>

<div class="profile-form">
    <h3>Change Password</h3>
    <form method="POST">
        <div class="form-group">
            <label>Current Password</label>
            <input type="password" name="current_password" required>
        </div>
        <div class="form-group">
            <label>New Password</label>
            <input type="password" name="new_password" required>
        </div>
        <div class="form-group">
            <label>Confirm New Password</label>
            <input type="password" name="confirm_password" required>
        </div>
        <button type="submit" name="change_password" class="btn">Change Password</button>
    </form>
</div>

<h3>Recent Threads</h3>
<?php if(count($recent_threads) > 0): ?>
    <?php foreach($recent_threads as $thread): ?>
        <div class="post-item">
            <div style="font-weight: 600; margin-bottom: 0.5rem;">
                <a href="thread.php?id=<?= $thread['id'] ?>" style="color: #0366d6; text-decoration: none;"><?= htmlspecialchars($thread['title']) ?></a>
            </div>
            <div style="color: #586069; font-size: 0.75rem;">
                in <?= htmlspecialchars($thread['forum_name']) ?> • 
                <?= date('M d, Y', strtotime($thread['created_at'])) ?> • 
                <?= $thread['views'] ?> views
            </div>
        </div>
    <?php endforeach; ?>

<?php include 'footer.php'; ?>