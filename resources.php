<?php
require 'database.php';

if (!isset($_SESSION['username'])) {
    header("Location: homepage.php?error=unauthorised");
    exit();
}

// Fetch all categories and their articles
$categories = [];
$sql = "SELECT ac.category_id, ac.category_name, a.article_id, a.title, a.content, a.date_posted 
        FROM article_categories ac
        LEFT JOIN articles a ON ac.category_id = a.category_id
        ORDER BY ac.category_name ASC, a.date_posted DESC";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    $catId = $row['category_id'];
    if (!isset($categories[$catId])) {
        $categories[$catId] = [
            'name' => $row['category_name'],
            'articles' => []
        ];
    }
    if ($row['article_id'] !== null) {
        $categories[$catId]['articles'][] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>HearTogether - Resources</title>
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

        h2.section-title {
            font-size: 22px;
            font-weight: 700;
            color: var(--primary-colour);
            margin-bottom: 25px;
            letter-spacing: 0.5px;
        }

        .resource-search {
            display: flex;
            justify-content: flex-end;
            margin: 30px 30px 10px 40px;
        }

        .resource-search input[type="text"] {
            padding: 8px 14px;
            border-radius: 20px;
            border: 1px solid var(--border-colour);
            width: 200px;
            font-size: 14px;
            background-color: var(--container-background);
            color: var(--text);
            outline: none;
        }

        section.category-section {
            margin-bottom: 60px;
        }

        section.category-section h3 {
            font-size: 20px;
            font-weight: 600;
            color: var(--heading-colour);
            margin: 20px 30px;
        }

        ul.article-list {
            list-style-type: none;
            padding-left: 0;
            margin: 0;
        }

        ul.article-list li {
            background-color: var(--container-background);
            margin-bottom: 12px;
            margin: 12px 30px;
            padding: 15px 20px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            transition: background-color 0.3s ease;
        }

        ul.article-list li:hover {
            background-color: #f0f5ff;
        }

        ul.article-list li a {
            color: var(--primary-colour);
            font-weight: 600;
            font-size: 16px;
            text-decoration: none;
        }

        ul.article-list li a:hover {
            text-decoration: underline;
        }

        ul.article-list li small {
            display: block;
            margin-top: 6px;
            color: var(--third, #666666);
            font-size: 13px;
        }

        p.no-articles {
            color: var(--text);
            font-style: italic;
        }

        @media (max-width: 700px) {
            body {
                padding: 24px 4vw;
            }
            .resource-search input[type="text"] {
                width: 100%;
            }
        }
    </style>
</head>
<body>
<?php include 'nav.php'; ?>

<main>
    <div class="resource-search">
        <input type="text" id="resourceSearch" placeholder="Search articles...">
    </div>

    <?php foreach ($categories as $category): ?>
        <section class="category-section" data-category="<?= htmlspecialchars(strtolower($category['name'])) ?>">
            <h3><?= htmlspecialchars($category['name']) ?></h3>
            <?php if (empty($category['articles'])): ?>
                <p class="no-articles">No articles available in this category.</p>
            <?php else: ?>
                <ul class="article-list">
                    <?php foreach ($category['articles'] as $article): ?>
                        <li data-title="<?= htmlspecialchars(strtolower($article['title'])) ?>">
                            <a href="resource_detail.php?id=<?= $article['article_id'] ?>">
                                <?= htmlspecialchars($article['title']) ?>
                            </a>
                            <small>Posted on <?= date('d M Y', strtotime($article['date_posted'])) ?></small>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>
    <?php endforeach; ?>
</main>

<script>
    const resourceSearch = document.getElementById('resourceSearch');
    const categorySections = document.querySelectorAll('section.category-section');

    resourceSearch.addEventListener('input', () => {
        const searchTerm = resourceSearch.value.toLowerCase();

        categorySections.forEach(section => {
            const articles = section.querySelectorAll('ul.article-list li');
            let visibleCount = 0;

            articles.forEach(article => {
                const title = article.getAttribute('data-title');
                const match = title.includes(searchTerm);
                article.style.display = match ? '' : 'none';
                if (match) visibleCount++;
            });

            section.style.display = visibleCount === 0 ? 'none' : '';
        });
    });
</script>
</body>
</html>
