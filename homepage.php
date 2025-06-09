<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$logoutMessage = '';

if (isset($_GET['logout']) && $_GET['logout'] === 'success') {
    $logoutMessage = 'You have been successfully logged out.';
}
?>

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

        body, html {
            margin: 0;
            font-family: 'Roboto', sans-serif;
            background-color: var(--background-colour);
            color: var(--text);
            overflow-y: scroll;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        body::-webkit-scrollbar {
            display: none;
        }

        .hero {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 60px 0 60px;
            flex-wrap: wrap;
            background-color: var(--container-background);
            margin: 40px 0 20px 0;
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
            color: var(--heading-colour);
        }

        .hero-text p {
            font-size: 18px;
            margin-bottom: 30px;
            color: var(--primary-colour);
        }

        .hero-text a {
            display: inline-block;
            padding: 12px 28px;
            font-size: 16px;
            font-weight: 600;
            color: var(--button-text);
            background-color: var(--button-background);
            border-radius: var(--border-radius);
            text-decoration: none;
            transition: background-color var(--transition-speed) ease;
        }

        .hero-text a:hover {
            background-color: var(--button-hover);
        }

        .hero-img {
            flex: 1;
            text-align: right;
        }

        .hero-img img {
            max-width: 300px;
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
            background-color: var(--button-background);
            color: var(--button-text);
            padding: 12px 24px;
            margin: 0 10px;
            border-radius: var(--border-radius);
            font-weight: 600;
        }

        .cta-buttons a:hover {
            background-color: var(--button-hover);
        }

        .features {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            margin: 60px 20px;
            gap: 40px;
        }

        .feature-box {
            background-color: var(--primary-colour);
            padding: 20px;
            border-radius: 15px;
            width: 280px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.09);
        }

        .feature-box h3 {
            margin-bottom: 10px;
            color: var(--button-text);
        }

        .testimonials {
            text-align: center;
            margin: 60px 20px;
        }

        .testimonials h2 {
            color: var(--heading-colour);
        }

        .testimonials p {
            font-style: italic;
            max-width: 600px;
            margin: 0 auto;
        }

        .logout-message {
            background-color: var(--primary-colour);
            color: var(--button-text);
            padding: 10px 20px;
            border-radius: var(--border-radius);
            font-weight: 600;
            max-width: 300px;
            margin: 20px auto;
            text-align: center;
            box-shadow: var(--box-shadow);
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
            <img src="images/hero-illustration.png" alt="Sign language illustration">
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="feature-box">
            <h3>Learn Sign Language</h3>
            <p>Learn basic sign language through easy-to-follow video tutorials made for you.</p>
        </div>
        <div class="feature-box">
            <h3>Simple & Access Design</h3>
            <p>Enjoy a clean, user-friendly website experience accessible on any device.</p>
        </div>
        <div class="feature-box">
            <h3>Interactive Quizzes</h3>
            <p>Test your knowledge and track your progress with fun and interactive quizzes.</p>
        </div>
    </section>
</body>

<?php if (isset($_GET['error']) && $_GET['error'] === 'unauthorised'): ?>
    <div id="unauth-toast" style="
        position: fixed;
        top: 20px;
        right: 20px;
        background-color: var(--toast-error-bg);
        color: #fff;
        padding: 12px 18px;
        border-radius: var(--border-radius);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        font-weight: 600;
        z-index: 9999;
        opacity: 0;
        transition: opacity 0.5s ease;
    ">
        Please log in to view the videos.
    </div>
    <script>
        const unauthToast = document.getElementById('unauth-toast');
        if (unauthToast) {
            setTimeout(() => {
                unauthToast.style.opacity = '1';
            }, 100);
            setTimeout(() => {
                unauthToast.style.opacity = '0';

                // Remove the query parameter without reloading the page
                const url = new URL(window.location);
                url.searchParams.delete('error');
                window.history.replaceState({}, document.title, url);
            }, 3000);
        }
    </script>
<?php endif; ?>

<?php if (!empty($logoutMessage)): ?>
    <div id="logout-toast" style="
        position: fixed;
        top: 20px;
        right: 20px;
        background-color: var(--primary-colour);
        color: var(--button-text);
        padding: 12px 18px;
        border-radius: var(--border-radius);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        font-weight: 600;
        z-index: 9999;
        opacity: 0;
        transition: opacity 0.5s ease;
    ">
        <?php echo htmlspecialchars($logoutMessage); ?>
    </div>
    <script>
        // Show the toast
        const toast = document.getElementById('logout-toast');
        if (toast) {
            setTimeout(() => {
                toast.style.opacity = '1';
            }, 100);
            setTimeout(() => {
                toast.style.opacity = '0';
            }, 3000);
        }
    </script>
<?php endif; ?>

</html>