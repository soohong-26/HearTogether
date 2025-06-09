<?php
require 'database.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: resources.php');
    exit();
}

$id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT a.title, a.content, a.date_posted, ac.category_name 
                        FROM articles a
                        LEFT JOIN article_categories ac ON a.category_id = ac.category_id
                        WHERE a.article_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Article not found.";
    exit();
}

$article = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title><?= htmlspecialchars($article['title']) ?> - HearTogether</title>
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

        h2.article-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary-colour);
            margin-bottom: 15px;
        }

        p.article-meta {
            color: var(--third, #666666);
            font-size: 14px;
            margin-bottom: 30px;
        }

        article.content {
            font-size: 18px;
            line-height: 1.7;
            white-space: pre-wrap;
        }

        a.back-link {
            display: inline-block;
            margin-top: 30px;
            color: var(--primary-colour);
            font-weight: 600;
            text-decoration: none;
            font-size: 16px;
        }
        a.back-link:hover {
            text-decoration: underline;
        }

        @media (max-width: 700px) {
            body {
                padding: 24px 4vw;
            }
            h2.article-title {
                font-size: 22px;
            }
            article.content {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
<?php include 'nav.php'; ?>

<main>
    <h2 class="article-title"><?= htmlspecialchars($article['title']) ?></h2>
    <p class="article-meta">
        Category: <?= htmlspecialchars($article['category_name'] ?? 'Uncategorized') ?> | Posted on <?= date('d M Y', strtotime($article['date_posted'])) ?>
    </p>
    <article class="content">
        <?= nl2br(htmlspecialchars($article['content'])) ?>
    </article>
    <a href="resources.php" class="back-link">&larr; Back to Resources</a>
</main>
</body>
</html>
