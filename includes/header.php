<?php
// Start session at the very top
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Generate CSRF token if not exists
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<header>
    <div>
    <i class="fa-solid fa-book book-icon"></i>
    <p>Library Hub</p>
    </div>
    <?php if (isset($_SESSION['admin_id'])): ?>
        <div class="admin">
            <img src="../assets/image/pngkey.com-no-image-png-1219231.png" alt="Profile" class="profile-pic">
            <span>Welcome, <?= htmlspecialchars($_SESSION['admin_name']) ?></span>
        </div>
    <?php endif; ?>
</header>
<div class="slide">
    <ul>
        <li><a href="#"><i class="fas fa-tv"></i>Dashboard</a></li>
        <li><a href="#"><i class="fa-solid fa-book"></i>Search Books</a></li>
        <li><a href="#"><i class="fa-solid fa-pen-to-square"></i>Edit books</a></li>
        <li><a href="#"><i class="fa-solid fa-folder"></i>Category</a></li>
        <li><a href="#"><i class="fa-solid fa-user"></i>User</a></li>
    </ul>
       <?php if (isset($_SESSION['admin_id'])): ?>
            <a href="logout.php" class="logout"> <i class="fa-solid fa-right-from-bracket"></i>Logout</a>
        <?php endif; ?>

</div>

<main style="padding: 20px; margin-left: 300px;">
