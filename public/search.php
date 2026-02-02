<?php
require '../includes/admin_auth.php';
include '../includes/header.php';
require "../config/db.php";

$csrf_token = $_SESSION['csrf_token'] ?? bin2hex(random_bytes(32));

$search_title    = $_GET['title'] ?? '';
$search_category = $_GET['category'] ?? '';
$search_year     = $_GET['year'] ?? '';

$sql = "SELECT * FROM books WHERE 1=1";
$params = [];
if ($search_title) { $sql .= " AND title LIKE ?"; $params[] = "%$search_title%"; }
if ($search_category) { $sql .= " AND category = ?"; $params[] = $search_category; }
if ($search_year) { $sql .= " AND published_year = ?"; $params[] = $search_year; }

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="Search_Section">
<div class="page-header">
    <h2 class="section-title">
        <i class="fa-solid fa-magnifying-glass title-icon"></i>
        Search Books
    </h2>
</div>

<div class="search-box">
    <form id="searchForm" method="GET" autocomplete="off" class="search-form">
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label" for="title">
                    <i class="fa-solid fa-heading"></i> Book Title
                </label>
                <div class="input-wrapper">
                    <input 
                        type="text" 
                        id="title" 
                        name="title" 
                        class="form-input"
                        value="<?= htmlspecialchars($search_title) ?>" 
                        placeholder="Enter book title...">
                    <div id="title-suggestions" class="suggestions-dropdown"></div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="category">
                    <i class="fa-solid fa-tag"></i> Category
                </label>
                <select id="category" name="category" class="form-select">
                    <option value="">All Categories</option>
                    <optgroup label="Fiction">
                        <option value="Adventure" <?= $search_category=='Adventure'?'selected':'' ?>>Adventure</option>
                        <option value="Fantasy" <?= $search_category=='Fantasy'?'selected':'' ?>>Fantasy</option>
                        <option value="Science Fiction" <?= $search_category=='Science Fiction'?'selected':'' ?>>Science Fiction</option>
                        <option value="Romance" <?= $search_category=='Romance'?'selected':'' ?>>Romance</option>
                        <option value="Mystery" <?= $search_category=='Mystery'?'selected':'' ?>>Mystery</option>
                    </optgroup>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="year">
                    <i class="fa-solid fa-calendar"></i> Year
                </label>
                <input 
                    type="text" 
                    id="year" 
                    name="year" 
                    class="form-input"
                    placeholder="YYYY" 
                    value="<?= htmlspecialchars($search_year) ?>">
            </div>

            <div class="form-group form-action">
                <div class="btn_1">
                <button type="submit" class="btn-search">
                    <i class="fa-solid fa-magnifying-glass"></i> 
                    <span>Search</span>
                </button>
                </div>
            </div>
        </div>
    </form>
</div>


<div class="table-container">
<?php
if ($books) {
    echo '<div class="results-header">';
    echo '<p class="results-count"><i class="fa-solid fa-check-circle"></i> Found ' . count($books) . ' book(s) matching your criteria</p>';
    echo '</div>';
    
    echo '<div class="table-wrapper">';
    echo "<table class='data-table'>";
    echo "<thead>
            <tr>
                <th><i class='fa-solid fa-heading'></i> Title</th>
                <th><i class='fa-solid fa-user-pen'></i> Author</th>
                <th><i class='fa-solid fa-tag'></i> Category</th>
                <th><i class='fa-solid fa-calendar'></i> Year</th>
                <th><i class='fa-solid fa-barcode'></i> ISBN</th>
                <th class='action-header'><i class='fa-solid fa-sliders'></i> Actions</th>
            </tr>
          </thead><tbody>";
    foreach ($books as $row) {
        $id = (int)$row['id'];
        echo "<tr class='table-row'>
                <td class='book-title'>" . htmlspecialchars($row['title']) . "</td>
                <td>" . htmlspecialchars($row['author']) . "</td>
                <td><span class='category-badge'>" . htmlspecialchars($row['category']) . "</span></td>
                <td>" . htmlspecialchars($row['published_year']) . "</td>
                <td class='isbn-cell'>" . htmlspecialchars($row['isbn']) . "</td>
                <td class='action-cell'>
                    <a href='edit.php?id={$id}' class='btn-action btn-edit' title='Edit Book'>
                        <i class='fa-solid fa-pen-to-square'></i>
                    </a>
                    <a href='delete.php?id={$id}&csrf={$csrf_token}' class='btn-action btn-delete' title='Delete Book' onclick=\"return confirm('Are you sure you want to delete this book?')\">
                        <i class='fa-solid fa-trash-can'></i>
                    </a>
                </td>
              </tr>";
    }
    echo "</tbody></table>";
    echo '</div>';
} else {
    echo '<div class="empty-state">';
    echo '<i class="fa-solid fa-magnifying-glass"></i>';
    echo '<p class="empty-text">No books found matching your search criteria.</p>';
    echo '<p class="empty-subtext">Try adjusting your filters or search terms.</p>';
    echo '</div>';
}
?>
</div>
</div>

<?php include '../includes/footer.php'; ?>