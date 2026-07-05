<?php
require_once 'config.php';

if(!isset($_SESSION['user_id'])) {
    redirect('/login');
}

$page_title = 'Edit Profile';
$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['update_bio'])) {
        $bio = $_POST['bio'];
        $stmt = $pdo->prepare("UPDATE users SET bio = ? WHERE id = ?");
        $stmt->execute([$bio, $user_id]);
        $success = 'Bio updated successfully!';
        $user['bio'] = $bio;
    }
    
    if(isset($_POST['update_profile'])) {
        $new_username = $_POST['username'];
        $new_email = $_POST['email'];
        
        try {
            $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
            $stmt->execute([$new_username, $new_email, $user_id]);
            $_SESSION['username'] = $new_username;
            $success = 'Profile updated successfully!';
            $user['username'] = $new_username;
            $user['email'] = $new_email;
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

include 'header.php';
?>

<style>
    .edit-section {
        background: white;
        border: 1px solid #e1e4e8;
        border-radius: 6px;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .edit-section h3 {
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #e1e4e8;
    }
</style>

<h1>Edit Profile</h1>

<?php if($success): ?>
    <div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>

<?php if($error): ?>
    <div class="alert alert-error"><?= $error ?></div>
<?php endif; ?>

<div class="edit-section">
    <h3>Profile Information</h3>
    <form method="POST">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>">
        </div>
        <button type="submit" name="update_profile" class="btn">Update Profile</button>
    </form>
</div>

<div class="edit-section">
    <h3>Bio</h3>
    <form method="POST">
        <div class="form-group">
            <textarea name="bio" placeholder="Tell us about yourself..." rows="4" style="min-height: 100px;"><?= htmlspecialchars($user['bio']) ?></textarea>
        </div>
        <button type="submit" name="update_bio" class="btn">Update Bio</button>
    </form>
</div>

<div class="edit-section">
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

<div class="edit-section">
    <h3>Avatar</h3>
    <p>Change your avatar on your <a href="/profile">profile page</a></p>
</div>

<?php include 'footer.php'; ?>