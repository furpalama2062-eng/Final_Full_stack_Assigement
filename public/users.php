<?php
require '../includes/admin_auth.php';
require '../config/db.php';
include '../includes/header.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

$sql = "SELECT id, name, email, role, created_at 
        FROM users 
        WHERE role = 'user'
        ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="main_contatin">

<div class="page-header">
    <h2 class="section-title">
        <i class="fa-solid fa-users"></i>
        User Management
    </h2>
</div>

<div class="table-container">
<?php if ($users): ?>
    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th class="action-header">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <?php $id = (int) $user['id']; ?>
                    <tr>
                        <td><?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <span class="role-badge role-<?= htmlspecialchars($user['role']) ?>">
                                <?= htmlspecialchars($user['role']) ?>
                            </span>
                        </td>
                        <td class="action-cell">
                            <?php if ($user['role'] !== 'admin'): ?>
                                <form action="delete_user.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="id" value="<?= $id ?>">
                                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                    <button type="submit"
                                            class="btn-action btn-delete"
                                            onclick="return confirm('Are you sure you want to delete this user?')">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            <?php else: ?>
                                <span class="disabled-text">Protected</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="empty-state">
        <i class="fa-solid fa-user-slash"></i>
        <p>No users found.</p>
    </div>
<?php endif; ?>
</div>

</main>

<?php include '../includes/footer.php'; ?>
