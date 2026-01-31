<?php
require '../includes/admin_auth.php';
include '../includes/header.php'; // session already started in header

$old_title = $_POST['title'] ?? '';
$old_author = $_POST['author'] ?? '';
$old_category = $_POST['category'] ?? '';
$old_year = $_POST['published_year'] ?? '';
$old_isbn = $_POST['isbn'] ?? '';

// CSRF token is already generated in header.php
$csrf_token = $_SESSION['csrf_token'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("<p style='color:red;'>Invalid CSRF token</p>");
    }

    $title = ucfirst(trim($_POST['title']));
    $author = ucfirst(trim($_POST['author']));
    $category = trim($_POST['category']);
    $publisher_year = $_POST['published_year'];
    $isbn = $_POST['isbn'];
    $errors = [];

    if (empty($title) || empty($author) || empty($category) || empty($publisher_year) || empty($isbn)) {
        $errors[] = "* All fields are required";
    }

    if (strlen($title) < 2) $errors[] = "* Title is too short";
    if (strlen($title) > 100) $errors[] = "* Title is too long";
    if (!preg_match("/^[a-zA-Z0-9\s\-\.,'!?:#]+$/", $title)) $errors[] = "* Title contains invalid characters";

    if (!preg_match("/^[A-Za-z][A-Za-z\s\.\'-]{1,49}$/", $author)) $errors[] = "* Author name contains invalid characters";
    if (strlen($author) < 2) $errors[] = "* Author name is too short";
    if (strlen($author) > 50) $errors[] = "* Author name is too long";

    if (!empty($publisher_year)) {
        $date = DateTime::createFromFormat('Y-m-d', $publisher_year);
        $today = new DateTime();
        if (!$date || $date->format('Y-m-d') !== $publisher_year) $errors[] = "* Invalid published date format";
        elseif ($date > $today) $errors[] = "* Published year cannot be in the future";
        elseif ((int)$date->format('Y') < 1500) $errors[] = "* Published year is too old";
    }

    if (!preg_match('/^\d{10}$|^\d{13}$/', $isbn)) $errors[] = "* ISBN must be 10 or 13 digits";

    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p style='color:red;'>" . htmlspecialchars($error) . "</p>";
        }
    } else {
        require "../config/db.php";

        $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM books WHERE title = :title AND author = :author");
        $stmt_check->execute([':title' => $title, ':author' => $author]);

        if ($stmt_check->fetchColumn() > 0) {
            echo "<p style='color:red;'>* Book already exists </p>";
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO books (title, author, category, published_year, isbn) 
                               VALUES (:title, :author, :category, :published_year, :isbn)");
        $stmt->execute([
            ':title' => $title,
            ':author' => $author,
            ':category' => $category,
            ':published_year' => $publisher_year,
            ':isbn' => $isbn
        ]);

        echo "<p style='color:green;'>Book added successfully!</p>";

        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $csrf_token = $_SESSION['csrf_token'];
    }
}
?>
<form method="post">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
    <label>Title:</label>
    <input type="text" name="title" value="<?= htmlspecialchars($old_title) ?>" required><br>
    <label>Author:</label>
    <input type="text" name="author" value="<?= htmlspecialchars($old_author) ?>" required><br>
    <label>Category:</label>
    <select name="category" required>
        <option value="">--Select category--</option>
        <optgroup label="Fiction">
            <option value="Adventure">Adventure</option>
            <option value="Fantasy">Fantasy</option>
            <option value="Science Fiction">Science Fiction</option>
            <option value="Romance">Romance</option>
            <option value="Mystery">Mystery</option>
        </optgroup>
        <optgroup label="Non-Fiction">
            <option value="Biography / Memoir">Biography / Memoir</option>
            <option value="Self-Help">Self-Help</option>
            <option value="Science & Technology">Science & Technology</option>
            <option value="History">History</option>
            <option value="Travel">Travel</option>
        </optgroup>
        <optgroup label="Children & Young Adult">
            <option value="Picture Books">Picture Books</option>
            <option value="Middle Grade">Middle Grade</option>
            <option value="Young Adult">Young Adult</option>
        </optgroup>
        <optgroup label="Others">
            <option value="Poetry">Poetry</option>
            <option value="Graphic Novels / Comics">Graphic Novels / Comics</option>
            <option value="Cookbooks">Cookbooks</option>
        </optgroup>
    </select><br>
    <label>Published Year:</label>
    <input type="date" name="published_year" value="<?= htmlspecialchars($old_year) ?>" required><br>
    <label>ISBN:</label>
    <input type="text" name="isbn" value="<?= htmlspecialchars($old_isbn) ?>" placeholder="Enter ISBN"><br>
    <button type="submit">Add book</button>
</form>
<?php include '../includes/footer.php'; ?>
