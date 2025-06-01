<?php
require 'database.php';

// Ensure all existing categories are in the categories table
$conn->query("INSERT IGNORE INTO faq_categories (name) SELECT DISTINCT category FROM faq");

// --- Handle Save Category Order ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category_order'])) {
    $order = explode(',', $_POST['category_order']);
    foreach ($order as $index => $name) {
        $stmt = $conn->prepare("UPDATE faq_categories SET category_order=? WHERE name=?");
        $stmt->bind_param("is", $index, $name);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: admin_faq.php?reorder=success");
    exit();
}

// --- Handle Add FAQ ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_question'], $_POST['new_answer'])) {
    $category = trim($_POST['new_category_input']) ?: trim($_POST['new_category']);
    $question = trim($_POST['new_question']);
    $answer = trim($_POST['new_answer']);
    if ($category && $question && $answer) {
        // Ensure new category is in the categories table
        $stmt = $conn->prepare("INSERT IGNORE INTO faq_categories (name) VALUES (?)");
        $stmt->bind_param("s", $category);
        $stmt->execute();
        $stmt->close();

        $stmt = $conn->prepare("INSERT INTO faq (category, question, answer) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $category, $question, $answer);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: admin_faq.php?add=success");
    exit();
}

// --- Handle Edit (AJAX) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'], $_POST['field'], $_POST['value'])) {
    $id = (int)$_POST['edit_id'];
    $field = in_array($_POST['field'], ['question', 'answer', 'category']) ? $_POST['field'] : null;
    $value = trim($_POST['value']);
    if ($field && $id && $value !== "") {
        $stmt = $conn->prepare("UPDATE faq SET $field=? WHERE id=?");
        $stmt->bind_param("si", $value, $id);
        $stmt->execute();
        $stmt->close();

        // If editing the category, make sure it's also in the categories table
        if ($field == "category") {
            $stmt = $conn->prepare("INSERT IGNORE INTO faq_categories (name) VALUES (?)");
            $stmt->bind_param("s", $value);
            $stmt->execute();
            $stmt->close();
        }
        echo json_encode(['success'=>true]);
    } else {
        echo json_encode(['success'=>false]);
    }
    exit();
}

// --- Handle Delete ---
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM faq WHERE id=$id");
    header("Location: admin_faq.php?delete_success=1");
    exit();
}

// --- Fetch Ordered Categories ---
$cat_res = $conn->query("SELECT * FROM faq_categories ORDER BY category_order ASC, name ASC");
$categories = [];
while ($cat = $cat_res->fetch_assoc()) {
    $categories[] = $cat['name'];
}

// --- Fetch All FAQs Grouped by Category ---
$faqs = [];
$res = $conn->query("SELECT * FROM faq ORDER BY category ASC, id ASC");
while ($row = $res->fetch_assoc()) {
    $faqs[$row['category']][] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin FAQ Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/faq.css?v=<?= time(); ?>">
</head>
<body class="admin">
<?php include 'nav.php'; ?>

<main>
    <div class="admin-header">
        <h2>Admin FAQ Management</h2>
        <a href="faq.php" style="color:var(--accent);font-weight:bold;text-decoration:underline;">View Public FAQ</a>
    </div>

    <div class="faq-form">
        <form method="post" autocomplete="off">
            <label for="new_category">Select Existing Category</label>
            <select id="new_category" name="new_category" onchange="document.getElementById('new_category_input').disabled = !!this.value;">
                <option value="">-- Select Existing Category --</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
                <?php endforeach; ?>
            </select>
            <label for="new_category_input">Or Type New Category</label>
            <input type="text" id="new_category_input" name="new_category_input" placeholder="Or type new category name here"
                oninput="document.getElementById('new_category').disabled = !!this.value; if(!this.value) document.getElementById('new_category').disabled=false;">
            <label for="new_question">Question</label>
            <input type="text" id="new_question" name="new_question" required placeholder="Enter new FAQ question">
            <label for="new_answer">Answer</label>
            <textarea id="new_answer" name="new_answer" required rows="3" placeholder="Enter answer here"></textarea>
            <button type="submit">Add FAQ</button>
        </form>
    </div>

    <!-- Category Group Order (Moves all FAQs in that category as a block) -->
    <div class="category-order-list">
        <h3>Organise Category Groups</h3>
        <form method="post" id="reorderForm" style="margin-bottom:30px;">
            <ul id="categoryList" style="padding-left:0;">
                <?php foreach ($categories as $i => $cat): ?>
                    <li data-name="<?= htmlspecialchars($cat) ?>" style="list-style:none;display:flex;align-items:center;gap:8px;margin-bottom:8px;">
                        <span style="flex:1;"><?= htmlspecialchars($cat) ?></span>
                        <button type="button" class="move-up" <?= $i==0?'disabled':'' ?>>&#8593;</button>
                        <button type="button" class="move-down" <?= $i==count($categories)-1?'disabled':'' ?>>&#8595;</button>
                    </li>
                <?php endforeach; ?>
            </ul>
            <input type="hidden" name="category_order" id="categoryOrderInput">
            <button type="submit" style="margin-top:8px;" class="save-order-btn">Save Order</button>
        </form>
    </div>

    <?php foreach ($categories as $category): ?>
        <?php if (!empty($faqs[$category])): ?>
            <div class="category-box" data-category="<?= htmlspecialchars($category) ?>">
                <h3><?= htmlspecialchars($category) ?></h3>
                <?php foreach ($faqs[$category] as $faq): ?>
                    <div class="faq-item" data-id="<?= $faq['id'] ?>">
                        <div class="faq-field">
                            <input type="text" class="faq-q" value="<?= htmlspecialchars($faq['question']) ?>" data-field="question" autocomplete="off">
                            <button class="confirm-btn" style="display:none;" data-field="question" title="Confirm">
                                <img src="icons/save_black.svg" alt="Confirm" class="confirm-icon">
                            </button>
                            <form method="get" style="margin:0;display:inline;">
                                <input type="hidden" name="delete" value="<?= $faq['id'] ?>">
                                <button type="submit" class="delete-btn" title="Delete FAQ" onclick="return confirm('Delete this FAQ?')">
                                    <img src="icons/delete_black.svg" alt="Delete" class="delete-icon">
                                </button>
                            </form>
                        </div>
                        <div class="faq-field">
                            <input type="text" class="faq-a" value="<?= htmlspecialchars($faq['answer']) ?>" data-field="answer" autocomplete="off">
                            <button class="confirm-btn" style="display:none;" data-field="answer" title="Confirm">
                                <img src="icons/save_black.svg" alt="Confirm" class="confirm-icon">
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>

    <div id="toast" class="toast"></div>
</main>

<script>
// Move category groups (blocks) up or down
function updateMoveButtons() {
    const lis = document.querySelectorAll('#categoryList li');
    lis.forEach((li, i) => {
        li.querySelector('.move-up').disabled = i === 0;
        li.querySelector('.move-down').disabled = i === lis.length - 1;
    });
}
updateMoveButtons();

document.querySelectorAll('.move-up, .move-down').forEach(btn => {
    btn.addEventListener('click', function() {
        const li = this.closest('li');
        if (this.classList.contains('move-up') && li.previousElementSibling) {
            li.parentNode.insertBefore(li, li.previousElementSibling);
        }
        if (this.classList.contains('move-down') && li.nextElementSibling) {
            li.parentNode.insertBefore(li.nextElementSibling, li);
        }
        updateMoveButtons();
    });
});

document.getElementById('reorderForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const order = Array.from(document.querySelectorAll('#categoryList li')).map(li => li.dataset.name);
    document.getElementById('categoryOrderInput').value = order.join(',');
    this.submit();
});

// Show confirm button on input change
document.querySelectorAll('.faq-field input[type="text"]').forEach(input => {
    input.addEventListener('input', function() {
        const field = this.closest('.faq-field');
        field.querySelector('.confirm-btn').style.display = 'inline-block';
    });
});

// Handle confirm button click (AJAX)
document.querySelectorAll('.confirm-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const input = this.closest('.faq-field').querySelector('input');
        const value = input.value.trim();
        const item = this.closest('.faq-item');
        const id = item.dataset.id;
        const fieldType = this.dataset.field;
        if (value === "") {
            showToast("Field cannot be empty!", true);
            return;
        }
        fetch('admin_faq.php', {
            method: "POST",
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `edit_id=${id}&field=${fieldType}&value=${encodeURIComponent(value)}`
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) showToast('FAQ updated!');
            else showToast('Error updating FAQ', true);
            this.style.display = 'none';
        })
        .catch(()=> {
            showToast('Error updating FAQ', true);
            this.style.display = 'none';
        });
    });
});

function showToast(msg, error) {
    const toast = document.getElementById('toast');
    toast.textContent = msg;
    toast.className = 'toast ' + (error ? 'error' : 'success');
    toast.style.display = 'block';  
    setTimeout(()=>{toast.style.display='none';}, 2300);
}

document.addEventListener('DOMContentLoaded', function() {
    const params = new URLSearchParams(window.location.search);
    if (params.get('add') === 'success') {
        showToast('FAQ added successfully!');
        params.delete('add');
        window.history.replaceState({}, document.title, window.location.pathname);
    }
    if (params.get('reorder') === 'success') {
        showToast('Category order updated!');
        params.delete('reorder');
        window.history.replaceState({}, document.title, window.location.pathname);
    }
    if (params.get('delete_success') === '1') {
        showToast('FAQ deleted successfully!');
        params.delete('delete_success');
        window.history.replaceState({}, document.title, window.location.pathname);
    }
});

// Dynamic disabling for category and input
document.getElementById('new_category').addEventListener('change', function() {
    document.getElementById('new_category_input').disabled = !!this.value;
    if(this.value) document.getElementById('new_category_input').value = '';
});
document.getElementById('new_category_input').addEventListener('input', function() {
    document.getElementById('new_category').disabled = !!this.value;
    if(this.value) document.getElementById('new_category').value = '';
    else document.getElementById('new_category').disabled = false;
});

</script>
</body>
</html>
