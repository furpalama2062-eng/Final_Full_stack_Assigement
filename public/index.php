<?php

require '../vendor/autoload.php';
require '../includes/admin_auth.php';
require "../config/db.php";

session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$csrf_token = $_SESSION['csrf_token'];

$sql = "SELECT * FROM books ORDER BY created_at ASC LIMIT 7";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = $pdo->query("SELECT COUNT(*) FROM books")->fetchColumn();
$total_users = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();
$total_categories = $pdo->query("SELECT COUNT(category) FROM books WHERE category IS NOT NULL")->fetchColumn();

$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader, [
    'cache' => false
]);

echo $twig->render('index.twig', [
    'books' => $books,
    'total' => $total,
    'total_users' => $total_users,
    'total_categories' => $total_categories,
    'csrf_token' => $csrf_token,
    'session' => $_SESSION
]);
