<?php
require '../includes/admin_auth.php';
require "../config/db.php";
require_once '../vendor/autoload.php'; // Composer autoload for Twig

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// Fetch all books
$stmt = $pdo->prepare("SELECT * FROM books ORDER BY id DESC");
$stmt->execute();
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Twig setup
$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader);

// Render template
echo $twig->render('books_dashboard.twig', [
    'books' => $books,
    'csrf_token' => $csrf_token,
    'admin_name' => $_SESSION['admin_name'] ?? null
]);
