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

        :root {
            --text: #ecf2f4;
            --background: #0a161a;
            --primary: #87c9e3;
            --secondary: #127094;
            --accent: #29bff9;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--background);
            margin: 0;
            padding: 0;
            height: 100vh;
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
            padding: 40px;
        }

        .section-title {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 20px;
            color: var(--text);
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
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            scroll-snap-align: start;
        }

        .video-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f9f9f9;
            width: 100%;
            height: 200px; /* or whatever height you find comfortable */
            overflow: hidden;
        }

        .video-wrapper img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            display: block;
        }

        .video-title {
            font-size: 14px;
            padding: 10px;
            color: var(--background);
            text-align: center;
            background-color: #f5f5f5;
            font-weight: 500;
        }

        hr {
            border: none;
            height: 1px;
            background-color: #ccc;
            margin: 40px 0;
        }
    </style>
</head>
<body>
<?php include 'nav.php'; ?>

<main class="video-section">
    <?php
    $categoryResult = $conn->query("SELECT DISTINCT category FROM videos ORDER BY category ASC");

    while ($cat = $categoryResult->fetch_assoc()):
        $categoryName = $cat['category'];
        $stmt = $conn->prepare("SELECT * FROM videos WHERE category = ? ORDER BY video_id DESC");
        $stmt->bind_param("s", $categoryName);
        $stmt->execute();
        $videoResult = $stmt->get_result();
    ?>
        <div>
            <h2 class="section-title"><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $categoryName))); ?></h2>
            <div class="video-grid">
                <?php if ($videoResult->num_rows > 0): ?>
                    <?php while ($video = $videoResult->fetch_assoc()): ?>
                        <div class="video-card">
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
</body>
</html>
