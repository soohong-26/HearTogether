<?php
require 'database.php';

// --- Handle Add FAQ ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_question'], $_POST['new_answer'])) {
    $category = trim($_POST['new_category_input']) ?: trim($_POST['new_category']);
    $question = trim($_POST['new_question']);
    $answer = trim($_POST['new_answer']);
    if ($category && $question && $answer) {
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
        echo json_encode(['success'=>true]);
    } else {
        echo json_encode(['success'=>false]);
    }
    exit();
}

// --- Handle Delete ---
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM faq WHERE id=$id");
    header("Location: admin_faq.php?delete=success");
    exit();
}

// --- Fetch All FAQs Grouped by Category ---
$faqs = [];
$res = $conn->query("SELECT * FROM faq ORDER BY category ASC, id ASC");
while ($row = $res->fetch_assoc()) {
    $faqs[$row['category']][] = $row;
}
$categories = array_keys($faqs);
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
            <label for="new_category">Category</label>
            <select id="new_category" name="new_category">
                <option value="">-- Select Existing Category --</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
                <?php endforeach; ?>
            </select>
            <input type="text" name="new_category_input" placeholder="Or type new category name here">
            <label for="new_question">Question</label>
            <input type="text" id="new_question" name="new_question" required placeholder="Enter new FAQ question">
            <label for="new_answer">Answer</label>
            <textarea id="new_answer" name="new_answer" required rows="3" placeholder="Enter answer here"></textarea>
            <button type="submit">Add FAQ</button>
        </form>
    </div>

    <?php foreach ($faqs as $category => $items): ?>
    <div class="category-box">
        <h3><?= htmlspecialchars($category) ?></h3>
        <?php foreach ($items as $faq): ?>
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
<?php endforeach; ?>

    <div id="toast" class="toast"></div>
</main>

<script>
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
    toast.style.background = error ? '#ff5e57' : 'var(--accent)';
    toast.style.display = 'block';
    setTimeout(()=>{toast.style.display='none';}, 2300);
}
</script>
</body>
</html>
