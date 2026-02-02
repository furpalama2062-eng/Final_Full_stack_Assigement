<?php
session_start();
require '../config/db.php';

$errors = [];
$message = '';
$name = '';
$email_raw = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name      = ucfirst(trim($_POST['name'] ?? ''));
    $email_raw = trim($_POST['email'] ?? '');
    $email     = filter_var($email_raw, FILTER_VALIDATE_EMAIL);
    $pass      = $_POST['password'] ?? '';
    $confirm_pass = $_POST['confirm-password'] ?? '';

    if (empty($name) || empty($email_raw) || empty($pass)) {
        $errors[] = "* All fields are required";
    }

    if (strlen($name) <= 3) {
        $errors[] = "*Name must be at least 3 characters";
    }

    if (!$email) {
        $errors[] = "*Invalid email address";
    }

    if (strlen($pass) <= 6) {
        $errors[] = "*Password must be at least 6 characters";
    }

    if (!preg_match('/[0-9]/', $pass)) {
        $errors[] = "*Password must contain at least one number";
    }

    if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $pass)) {
        $errors[] = "*Password must contain at least one special character";
    }

    if (!preg_match('/[A-Z]/', $pass)) {
        $errors[] = "*Password must contain at least one uppercase letter";
    }

    if (!preg_match('/[a-z]/', $pass)) {
        $errors[] = "*Password must contain at least one lowercase letter";
    }

    if ($pass !== $confirm_pass) {
        $errors[] = "*Password and Confirm Password do not match";
    }

    if ($email) {
        $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);
        if ($check->rowCount() > 0) {
            $errors[] = "*Email already registered";
        }
    }

    if (empty($errors)) {
        try {
            $hashed = password_hash($pass, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            if ($stmt->execute([$name, $email, $hashed])) {
                $message = "Registration successful. You can login now.";
                $name = '';
                $email_raw = '';
            }
        } catch (PDOException $e) {
            $errors[] = "Something went wrong";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>
    <link rel="stylesheet" href="../assets/css/logstyle.css">
</head>
<body>
    <div class="auth-container">
        <div class="form-container">
            <h2 class="form-title">Create Account</h2>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <?php foreach ($errors as $error): ?>
                        <p><?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($message): ?>
                <div class="alert alert-success">
                    <p><?= htmlspecialchars($message) ?></p>
                </div>
            <?php endif; ?>

            <form method="POST" class="signup-form">
                <div class="form-group">
                    <input 
                        type="text" 
                        id="name"
                        name="name" 
                        class="form-input" 
                        placeholder="Name" 
                        value="<?= htmlspecialchars($name) ?>" 
                        required
                    >
                </div>

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

                <div class="form-group">
                    <input 
                        type="password" 
                        id="confirm-password"
                        name="confirm-password"
                        class="form-input" 
                        placeholder="Confirm Password" 
                        required
                    >
                </div>

                <div class="form-group checkbox-group">
                    <label class="checkbox-label">
                        <input type="checkbox" class="checkbox-input" required>
                        <span class="checkbox-text">I agree and Approved to the Terms of Service and Privacy Policy</span>
                    </label>
                </div>

                <button type="submit" class="btn-primary">Create Account</button>
            </form>

            <div class="form-footer">
                <p class="footer-text">Already have an account? <a href="login.php" class="link">Log In</a></p>
            </div>
        </div>
    </div>
</body>
</html>
