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
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf_token']) die("Invalid CSRF token");

    $title = ucfirst(trim($_POST['title']));
    $author = ucfirst(trim($_POST['author']));
    $category = trim($_POST['category']);
    $year = trim($_POST['published_year']);
    $isbn = trim($_POST['isbn']);

    // Validation
    if (empty($title) || empty($author) || empty($category) || empty($year) || empty($isbn)) {
        $errors[] = "All fields are required.";
    }

    if (strlen($title) < 2 || strlen($title) > 100) {
        $errors[] = "Title must be between 2 and 100 characters.";
    }
    if (!preg_match("/^[a-zA-Z0-9\s\-\.,'!?:#]+$/", $title)) {
        $errors[] = "* Title contains invalid characters";
    }

    if (!preg_match("/^[A-Za-z][A-Za-z\s\.\'-]{1,49}$/", $author)) {
        $errors[] = "Invalid author name format.";
    }

    if (strlen($author) < 2 || strlen($author) > 50){
        $errors[] = "Author name must be between 2 and 50 characters.";
    }
    
    if (!empty($year)) {
        $date = DateTime::createFromFormat('Y-m-d', $year);
        $today = new DateTime();
        if (!$date || $date->format('Y-m-d') !== $year) {
             $errors[] = "* Invalid published date format";
        } elseif ($date > $today) {
            $errors[] = "* Published year cannot be in the future";
        } elseif ((int)$date->format('Y') < 1500) {
            $errors[] = "* Published year is too old";
        }
    }

    if (!preg_match('/^\d{10}$|^\d{13}$/', $isbn)) {
        $errors[] = "ISBN must be 10 or 13 digits.";
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE books SET title=?, author=?, category=?, published_year=?, isbn=? WHERE id=?");
        $stmt->execute([$title, $author, $category, $year, $isbn, (int)$id]);
        $success = true;
        $stmt = $pdo->prepare("SELECT * FROM books WHERE id=?");
        $stmt->execute([(int)$id]);
        $book = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
<div class="edit_section">
<div class="edit-book-page">
    <div class="page-header">
        <h2 class="section-title"><i class="fas fa-edit title-icon"></i>Edit Book</h2>
    </div>
    
    <?php if ($success): ?>
        <div class="alert-message alert-success">
            <i class="fas fa-check-circle alert-icon"></i>
            <div class="alert-content">Book updated successfully!</div>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($errors)): ?>
        <div class="edit-errors">
            <?php foreach ($errors as $error): ?>
                <div class="alert-message alert-error">
                    <i class="fas fa-exclamation-circle alert-icon"></i>
                    <div class="alert-content"><?= htmlspecialchars($error) ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <div class="edit-form-container">
        <div class="form-card">
            <form method="post" class="book-form edit-form">
                <input type="hidden" name="csrf" value="<?= $_SESSION['csrf_token'] ?>">
                
                <div class="form-row">
                    <div class="form-group form-full">
                        <label class="form-label">
                            <i class="fas fa-book"></i>
                            Title<span class="required">*</span>
                        </label>
                        <input type="text" name="title" class="form-input" value="<?= htmlspecialchars($book['title']) ?>" required placeholder="Enter the full book title">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-user"></i>
                            Author<span class="required">*</span>
                        </label>
                        <input type="text" name="author" class="form-input" value="<?= htmlspecialchars($book['author']) ?>" required placeholder="Author's full name">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-tag"></i>
                            Category<span class="required">*</span>
                        </label>
                        <select name="category" class="form-select" required>
                            <option value="">-- Select Category --</option>
                            
                            <optgroup label="Fiction">
                                <option value="Adventure" <?= $book['category'] === 'Adventure' ? 'selected' : '' ?>>Adventure</option>
                                <option value="Fantasy" <?= $book['category'] === 'Fantasy' ? 'selected' : '' ?>>Fantasy</option>
                                <option value="Science Fiction" <?= $book['category'] === 'Science Fiction' ? 'selected' : '' ?>>Science Fiction</option>
                                <option value="Mystery" <?= $book['category'] === 'Mystery' ? 'selected' : '' ?>>Mystery</option>
                            </optgroup>
                            
                            <optgroup label="Non-Fiction">
                                <option value="History" <?= $book['category'] === 'History' ? 'selected' : '' ?>>History</option>
                                <option value="Science & Technology" <?= $book['category'] === 'Science & Technology' ? 'selected' : '' ?>>Science & Technology</option>
                                <option value="Biography / Memoir" <?= $book['category'] === 'Biography / Memoir' ? 'selected' : '' ?>>Biography / Memoir</option>
                            </optgroup>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-calendar"></i>
                            Date Published<span class="required">*</span>
                        </label>
                        <input type="date" name="published_year" class="form-input" value="<?= htmlspecialchars($book['published_year']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-barcode"></i>
                            ISBN (10 or 13 digits)<span class="required">*</span>
                        </label>
                        <input type="text" name="isbn" class="form-input" value="<?= htmlspecialchars($book['isbn']) ?>" required placeholder="e.g. 9781234567890">
                    </div>
                </div>
                
                <div class="form-actions">
                    <a href="index.php" class="btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i> Update Book
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>

<?php include '../includes/footer.php'; ?>