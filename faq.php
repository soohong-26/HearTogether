<?php
require 'database.php';

// Get all categories in order
$cat_res = $conn->query("SELECT name FROM faq_categories ORDER BY category_order ASC, name ASC");
$categories = [];
while ($cat = $cat_res->fetch_assoc()) {
    $categories[] = $cat['name'];
}

// Get all FAQs grouped by category
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
    <title>HearTogether - FAQ</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Link the shared FAQ stylesheet -->
    <link rel="stylesheet" href="css/faq.css?v=<?= time(); ?>">
</head>
<body>

<?php include 'nav.php'; ?>

<main>
    <div class="faq-header">
        <h2>Frequently Asked Questions</h2>
        <!-- Search bar -->
        <input type="text" id="faqSearch" placeholder="Search...">
    </div>

    <!-- Display all FAQs -->
    <?php foreach ($categories as $category): ?>
        <?php if (!empty($faqs[$category])): ?>
        <!-- Each category -->
        <div class="category">
            <!-- Category name -->
            <h3><?= htmlspecialchars($category) ?></h3>
            <!-- Each individual FAQ -->
            <?php foreach ($faqs[$category] as $faq): ?>
                <!-- Button for dropdown -->
                <button class="accordion"><?= htmlspecialchars($faq['question']) ?></button>
                <div class="panel"><p><?= nl2br(htmlspecialchars($faq['answer'])) ?></p></div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    <?php endforeach; ?>
</main>

<script>
const acc = document.querySelectorAll(".accordion");

// Accordion toggle logic
acc.forEach(button => {
    button.addEventListener("click", function () {
        acc.forEach(btn => {
            if (btn !== this) {
                btn.classList.remove("active");
                btn.nextElementSibling.style.display = "none";
            }
        });
        this.classList.toggle("active");
        const panel = this.nextElementSibling;
        panel.style.display = panel.style.display === "block" ? "none" : "block";
    });
});

// Search/filter logic
const searchInput = document.getElementById('faqSearch');
searchInput.addEventListener('input', function () {
    const searchTerm = this.value.toLowerCase();
    const categories = document.querySelectorAll('.category');

    categories.forEach(category => {
        let hasMatch = false;
        const buttons = category.querySelectorAll('.accordion');

        buttons.forEach(button => {
            const question = button.textContent.toLowerCase();
            const match = question.includes(searchTerm);

            button.style.display = match ? "flex" : "none";
            button.classList.remove("active");
            button.nextElementSibling.style.display = "none";

            if (match) hasMatch = true;
        });

        // Show/hide the whole category block
        category.style.display = hasMatch ? "block" : "none";
    });
});
</script>

</body>
</html>
