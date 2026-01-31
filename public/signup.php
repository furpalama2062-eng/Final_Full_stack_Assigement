<?php
session_start();
require '../config/db.php';

$errors = [];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = ucfirst(trim($_POST['name'] ?? ''));
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $email_raw = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';

    // Validation
    if (empty($name) || empty($email_raw) || empty($pass)) {
        $errors[] = "* All field are Required";
    }
    if(strlen($name) <= 3) {
        $errors[] = "*Name must be at least 3 characters ";
    }
    if (!$email) {
        $errors[] = "*Invalid email address";
    }
    if(strlen($pass) <= 6 ) {
        $errors[] = "*Password must be at least 6 characters ";
    }

    if (!preg_match('/[0-9]/', $pass)) {
        $errors[] = "*Password must contain Numerical number";
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


     $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
     $check->execute([$email]);
     
    if ($check->rowCount() > 0) {
        $errors[] = "Email already registered";
    }

    if(!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p style = 'color:red'>$error</p>";
        }
    }  else {
        try {
        // Hash password
            $hashed = password_hash($pass, PASSWORD_DEFAULT);

            // Insert USER ONLY (role not included)
            $stmt = $pdo->prepare(
                "INSERT INTO users (name, email, password) VALUES (?, ?, ?)"
            );

            if ($stmt->execute([$name, $email, $hashed])) {
                $message = "Registration successful. You can login now.";
            } } catch(PDOException $e) {
                $message = "Something went wrong";
    }
       if ($message) {
    echo "<p style='color:green'>$message</p>";
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
<form method="POST">
    <input type="text" name="name" placeholder="Full Name" required><br><br>
    <input type="email" name="email" placeholder="Email" required><br><br>
    <input type="password" name="password" placeholder="Password" required><br><br>
    <button type="submit">Sign Up</button>
</form>

<p>Already have an account? <a href="login.php">Login</a></p>

</body>
</html>
