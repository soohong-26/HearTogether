<?php

require 'database.php';

if (!isset($_SESSION['username'])) {
    header("Location: homepage.php?error=unauthorised");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>HearTogether - Videos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;600&display=swap');

        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--background-colour);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            color: var(--text);
            overflow-y: scroll;
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        body::-webkit-scrollbar {
            width: 0px;
            background: transparent;
        }

        .greeting {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .greeting img {
            width: 30px;
            height: 30px;
            border-radius: 50%;
        }

        .greeting span {
            font-size: 16px;
            color: var(--text);
        }

        .logout-icon {
            margin-left: 10px;
            text-decoration: none;
            font-size: 16px;
            color: var(--accent);
        }

        .video-section {
            padding: 40px 6vw 40px 6vw;
            max-width: 1440px;
            margin: 0 auto;
        }

        .section-title {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 20px;
            color: var(--primary-colour);
            letter-spacing: 0.5px;
        }

        .video-grid {
            display: flex;
            overflow-x: auto;
            gap: 20px;
            padding-bottom: 10px;
            scroll-snap-type: x mandatory;
        }

        .video-card {
            min-width: 320px;
            flex: 0 0 auto;
            background-color: var(--container-background);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            scroll-snap-align: start;
            border: 1px solid var(--border-colour);
        }

        .video-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f5f7fa;
            width: 100%;
            height: 200px;
            overflow: hidden;
        }

        .video-wrapper img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            display: block;
        }

        .video-title {
            font-size: 15px;
            padding: 14px 8px;
            color: var(--heading-colour);
            text-align: center;
            background-color: #f5f7fa;
            font-weight: 500;
            border-top: 1px solid var(--border-colour);
            letter-spacing: 0.15px;
        }

        hr {
            border: none;
            height: 1px;
            background-color: var(--border-colour);
            margin: 44px 0;
        }

        .video-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .video-header h2 {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
            color: var(--primary-colour);
        }

        .video-header input[type="text"] {
            padding: 8px 12px;
            border-radius: 20px;
            border: 1px solid var(--border-colour);
            width: 200px;
            font-size: 14px;
            background-color: var(--container-background);
            color: var(--text);
            outline: none;
        }

        @media (max-width: 700px) {
            .video-section {
                padding: 24px 4vw 24px 4vw;
            }
            .video-card {
                min-width: 200px;
            }
            .video-title {
                font-size: 13px;
                padding: 10px 4px;
            }
        }
    </style>
</head>
<body>
<?php include 'nav.php'; ?>

<main class="video-section">

    <!-- Video Search -->
    <div class="video-header">
        <h2>Videos</h2>
        <input type="text" id="videoSearch" placeholder="Search...">
    </div>


    <?php
    $categoryResult = $conn->query("SELECT DISTINCT category FROM videos ORDER BY category ASC");

    while ($cat = $categoryResult->fetch_assoc()):
        $categoryName = $cat['category'];
        $stmt = $conn->prepare("SELECT * FROM videos WHERE category = ? ORDER BY video_id DESC");
        $stmt->bind_param("s", $categoryName);
        $stmt->execute();
        $videoResult = $stmt->get_result();
    ?>
        <div class="category-section">
            <h2 class="section-title"><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $categoryName))); ?></h2>
            <div class="video-grid">
                <?php if ($videoResult->num_rows > 0): ?>
                    <?php while ($video = $videoResult->fetch_assoc()): ?>
                        <div class="video-card"
                            data-title="<?php echo htmlspecialchars(strtolower($video['title'])); ?>"
                            data-category="<?php echo htmlspecialchars(strtolower($video['category'])); ?>">
                            <div class="video-wrapper">
                                <img src="videos/<?php echo htmlspecialchars($video['filename']); ?>" alt="<?php echo htmlspecialchars($video['title']); ?>">
                            </div>
                            <div class="video-title"><?php echo htmlspecialchars($video['title']); ?></div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p style="color: var(--text);">No videos found for this category.</p>
                <?php endif; ?>
            </div>
        </div>
        <hr>
    <?php endwhile; ?>
</main>

<script>
const videoSearch = document.getElementById('videoSearch');
const videoCards = document.querySelectorAll('.video-card');
const categorySections = document.querySelectorAll('.category-section');

videoSearch.addEventListener('input', function () {
    const searchTerm = this.value.toLowerCase();
    // Track if any cards are visible in a category
    categorySections.forEach(section => {
        let visibleCount = 0;
        // Only search cards in this category
        const cards = section.querySelectorAll('.video-card');
        cards.forEach(card => {
            const title = card.getAttribute('data-title');
            const category = card.getAttribute('data-category');
            const match = title.includes(searchTerm) || category.includes(searchTerm);
            card.style.display = match ? "" : "none";
            if (match) visibleCount++;
        });
        // Hide entire category section if no cards match
        section.style.display = visibleCount === 0 ? "none" : "";
    });
});
</script>
</body>
</html>
