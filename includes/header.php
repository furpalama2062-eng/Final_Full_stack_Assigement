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
</head>
<body>
<header>
    <nav style="padding:10px; background:#f2f2f2; margin-bottom:20px;">
        <a href="index.php" style="margin-right:15px;">Home</a>
        <a href="add.php" style="margin-right:15px;">Add Book</a>
        <a href="search.php">Search Books</a>
    </nav>
</header>
<main style="padding:0 20px;">
