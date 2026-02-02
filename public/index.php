<?php
require '../includes/admin_auth.php';
include '../includes/header.php';
require "../config/db.php";

$csrf_token = $_SESSION['csrf_token'] ?? bin2hex(random_bytes(32));

$sql = "SELECT * FROM books ORDER BY created_at ASC LIMIT 7";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = $pdo->query("SELECT COUNT(*) FROM books")->fetchColumn();
$total_users = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();
$total_categories = $pdo->query("SELECT COUNT(category) FROM books WHERE category IS NOT NULL")->fetchColumn();
?>
<main class="main_contatin">
<div class="page-header">
    <h2 class="section-title">
        <i class="fas fa-chart-line title-icon"></i>
        Dashboard Overview
    </h2>
</div>

<div class="stats-container">
    <div class="stat-card stat-primary">
        <div class="stat-icon">
            <i class="fa-solid fa-book"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Total Books</div>
            <div class="stat-value"><?= htmlspecialchars($total) ?></div>
        </div>
    </div>

    <div class="stat-card stat-success">
        <div class="stat-icon">
            <i class="fa-solid fa-users"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Total Users</div>
            <div class="stat-value"><?= htmlspecialchars($total_users) ?></div>
        </div>
    </div>

    <div class="stat-card stat-info">
        <div class="stat-icon">
            <i class="fa-solid fa-layer-group"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Total Categories</div>
            <div class="stat-value"><?= htmlspecialchars($total_categories) ?></div>
        </div>
    </div>
</div>

<div class="page-header" style="margin-top: 48px;">
    <h2 class="section-title">
        <i class="fas fa-book-open title-icon"></i>
        Recently Added Books
    </h2>
</div>

<div class="table-container">
<?php if ($books): ?>
    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th><i class="fa-solid fa-heading"></i> Title</th>
                    <th><i class="fa-solid fa-user-pen"></i> Author</th>
                    <th><i class="fa-solid fa-tag"></i> Category</th>
                    <th><i class="fa-solid fa-calendar"></i> Published Year</th>
                    <th><i class="fa-solid fa-barcode"></i> ISBN</th>
                    <th class="action-header"><i class="fa-solid fa-sliders"></i> Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($books as $row): ?>
                    <?php $id = (int) $row['id']; ?>
                    <tr class="table-row">
                        <td class="book-title"><?= htmlspecialchars($row['title']) ?></td>
                        <td><?= htmlspecialchars($row['author']) ?></td>
                        <td><span class="category-badge"><?= htmlspecialchars($row['category']) ?></span></td>
                        <td><?= htmlspecialchars($row['published_year']) ?></td>
                        <td class="isbn-cell"><?= htmlspecialchars($row['isbn']) ?></td>
                        <td class="action-cell">
                            <a href="edit.php?id=<?= $id ?>" class="btn-action btn-edit" title="Edit Record">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <a href="delete.php?id=<?= $id ?>&csrf=<?= $csrf_token ?>"
                               class="btn-action btn-delete"
                               title="Delete Record"
                               onclick="return confirm('Are you sure you want to delete this book?')">
                                <i class="fa-solid fa-trash-can"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="empty-state">
        <i class="fa-solid fa-book-open-reader"></i>
        <p class="empty-text">No books found in the library.</p>
        <a href="add.php" class="btn-primary">
            <i class="fa-solid fa-plus"></i> Add Your First Book
        </a>
    </div>
<?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>