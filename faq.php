<?php
require 'database.php';

// Fetch all FAQs grouped by category
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
        <input type="text" id="faqSearch" placeholder="🔍 Search...">
    </div>

    <?php foreach ($faqs as $category => $items): ?>
        <div class="category">
            <h3><?= htmlspecialchars($category) ?></h3>
            <?php foreach ($items as $faq): ?>
                <button class="accordion"><?= htmlspecialchars($faq['question']) ?></button>
                <div class="panel"><p><?= nl2br(htmlspecialchars($faq['answer'])) ?></p></div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
</main>

<script>
// One open at a time
const acc = document.querySelectorAll(".accordion");
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

// Search/filter function
const searchInput = document.getElementById('faqSearch');
searchInput.addEventListener('input', function () {
    const searchTerm = this.value.toLowerCase();
    acc.forEach(button => {
        const question = button.textContent.toLowerCase();
        const panel = button.nextElementSibling;
        const match = question.includes(searchTerm);
        button.style.display = match ? "flex" : "none";
        panel.style.display = "none";
        button.classList.remove("active");
    });
});
</script>

</body>
</html>
