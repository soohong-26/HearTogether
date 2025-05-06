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
            font-family: 'Roboto', sans-serif;
            background-color: var(--background);
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
            aspect-ratio: 16 / 9;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .video-wrapper iframe {
            width: 100%;
            height: 100%;
            border: none;
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
    <!-- Navigation Bar -->
    <?php include 'nav.php'; ?>

    <main class="video-section">
        <div>
            <h2 class="section-title">How To Sign Basic Conversations</h2>
            <div class="video-grid">

                <div class="video-card">
                    <div class="video-wrapper">
                        <iframe src="https://www.youtube.com/embed/abc1" allowfullscreen></iframe>
                    </div>
                    <div class="video-title">Greeting with Sign Language</div>
                </div>

                <div class="video-card">
                    <div class="video-wrapper">
                        <iframe src="https://www.youtube.com/embed/abc1" allowfullscreen></iframe>
                    </div>
                    <div class="video-title">Greeting with Sign Language</div>
                </div>

                <div class="video-card">
                    <div class="video-wrapper">
                        <iframe src="https://www.youtube.com/embed/abc1" allowfullscreen></iframe>
                    </div>
                    <div class="video-title">Greeting with Sign Language</div>
                </div>
            
        </div>

        <hr>

        <div>
            <h2 class="section-title">Common Phrases in Sign Language</h2>
            <div class="video-grid">
            <div class="video-card">
                    <div class="video-wrapper">
                        <iframe src="https://www.youtube.com/embed/abc1" allowfullscreen></iframe>
                    </div>
                    <div class="video-title">Greeting with Sign Language</div>
                </div>

                <div class="video-card">
                    <div class="video-wrapper">
                        <iframe src="https://www.youtube.com/embed/abc1" allowfullscreen></iframe>
                    </div>
                    <div class="video-title">Greeting with Sign Language</div>
                </div>

                <div class="video-card">
                    <div class="video-wrapper">
                        <iframe src="https://www.youtube.com/embed/abc1" allowfullscreen></iframe>
                    </div>
                    <div class="video-title">Greeting with Sign Language</div>
                </div>
            </div>
        </div>
    </main>

</body>
</html>
