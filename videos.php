<?php
session_start();
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
            margin: 0;
            font-family: 'Roboto', sans-serif;
            background-color: #e0e0e0;
            color: #333;
        }

        header {
            background-color: var(--background);
            color: #fff;
            padding: 10px 40px;
            margin: 20px 20px 0 20px;
            border-radius: 20px;
            border: 0.5px solid var(--accent);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .header-title a {
            text-decoration: none;
            color: var(--primary);
            font-size: 22px;
            font-weight: 600;
        }

        nav ul {
            display: flex;
            gap: 48px;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        nav ul li a {
            text-decoration: none;
            color: white;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        nav ul li a:hover, nav ul li a.active {
            color: var(--accent);
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
        }

        .video-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .video-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            overflow: hidden;
            aspect-ratio: 16 / 9;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .video-card iframe {
            width: 100%;
            height: 100%;
            border: none;
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

    <header>
        <div class="header-title">
            <a href="#">HearTogether</a>
        </div>

        <nav>
            <ul>
                <li><a href="#">Home</a></li>
                <li><a class="active" href="#">Videos</a></li>
                <li><a href="#">FAQ</a></li>
                <li><a href="#">Contact</a></li>
            </ul>
        </nav>

        <div class="greeting">
            <?php if (isset($_SESSION['username'])) : ?>
                <img src="<?php echo isset($_SESSION['profile_img']) ? $_SESSION['profile_img'] : 'icons/user.png'; ?>" alt="Profile Picture">
                <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a href="logout.php" class="logout-icon" title="Logout">⎋</a>
            <?php else : ?>
                <img src="icons/user.png" alt="Guest">
                <span>Hello, Guest</span>
            <?php endif; ?>
        </div>
    </header>

    <main class="video-section">
        <div>
            <h2 class="section-title">How To Sign Basic Conversations</h2>
            <div class="video-grid">
                <div class="video-card"><iframe src="https://www.youtube.com/embed/abc1" allowfullscreen></iframe></div>
                <div class="video-card"><iframe src="https://www.youtube.com/embed/abc2" allowfullscreen></iframe></div>
                <div class="video-card"><iframe src="https://www.youtube.com/embed/abc3" allowfullscreen></iframe></div>
            </div>
        </div>

        <hr>

        <div>
            <h2 class="section-title">Common Phrases in Sign Language</h2>
            <div class="video-grid">
                <div class="video-card"><iframe src="https://www.youtube.com/embed/def1" allowfullscreen></iframe></div>
                <div class="video-card"><iframe src="https://www.youtube.com/embed/def2" allowfullscreen></iframe></div>
                <div class="video-card"><iframe src="https://www.youtube.com/embed/def3" allowfullscreen></iframe></div>
            </div>
        </div>
    </main>

</body>
</html>
