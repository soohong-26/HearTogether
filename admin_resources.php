<?php
require 'database.php';

if (!isset($_SESSION['username']) || $_SESSION['roles'] !== 'admin') {
    header("Location: homepage.php?error=unauthorised");
    exit();
}

// Handle Add/Edit Article
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $article_id = $_POST['article_id'] ?? null;
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $category = trim($_POST['new_category_input']) ?: trim($_POST['new_category']);

    if ($category && $title && $content) {
        // Insert new category if needed
        $stmt = $conn->prepare("INSERT IGNORE INTO article_categories (category_name) VALUES (?)");
        $stmt->bind_param("s", $category);
        $stmt->execute();
        $stmt->close();

        // Get category id
        $stmt = $conn->prepare("SELECT category_id FROM article_categories WHERE category_name = ?");
        $stmt->bind_param("s", $category);
        $stmt->execute();
        $stmt->bind_result($category_id);
        $stmt->fetch();
        $stmt->close();

        if ($article_id) {
            // Update existing article
            $stmt = $conn->prepare("UPDATE articles SET title = ?, content = ?, category_id = ? WHERE article_id = ?");
            $stmt->bind_param("ssii", $title, $content, $category_id, $article_id);
            $stmt->execute();
            $stmt->close();
        } else {
            // Insert new article
            $stmt = $conn->prepare("INSERT INTO articles (title, content, category_id) VALUES (?, ?, ?)");
            $stmt->bind_param("ssi", $title, $content, $category_id);
            $stmt->execute();
            $stmt->close();
        }
    }
    header("Location: admin_resources.php?saved=1");
    exit();
}

// Handle Delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM articles WHERE article_id = ?");
    $stmt->bind_param("i", $delId);
    $stmt->execute();
    $stmt->close();
    header("Location: admin_resources.php?deleted=1");
    exit();
}

// Fetch categories for dropdown
$catResult = $conn->query("SELECT * FROM article_categories ORDER BY category_name ASC");
$categories = [];
while ($row = $catResult->fetch_assoc()) {
    $categories[$row['category_id']] = $row['category_name'];
}

// Fetch article to edit if applicable
$editArticle = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT a.article_id, a.title, a.content, ac.category_name, ac.category_id
                            FROM articles a 
                            LEFT JOIN article_categories ac ON a.category_id = ac.category_id
                            WHERE a.article_id = ?");
    $stmt->bind_param("i", $editId);
    $stmt->execute();
    $result = $stmt->get_result();
    $editArticle = $result->fetch_assoc();
    $stmt->close();
}

// Fetch all articles for listing
$articleResult = $conn->query("SELECT a.article_id, a.title, ac.category_name, a.date_posted 
                               FROM articles a 
                               LEFT JOIN article_categories ac ON a.category_id = ac.category_id 
                               ORDER BY a.date_posted DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Admin - Manage Resources</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<style>
    @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;600&display=swap');
    body {
        font-family: 'Roboto', sans-serif;
        background-color: var(--background-colour);
        margin: 0;
        padding: 0;
        overflow-y: scroll;
        -ms-overflow-style: none;
        scrollbar-width: none;
        color: var(--text);
    }
    body::-webkit-scrollbar {
        width: 0;
        background: transparent;
    }

    h2.page-title {
        color: var(--primary-colour);
        font-weight: 700;
        margin: 30px;
        font-size: 26px;
    }

    form.resource-form {
        background-color: var(--container-background);
        padding: 25px 30px;
        margin: 0 30px 40px 30px;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
    }

    form.resource-form label {
        display: block;
        margin-top: 15px;
        font-weight: 600;
        color: var(--heading-colour);
    }

    form.resource-form select,
    form.resource-form input[type="text"],
    form.resource-form textarea {
        width: 100%;
        padding: 12px 15px;
        margin-top: 8px;
        border-radius: var(--border-radius);
        border: 1px solid var(--border-colour);
        font-size: 16px;
        background-color: var(--container-background);
        color: var(--text);
        outline: none;
        resize: vertical;
        box-sizing: border-box;
    }

    form.resource-form textarea {
        min-height: 140px;
    }

    form.resource-form button {
        margin-top: 24px;
        padding: 14px 30px;
        background-color: var(--button-background);
        color: var(--button-text);
        border: none;
        font-weight: 700;
        font-size: 18px;
        cursor: pointer;
        border-radius: var(--border-radius);
        transition: background-color 0.3s ease;
    }

    form.resource-form button:hover {
        background-color: var(--button-hover);
    }

    a.cancel-link {
        margin-left: 20px;
        font-weight: 600;
        color: var(--primary-colour);
        text-decoration: underline;
        cursor: pointer;
    }
    a.cancel-link:hover {
        color: var(--primary-hover);
    }

    table.resource-table {
        width: 100%;
        border-collapse: collapse;
        margin: 18px 0 30px 0;
        font-size: 1rem;
        border: 1px solid var(--border-colour);
        border-radius: var(--border-radius);
        overflow: hidden;
    }

    table.resource-table th, table.resource-table td {
        border: 1px solid var(--border-colour);
        padding: 8px 12px;
        text-align: left;
        vertical-align: middle;
    }

    table.resource-table th {
        background-color: var(--primary-colour);
        color: var(--button-text);
        font-weight: 600;
    }

    table.resource-table tr{
        background-color: #f5f7fa;
    }

    table.resource-table td.actions {
        width: 110px;
        white-space: nowrap;
    }

    .resource-table tr {
        margin: 20px 30px;
    }

    /* Empty Container */
    .empty-container {
        margin: 0 30px;
    }

    .action-buttons {
        display: flex;
        flex-direction: column;
        gap: 8px;
        align-items: flex-start;
    }
    .action-buttons a {
        display: inline-block;
        width: 70px;
        background: var(--button-background);
        color: var(--button-text);
        font-weight: 600;
        text-decoration: none;
        text-align: center;
        padding: 6px 0;
        border-radius: 6px;
        transition: background 0.2s;
        cursor: pointer;
    }

    .action-buttons .delete-btn {
        background: var(--toast-error-bg);
        color: var(--button-text);
    }

    .action-buttons .delete-btn:hover {
        background: #dc3545;
    }

    .action-buttons a:hover {
        background: var(--button-hover);
    }

    .success-message {
        max-width: 100%;
        background-color: #d4edda;
        color: #155724;
        padding: 14px 20px;
        border-radius: var(--border-radius);
        font-weight: 700;
        margin: 30px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    /* Toast */
    #toast {
        position: fixed;
        top: 20px;
        right: 20px;
        max-width: 320px;
        padding: 14px 20px;
        border-radius: var(--border-radius);
        font-weight: 600;
        color: white;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.5s ease;
        z-index: 9999;
    }
    #toast.success {
        background-color: var(--success);
    }
    #toast.error {
        background-color: var(--danger);
    }

</style>
</head>
<body>
<?php include 'nav.php'; ?>

<main>
    <h2 class="page-title">Manage Educational Resources</h2>

    <?php if (isset($_GET['deleted'])): ?>
        <p class="success-message">Article deleted successfully.</p>
    <?php elseif (isset($_GET['saved'])): ?>
        <p class="success-message">Article saved successfully.</p>
    <?php endif; ?>

    <form method="post" class="resource-form" autocomplete="off">
        <input type="hidden" name="article_id" value="<?= $editArticle['article_id'] ?? '' ?>" />

        <label for="new_category">Select Existing Category</label>
        <select id="new_category" name="new_category" onchange="document.getElementById('new_category_input').disabled = !!this.value;">
            <option value="">-- Select Existing Category --</option>
            <?php foreach ($categories as $catId => $catName): ?>
                <option value="<?= htmlspecialchars($catName) ?>" <?= (isset($editArticle['category_name']) && $editArticle['category_name'] === $catName) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($catName) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="new_category_input">Or Type New Category</label>
        <input type="text" id="new_category_input" name="new_category_input" placeholder="Or type new category name here"
            oninput="document.getElementById('new_category').disabled = !!this.value; if(!this.value) document.getElementById('new_category').disabled=false;"
            value="<?= htmlspecialchars($editArticle['category_name'] ?? '') ?>">

        <label for="title">Article Title</label>
        <input type="text" id="title" name="title" required placeholder="Enter article title"
               value="<?= htmlspecialchars($editArticle['title'] ?? '') ?>">

        <label for="content">Content</label>
        <textarea id="content" name="content" required rows="6" placeholder="Enter article content"><?= htmlspecialchars($editArticle['content'] ?? '') ?></textarea>

        <button type="submit"><?= $editArticle ? 'Update' : 'Add' ?> Article</button>
        <?php if ($editArticle): ?>
            <a href="admin_resources.php" class="cancel-link">Cancel</a>
        <?php endif; ?>
    </form>

    <div class="empty-container">
        <table class="resource-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Date Posted</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $articleResult->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['title']) ?></td>
                        <td><?= htmlspecialchars($row['category_name'] ?? 'Uncategorized') ?></td>
                        <td><?= date('d M Y', strtotime($row['date_posted'])) ?></td>
                        <td class="actions">
                            <div class="action-buttons">
                                <a href="admin_resources.php?edit=<?= $row['article_id'] ?>">Edit</a>
                                <a href="admin_resources.php?delete=<?= $row['article_id'] ?>" class="delete-btn" onclick="return confirm('Delete this article?')">Delete</a>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</main>

<div id="toast"></div>

<script>
    // Toast helper
    const toast = document.getElementById('toast');
    let toastTimeout;
    function showToast(message, type = 'success') {
        toast.textContent = message;
        toast.className = type;
        toast.style.opacity = '1';
        toast.style.pointerEvents = 'auto';
        clearTimeout(toastTimeout);
        toastTimeout = setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.pointerEvents = 'none';
        }, 3500);
    }

    // Show notification based on URL query parameters
    const params = new URLSearchParams(window.location.search);
    if (params.has('saved')) {
        showToast('Article saved successfully!', 'success');
        params.delete('saved');
        history.replaceState(null, '', window.location.pathname);
    }
    if (params.has('deleted')) {
        showToast('Article deleted successfully!', 'success');
        params.delete('deleted');
        history.replaceState(null, '', window.location.pathname);
    }
    if (params.has('error') && params.get('error') === 'unauthorised') {
        showToast('Unauthorized access. Please log in.', 'error');
        params.delete('error');
        history.replaceState(null, '', window.location.pathname);
    }
</script>
</body>
</html>
