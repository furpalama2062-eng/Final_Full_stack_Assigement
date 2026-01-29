<?php
require "../config/db.php"; // DB connection

$search_title = $_GET['title'] ?? '';

if ($search_title) {
    $stmt = $pdo->prepare("SELECT DISTINCT title FROM books WHERE title LIKE ? LIMIT 10");
    $stmt->execute([$search_title . '%']);
    $titles = $stmt->fetchAll(PDO::FETCH_COLUMN); // simple array
    header('Content-Type: application/json');
    echo json_encode($titles);
    exit;
}
