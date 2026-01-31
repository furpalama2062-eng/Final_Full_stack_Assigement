<?php
require '../includes/admin_auth.php';
include '../includes/header.php';
require "../config/db.php";

// CSRF token for delete links
$csrf_token = $_SESSION['csrf_token'] ?? bin2hex(random_bytes(32));

// Fetch all books dynamically
$sql = "SELECT * FROM books ORDER BY id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Dashboard - Books</h2>

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
