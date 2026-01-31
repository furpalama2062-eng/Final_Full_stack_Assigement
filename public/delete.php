<?php
require '../includes/admin_auth.php';
include '../includes/header.php';
require "../config/db.php";

$id = $_GET['id'] ?? null;
$csrf = $_GET['csrf'] ?? '';

if (!$id || !$csrf || $csrf !== $_SESSION['csrf_token']) die("Invalid CSRF token");

$stmt = $pdo->prepare("DELETE FROM books WHERE id=?");
$stmt->execute([(int)$id]);

header("Location: index.php");
exit;
