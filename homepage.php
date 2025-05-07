<!-- HTML -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HearTogether - Home</title>
    <!-- CSS -->
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
            background-color: var(--background);
            color: var(--text);
        }

        .hero {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 60px 80px;
            flex-wrap: wrap;
            background-color: var(--background);
        }

        .hero-text {
            flex: 1;
            max-width: 600px;
        }

        .hero-text h1 {
            font-size: 42px;
            font-weight: 700;
            margin-bottom: 20px;
            line-height: 1.3;
        }

        .hero-text p {
            font-size: 18px;
            margin-bottom: 30px;
            color: var(--primary);
        }

        .hero-text a {
            display: inline-block;
            padding: 12px 28px;
            font-size: 16px;
            font-weight: 600;
            color: var(--background);
            background-color: var(--accent);
            border-radius: 10px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .hero-text a:hover {
            background-color: #22a2d4;
        }

        .hero-img {
            flex: 1;
            text-align: right;
        }

        .hero-img img {
            max-width: 400px;
            width: 100%;
            height: auto;
        }

        @media (max-width: 900px) {
            .hero {
                flex-direction: column;
                text-align: center;
                padding: 40px 20px;
            }

            .hero-img {
                text-align: center;
                margin-top: 30px;
            }
        }

        .cta-buttons {
            margin-top: 30px;
        }

        .cta-buttons a {
            text-decoration: none;
            background-color: var(--accent);
            color: var(--background);
            padding: 12px 24px;
            margin: 0 10px;
            border-radius: 10px;
            font-weight: 600;
        }

        .features {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            margin: 60px 20px;
            gap: 40px;
        }

        .feature-box {
            background-color: var(--primary);
            padding: 20px;
            border-radius: 15px;
            width: 280px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .feature-box h3 {
            margin-bottom: 10px;
            color: var(--background);
        }

        .testimonials {
            text-align: center;
            margin: 60px 20px;
        }

        .testimonials p {
            font-style: italic;
            max-width: 600px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <?php include 'nav.php'; ?>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-text">
            <h1>Learn Sign Language,<br>Connect With Everyone</h1>
            <p>Break barriers, embrace inclusion, and start your journey into the world of sign language today.</p>
            <a href="videos.php">Start Learning</a>
        </div>
        <div class="hero-img">
            <img src="assets/images/hero-illustration.png" alt="Sign language illustration">
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="feature-box">
            <h3>Learn Sign Language</h3>
            <p>Watch easy-to-follow videos for beginners and families.</p>
        </div>
        <div class="feature-box">
            <h3>Trusted Resources</h3>
            <p>Access expert-approved content to support your parenting journey.</p>
        </div>
        <div class="feature-box">
            <h3>Community Support</h3>
            <p>Find answers to common questions and connect with others.</p>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="testimonials">
        <h2>What Parents Are Saying</h2>
        <p>"HearTogether helped me understand and communicate better with my child. It's a lifesaver!"</p>
    </section>

</body>
</html>
