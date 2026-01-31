<?php
require '../includes/admin_auth.php';
include '../includes/header.php';
require "../config/db.php";

$id = $_GET['id'] ?? null;
if (!$id) die("Book ID not specified");

$stmt = $pdo->prepare("SELECT * FROM books WHERE id=?");
$stmt->execute([(int)$id]);
$book = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$book) die("Book not found");

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf_token']) die("Invalid CSRF token");

    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $category = trim($_POST['category']);
    $year = trim($_POST['published_year']);
    $isbn = trim($_POST['isbn']);

    if (!$title) $errors[] = "Title is required";
    if (!$author) $errors[] = "Author is required";
    if (!$category) $errors[] = "Category is required";
    if (!preg_match('/^\d{4}$/', $year)) $errors[] = "Published year must be 4 digits";
    if (!preg_match('/^\d{10}(\d{3})?$/', $isbn)) $errors[] = "ISBN must be 10 or 13 digits";

    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE books SET title=?, author=?, category=?, published_year=?, isbn=? WHERE id=?");
        $stmt->execute([$title, $author, $category, $year, $isbn, (int)$id]);
        echo "<p style='color:green;'>Book updated successfully!</p>";
    }
}
?>

<h2>Edit Book</h2>
<?php foreach ($errors as $error) echo "<p style='color:red;'>$error</p>"; ?>
<form method="post">
    <input type="hidden" name="csrf" value="<?= $_SESSION['csrf_token'] ?>">
    <label>Title:</label><br>
    <input type="text" name="title" value="<?= htmlspecialchars($book['title']) ?>"><br>
    <label>Author:</label><br>
    <input type="text" name="author" value="<?= htmlspecialchars($book['author']) ?>"><br>
    <label>Category:</label><br>
    <input type="text" name="category" value="<?= htmlspecialchars($book['category']) ?>"><br>
    <label>Published Year:</label><br>
    <input type="text" name="published_year" value="<?= htmlspecialchars($book['published_year']) ?>"><br>
    <label>ISBN:</label><br>
    <input type="text" name="isbn" value="<?= htmlspecialchars($book['isbn']) ?>"><br><br>
    <input type="submit" value="Update Book">
</form>
<?php include '../includes/footer.php'; ?>
