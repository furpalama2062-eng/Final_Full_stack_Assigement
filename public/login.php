<?php
require "../config/db.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$errors = [];
$email_raw = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email_raw = trim($_POST['email'] ?? '');
    $password  = trim($_POST['password'] ?? '');

    if (empty($email_raw) || empty($password)) {
        $errors[] = "* All fields are required";
    } else {
        $email = filter_var($email_raw, FILTER_VALIDATE_EMAIL);
        if (!$email) {
            $errors[] = "* Invalid email address";
        } else {
            $stmt = $pdo->prepare(
                "SELECT id, name, password, role FROM users WHERE email = ? LIMIT 1"
            );
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user || !password_verify($password, $user['password'])) {
                $errors[] = "* Email or password incorrect";
            } elseif ($user['role'] !== 'admin') {
                $errors[] = "* Access denied. Admin only.";
            } else {
                $_SESSION['admin_id']   = $user['id'];
                $_SESSION['admin_name'] = $user['name'];

                session_regenerate_id(true);

                header("Location: index.php");
                exit;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="../assets/css/logstyle.css">
</head>
<body>
    <div class="auth-container">
        <div class="form-container">
            <h2 class="form-title">Admin Login</h2>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <?php foreach ($errors as $error): ?>
                        <p><?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="signup-form">
                <div class="form-group">
                    <input 
                        type="email" 
                        id="email"
                        name="email" 
                        class="form-input" 
                        placeholder="Email" 
                        value="<?= htmlspecialchars($email_raw) ?>" 
                        required
                    >
                </div>

                <div class="form-group">
                    <input 
                        type="password" 
                        id="password"
                        name="password" 
                        class="form-input" 
                        placeholder="Password" 
                        required
                    >
                </div>

                <button type="submit" class="btn-primary">Login</button>
            </form>

            <div class="form-footer">
                <p class="footer-text">New user? <a href="signup.php" class="link">Create Account</a></p>
            </div>
        </div>
    </div>
</body>
</html>