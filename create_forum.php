<?php
require_once 'config.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$page_title = 'Create Forum';

$stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
$categories = $stmt->fetchAll();

$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category_id = $_POST['category_id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $user_id = $_SESSION['user_id'];
    
    try {
        $stmt = $pdo->prepare("INSERT INTO forums (category_id, name, description, created_by) VALUES (?, ?, ?, ?)");
        $stmt->execute([$category_id, $name, $description, $user_id]);
        header("Location: index.php");
        exit();
    } catch(PDOException $e) {
        $error = 'Failed to create forum';
    }
}

include 'header.php';
?>

<h1>Create New Forum</h1>

<?php if($error): ?>
    <div class="alert alert-error"><?= $error ?></div>
<?php endif; ?>

<form method="POST">
    <div class="form-group">
        <label>Category</label>
        <select name="category_id" required>
            <option value="">Select a category</option>
            <?php foreach($categories as $category): ?>
                <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label>Forum Name</label>
        <input type="text" name="name" required>
    </div>
    <div class="form-group">
        <label>Description</label>
        <textarea name="description" required></textarea>
    </div>
    <button type="submit" class="btn">Create Forum</button>
    <a href="index.php" style="margin-left: 1rem;">Cancel</a>
</form>

<?php include 'footer.php'; ?>