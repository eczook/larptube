<?php
require_once 'config.php';

$page_title = 'Users';

$search = isset($_GET['search']) ? $_GET['search'] : '';

if($search) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username LIKE ? ORDER BY created_at DESC");
    $stmt->execute(["%$search%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
}
$users = $stmt->fetchAll();

include 'header.php';
?>

<style>
    .users-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }
    
    .user-card {
        background: white;
        border: 1px solid #e1e4e8;
        border-radius: 6px;
        padding: 1rem;
        text-align: center;
        transition: transform 0.1s, box-shadow 0.2s;
    }
    
    .user-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .user-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        margin: 0 auto 0.5rem;
        border: 2px solid #0366d6;
    }
    
    .user-avatar-placeholder {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        font-weight: bold;
        color: white;
        margin: 0 auto 0.5rem;
        border: 2px solid #0366d6;
    }
    
    .user-username {
        font-weight: 600;
        margin-bottom: 0.25rem;
    }
    
    .user-username a {
        color: #0366d6;
        text-decoration: none;
    }
    
    .user-meta {
        font-size: 0.75rem;
        color: #586069;
        margin-bottom: 0.5rem;
    }
    
    .user-bio {
        font-size: 0.875rem;
        color: #24292e;
        margin-top: 0.5rem;
        padding-top: 0.5rem;
        border-top: 1px solid #e1e4e8;
    }
    
    .search-box {
        background: white;
        border: 1px solid #e1e4e8;
        border-radius: 6px;
        padding: 1rem;
        margin-bottom: 2rem;
        display: flex;
        gap: 1rem;
    }
    
    .search-box input {
        flex: 1;
    }
    
    .search-box button {
        width: auto;
    }
</style>

<h1>Users</h1>

<div class="search-box">
    <form method="GET" style="display: flex; gap: 1rem; width: 100%;">
        <input type="text" name="search" placeholder="Search users..." value="<?= htmlspecialchars($search) ?>" style="flex: 1;">
        <button type="submit" class="btn">Search</button>
        <?php if($search): ?>
            <a href="/users" class="btn btn-outline">Clear</a>
        <?php endif; ?>
    </form>
</div>

<div class="users-grid">
    <?php if(count($users) > 0): ?>
        <?php foreach($users as $user): ?>
            <div class="user-card">
                <a href="/user/<?= $user['id'] ?>">
                    <?php if($user['avatar'] && file_exists($user['avatar'])): ?>
                        <img src="<?= $user['avatar'] ?>" alt="<?= htmlspecialchars($user['username']) ?>" class="user-avatar">
                    <?php else: ?>
                        <div class="user-avatar-placeholder">
                            <?= strtoupper(substr($user['username'], 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                </a>
                <div class="user-username">
                    <a href="/user/<?= $user['id'] ?>"><?= htmlspecialchars($user['username']) ?></a>
                </div>
                <div class="user-meta">
                    Joined <?= date('M Y', strtotime($user['created_at'])) ?>
                </div>
                <?php if($user['bio']): ?>
                    <div class="user-bio">
                        <?= htmlspecialchars(substr($user['bio'], 0, 60)) ?>
                        <?php if(strlen($user['bio']) > 60): ?>...<?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="alert alert-info" style="grid-column: 1/-1;">No users found</div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>