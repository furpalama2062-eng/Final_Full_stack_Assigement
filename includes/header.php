<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<header class="main-header">
    <div class="header-brand">
        <i class="fa-solid fa-book book-icon"></i>
        <p class="brand-text">Library Hub</p>
    </div>
    <?php if (isset($_SESSION['admin_id'])): ?>
        <div class="admin-profile">
            <img src="../assets/image/pngkey.com-no-image-png-1219231.png" alt="Profile" class="profile-pic">
            <span class="admin-name">Welcome, <?= htmlspecialchars($_SESSION['admin_name']) ?></span>
        </div>
    <?php endif; ?>
</header>

<div class="sidebar">
    <nav class="sidebar-nav">
        <ul class="nav-list">
            <li class="nav-item"><a href="index.php" class="nav-link"><i class="fas fa-tv"></i><span>Dashboard</span></a></li>
            <li class="nav-item"><a href="search.php" class="nav-link"><i class="fa-solid fa-magnifying-glass"></i><span>Search Books</span></a></li>
            <li class="nav-item"><a href="add.php" class="nav-link"><i class="fa-solid fa-plus"></i><span>Add Book</span></a></li>
            <li class="nav-item"><a href="#" class="nav-link"><i class="fa-solid fa-folder"></i><span>Category</span></a></li>
            <li class="nav-item"><a href="users.php" class="nav-link"><i class="fa-solid fa-user"></i><span>Users</span></a></li>
        </ul>
    </nav>

    <?php if (isset($_SESSION['admin_id'])): ?>
        <a href="logout.php" class="logout-btn">
            <i class="fa-solid fa-right-from-bracket"></i>
            <span>Logout</span>
        </a>
    <?php endif; ?>
</div>

<main class="main-content"></main>