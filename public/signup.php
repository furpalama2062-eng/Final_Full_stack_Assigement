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

    // Validation
    if (empty($name) || empty($email_raw) || empty($pass)) {
        $errors[] = "* All fields are required";
    }
    if(strlen($name) <= 3) {
        $errors[] = "*Name must be at least 3 characters";
    }
    if (!$email) {
        $errors[] = "*Invalid email address";
    }
    if(strlen($pass) <= 6 ) {
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

    // Check duplicate email
    if($email) {
        $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);
        if ($check->rowCount() > 0) {
            $errors[] = "*Email already registered";
        }
    }

    // Insert user if no errors
    if(empty($errors)) {
        try {
            $hashed = password_hash($pass, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            if ($stmt->execute([$name, $email, $hashed])) {
                $message = "Registration successful. You can login now.";
                // Clear old inputs after successful registration
                $name = '';
                $email_raw = '';
            }
        } catch(PDOException $e) {
            $errors[] = "Something went wrong";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>User Signup</title>
</head>
<body>

<h2>Signup</h2>

<!-- Display Errors -->
<?php foreach($errors as $error): ?>
    <p style="color:red"><?= htmlspecialchars($error) ?></p>
<?php endforeach; ?>

<!-- Display Success Message -->
<?php if($message): ?>
    <p style="color:green"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<form method="POST">
    <input type="text" name="name" placeholder="Full Name" value="<?= htmlspecialchars($name) ?>" required><br><br>
    <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($email_raw) ?>" required><br><br>
    <input type="password" name="password" placeholder="Password" required><br><br>
    <button type="submit">Sign Up</button>
</form>

<p>Already have an account? <a href="login.php">Login</a></p>

</body>
</html>
