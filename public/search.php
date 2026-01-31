<?php
require '../includes/admin_auth.php';
include '../includes/header.php';
require "../config/db.php";

// CSRF token for delete links
$csrf_token = $_SESSION['csrf_token'] ?? bin2hex(random_bytes(32));

// Get search parameters from form
$search_title    = $_GET['title'] ?? '';
$search_category = $_GET['category'] ?? '';
$search_year     = $_GET['year'] ?? '';

// Fetch books dynamically
$sql = "SELECT * FROM books WHERE 1=1";
$params = [];
if ($search_title) { $sql .= " AND title LIKE ?"; $params[] = "%$search_title%"; }
if ($search_category) { $sql .= " AND category = ?"; $params[] = $search_category; }
if ($search_year) { $sql .= " AND published_year = ?"; $params[] = $search_year; }

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Dashboard - Books</h2>
<form id="searchForm" method="GET" autocomplete="off">
    <label>Title:</label><br>
    <div style="position: relative; display: inline-block; width: 300px;">
        <input type="text" id="title" name="title" value="<?= htmlspecialchars($search_title) ?>" style="width: 100%;" placeholder="Enter title...">
        <!-- Suggestions container below title input -->
        <div id="title-suggestions" style="position: absolute; top: 100%; left: 0; right: 0; border: 1px solid #ccc; background: #fff; z-index: 10; display: none;"></div>
    </div>
    <br><br>

    <label>Category:</label><br>
    <select id="category" name="category">
        <option value="">--Any Category--</option>
        <optgroup label="Fiction">
            <option value="Adventure" <?= $search_category=='Adventure'?'selected':'' ?>>Adventure</option>
            <option value="Fantasy" <?= $search_category=='Fantasy'?'selected':'' ?>>Fantasy</option>
            <option value="Science Fiction" <?= $search_category=='Science Fiction'?'selected':'' ?>>Science Fiction</option>
            <option value="Romance" <?= $search_category=='Romance'?'selected':'' ?>>Romance</option>
            <option value="Mystery" <?= $search_category=='Mystery'?'selected':'' ?>>Mystery</option>
        </optgroup>
        <optgroup label="Non-Fiction">
            <option value="Biography / Memoir" <?= $search_category=='Biography / Memoir'?'selected':'' ?>>Biography / Memoir</option>
            <option value="Self-Help" <?= $search_category=='Self-Help'?'selected':'' ?>>Self-Help</option>
            <option value="Science & Technology" <?= $search_category=='Science & Technology'?'selected':'' ?>>Science & Technology</option>
            <option value="History" <?= $search_category=='History'?'selected':'' ?>>History</option>
            <option value="Travel" <?= $search_category=='Travel'?'selected':'' ?>>Travel</option>
        </optgroup>
        <optgroup label="Children & Young Adult">
            <option value="Picture Books" <?= $search_category=='Picture Books'?'selected':'' ?>>Picture Books</option>
            <option value="Middle Grade" <?= $search_category=='Middle Grade'?'selected':'' ?>>Middle Grade</option>
            <option value="Young Adult" <?= $search_category=='Young Adult'?'selected':'' ?>>Young Adult</option>
        </optgroup>
        <optgroup label="Others">
            <option value="Poetry" <?= $search_category=='Poetry'?'selected':'' ?>>Poetry</option>
            <option value="Graphic Novels / Comics" <?= $search_category=='Graphic Novels / Comics'?'selected':'' ?>>Graphic Novels / Comics</option>
            <option value="Cookbooks" <?= $search_category=='Cookbooks'?'selected':'' ?>>Cookbooks</option>
        </optgroup>
    </select><br><br>

    <label>Published Year:</label><br>
    <input type="text" id="year" name="year" placeholder="YYYY" value="<?= htmlspecialchars($search_year) ?>"><br><br>

    <button type="submit">Search</button>
</form>

<div id="results">
<?php
if ($books) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr>
            <th>ID</th><th>Title</th><th>Author</th><th>Category</th>
            <th>Published Year</th><th>ISBN</th><th>Action</th>
          </tr>";
    foreach ($books as $row) {
        $id = (int)$row['id'];
        echo "<tr>
                <td>{$id}</td>
                <td>" . htmlspecialchars($row['title']) . "</td>
                <td>" . htmlspecialchars($row['author']) . "</td>
                <td>" . htmlspecialchars($row['category']) . "</td>
                <td>" . htmlspecialchars($row['published_year']) . "</td>
                <td>" . htmlspecialchars($row['isbn']) . "</td>
                <td>
                    <a href='edit.php?id={$id}'>Edit</a> | 
                    <a href='delete.php?id={$id}&csrf={$csrf_token}' onclick=\"return confirm('Are you sure?')\">Delete</a>
                </td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "<p>No books found.</p>";
}
?>
</div>
<?php include '../includes/footer.php'; ?>