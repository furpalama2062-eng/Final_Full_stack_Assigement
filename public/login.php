<?php
require "../config/db.php";

// Start session safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$errors = [];
$email_raw = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email_raw = trim($_POST['email'] ?? '');
    $password  = trim($_POST['password'] ?? '');

    // Required fields check
    if (empty($email_raw) || empty($password)) {
        $errors[] = "* All fields are required";
    } else {

        // Validate email
        $email = filter_var($email_raw, FILTER_VALIDATE_EMAIL);
        if (!$email) {
            $errors[] = "* Invalid email address";
        } else {

            // Fetch admin from DB
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
                // ✅ ADMIN LOGIN SUCCESS
                $_SESSION['admin_id']   = $user['id'];
                $_SESSION['admin_name'] = $user['name'];

                // Regenerate session ID
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
    <title>Admin Login</title>
</head>
<body>

<h2>Admin Login</h2>

<!-- Display Errors -->
<?php foreach ($errors as $error): ?>
    <p style="color:red"><?= htmlspecialchars($error) ?></p>
<?php endforeach; ?>

<form method="POST">
    <input type="email" name="email" placeholder="Email"
           value="<?= htmlspecialchars($email_raw) ?>" required><br><br>

    <input type="password" name="password" placeholder="Password" required><br><br>

    <button type="submit">Login</button>
</form>
<a href="signup.php">New user</a>

</body>
</html>
