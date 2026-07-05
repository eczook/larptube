<?php
require_once 'config.php';

if(isset($_SESSION['user_id'])) {
    redirect('index.php');
}

$page_title = 'Register';
$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = $_POST['email'];
    
    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
        $stmt->execute([$username, $password, $email]);
        $_SESSION['success'] = 'Registration successful! Please login.';
        redirect('login.php');
    } catch(PDOException $e) {
        $error = 'Username already exists';
    }
}

include 'header.php';
?>

<h1>Register</h1>

<?php if($error): ?>
    <div class="alert alert-error"><?= $error ?></div>
<?php endif; ?>

<form method="POST">
    <div class="form-group">
        <label>Username</label>
        <input type="text" name="username" required autofocus>
    </div>
    <div class="form-group">
        <label>Email (optional)</label>
        <input type="email" name="email">
    </div>
    <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" required>
    </div>
    <button type="submit" class="btn">Register</button>
</form>

<p style="margin-top: 1rem;">Already have an account? <a href="login.php">Login</a></p>

<?php include 'footer.php'; ?>