<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title . ' - ' : '' ?>1WEB</title>
    <link rel="icon" type="image/png" sizes="32x32" href="assets/favicon.png">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Source Sans Pro", Arial, sans-serif;
            background: #f6f8fa;
            color: #24292e;
            line-height: 1.5;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .header {
            background: white;
            border-bottom: 1px solid #e1e4e8;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
        }
        
        .logo-img {
            height: 40px;
            width: auto;
        }
        
        .logo-text {
            font-size: 1.5rem;
            font-weight: bold;
            color: #0366d6;
        }
        
        .nav {
            display: flex;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .nav a {
            color: #0366d6;
            text-decoration: none;
        }
        
        .nav a:hover {
            text-decoration: underline;
        }
        
        .btn {
            padding: 0.5rem 1rem;
            background: #0366d6;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn:hover {
            background: #0056b3;
        }
        
        .btn-danger {
            background: #d73a49;
        }
        
        .btn-danger:hover {
            background: #cb2431;
        }
        
        .btn-outline {
            background: transparent;
            border: 1px solid #0366d6;
            color: #0366d6;
        }
        
        .btn-outline:hover {
            background: #0366d6;
            color: white;
        }
        
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
            flex: 1;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        
        input, textarea, select {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #e1e4e8;
            border-radius: 6px;
            font-family: inherit;
        }
        
        input:focus, textarea:focus, select:focus {
            outline: none;
            border-color: #0366d6;
        }
        
        textarea {
            min-height: 100px;
            resize: vertical;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1rem;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 0 1rem;
            }
            
            .header {
                padding: 1rem;
                justify-content: center;
            }
            
            .nav {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="/" class="logo">
            <img src="assets/logo.png" alt="1web" class="logo-img" onerror="this.src='assets/logo-default.png'">
        </a>
        <div class="nav">
            <a href="/">Home</a>
            <a href="/users">Users</a>
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="/create_forum">Create</a>
                <a href="/profile">Profile</a>
                <a href="/logout" class="btn btn-outline" style="padding: 0.3rem 0.8rem;">Logout</a>
            <?php else: ?>
                <a href="/login">Login</a>
                <a href="/register" class="btn">Register</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="container">