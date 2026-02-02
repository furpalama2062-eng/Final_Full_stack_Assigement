<?php
require '../includes/admin_auth.php';
include '../includes/header.php';

$old_title = $_POST['title'] ?? '';
$old_author = $_POST['author'] ?? '';
$old_category = $_POST['category'] ?? '';
$old_year = $_POST['published_year'] ?? '';
$old_isbn = $_POST['isbn'] ?? '';

$csrf_token = $_SESSION['csrf_token'];
$message = "";
$msg_type = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("<p class='badge-danger' style='color:red;'>Invalid CSRF token</p>");
    }

    $title = ucfirst(trim($_POST['title']));
    $author = ucfirst(trim($_POST['author']));
    $category = trim($_POST['category']);
    $publisher_year = $_POST['published_year'];
    $isbn = $_POST['isbn'];
    $errors = [];

    if (empty($title) || empty($author) || empty($category) || empty($publisher_year) || empty($isbn)) {
        $errors[] = "All fields are required.";
    }

    if (strlen($title) < 2 || strlen($title) > 100) {
        $errors[] = "Title must be between 2 and 100 characters.";
    }

    if (!preg_match("/^[A-Za-z][A-Za-z\s\.\'-]{1,49}$/", $author)) {
        $errors[] = "Invalid author name format.";
    }

    if (!preg_match('/^\d{10}$|^\d{13}$/', $isbn)) {
        $errors[] = "ISBN must be 10 or 13 digits.";
    }

    if (!empty($errors)) {
        $msg_type = "error";
        $message = implode("<br>", $errors);
    } else {
        require "../config/db.php";

        $stmt_check = $pdo->prepare(
            "SELECT COUNT(*) FROM books WHERE title = :title AND author = :author"
        );
        $stmt_check->execute([
            ':title' => $title,
            ':author' => $author
        ]);

        if ($stmt_check->fetchColumn() > 0) {
            $msg_type = "error";
            $message = "This book already exists in the library.";
        } else {
            $stmt = $pdo->prepare(
                "INSERT INTO books (title, author, category, published_year, isbn)
                 VALUES (:title, :author, :category, :published_year, :isbn)"
            );

            $stmt->execute([
                ':title' => $title,
                ':author' => $author,
                ':category' => $category,
                ':published_year' => $publisher_year,
                ':isbn' => $isbn
            ]);

            $msg_type = "success";
            $message = "Book added successfully!";

            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $csrf_token = $_SESSION['csrf_token'];

            $old_title = $old_author = $old_category = $old_year = $old_isbn = '';
        }
    }
}
?>
<div class="add_section">
<div class="page-header">
    <h2 class="section-title">
        <i class="fa-solid fa-plus title-icon"></i>
        Add New Book
    </h2>
</div>

<div class="add-book-container">
    <?php if ($message): ?>
        <div class="alert-message alert-<?= $msg_type ?>">
            <div class="alert-icon">
                <?php if ($msg_type === 'success'): ?>
                    <i class="fa-solid fa-circle-check"></i>
                <?php else: ?>
                    <i class="fa-solid fa-circle-exclamation"></i>
                <?php endif; ?>
            </div>
            <div class="alert-content">
                <?= $message ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="form-card">
        <form method="post" action="" class="book-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

            <div class="form-row">
                <div class="form-group form-full">
                    <label class="form-label" for="title">
                        <i class="fa-solid fa-heading"></i> Book Title <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        id="title"
                        name="title"
                        class="form-input"
                        value="<?= htmlspecialchars($old_title) ?>"
                        required
                        placeholder="Enter the full book title"
                    >
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="author">
                        <i class="fa-solid fa-user-pen"></i> Author <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        id="author"
                        name="author"
                        class="form-input"
                        value="<?= htmlspecialchars($old_author) ?>"
                        required
                        placeholder="Author's full name"
                    >
                </div>

                <div class="form-group">
                    <label class="form-label" for="category">
                        <i class="fa-solid fa-tag"></i> Category <span class="required">*</span>
                    </label>
                    <select id="category" name="category" class="form-select" required>
                        <option value="">-- Select Category --</option>

                        <optgroup label="Fiction">
                            <option value="Adventure" <?= $old_category === 'Adventure' ? 'selected' : '' ?>>Adventure</option>
                            <option value="Fantasy" <?= $old_category === 'Fantasy' ? 'selected' : '' ?>>Fantasy</option>
                            <option value="Science Fiction" <?= $old_category === 'Science Fiction' ? 'selected' : '' ?>>Science Fiction</option>
                            <option value="Mystery" <?= $old_category === 'Mystery' ? 'selected' : '' ?>>Mystery</option>
                        </optgroup>

                        <optgroup label="Non-Fiction">
                            <option value="History" <?= $old_category === 'History' ? 'selected' : '' ?>>History</option>
                            <option value="Science & Technology" <?= $old_category === 'Science & Technology' ? 'selected' : '' ?>>Science & Technology</option>
                            <option value="Biography / Memoir" <?= $old_category === 'Biography / Memoir' ? 'selected' : '' ?>>Biography / Memoir</option>
                        </optgroup>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="published_year">
                        <i class="fa-solid fa-calendar"></i> Date Published <span class="required">*</span>
                    </label>
                    <input
                        type="date"
                        id="published_year"
                        name="published_year"
                        class="form-input"
                        value="<?= htmlspecialchars($old_year) ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label class="form-label" for="isbn">
                        <i class="fa-solid fa-barcode"></i> ISBN (10 or 13 digits) <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        id="isbn"
                        name="isbn"
                        class="form-input"
                        value="<?= htmlspecialchars($old_isbn) ?>"
                        placeholder="e.g. 9781234567890"
                        required
                    >
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit">
                    <i class="fa-solid fa-plus"></i>
                    <span>Add Book to Library</span>
                </button>
            </div>
        </form>
    </div>
</div>
</div>
<?php include '../includes/footer.php'; ?>