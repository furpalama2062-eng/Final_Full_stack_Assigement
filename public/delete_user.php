<?php
require '../includes/admin_auth.php';
require '../config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* Only POST allowed */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: users.php');
    exit;
}

/* CSRF Validation */
if (
    empty($_POST['csrf_token']) ||
    empty($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    die('Invalid CSRF token');
}

/* Validate ID */
if (!isset($_POST['id']) || !filter_var($_POST['id'], FILTER_VALIDATE_INT)) {
    header('Location: users.php');
    exit;
}

$user_id = (int) $_POST['id'];

/* Prevent deleting admin */
$check = $pdo->prepare("SELECT role FROM users WHERE id = ?");
$check->execute([$user_id]);
$user = $check->fetch(PDO::FETCH_ASSOC);

if (!$user || $user['role'] === 'admin') {
    header('Location: users.php');
    exit;
}

/* Delete user (SQL Injection safe) */
$delete = $pdo->prepare("DELETE FROM users WHERE id = ?");
$delete->execute([$user_id]);

header('Location: users.php?deleted=1');
exit;
