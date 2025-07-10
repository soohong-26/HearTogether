<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require 'database.php';

$logoutMessage = '';

if (isset($_GET['logout']) && $_GET['logout'] === 'success') {
    $logoutMessage = 'You have been successfully logged out.';
}

// Calculate average rating
$avgRating = 0;
$totalRatings = 0;
$ratingRes = $conn->query("SELECT AVG(rating) AS avg_rating, COUNT(*) AS total FROM website_ratings");
if ($ratingRes && $row = $ratingRes->fetch_assoc()) {
    $avgRating = round($row['avg_rating'], 2);
    $totalRatings = $row['total'];
}

// If user is logged in, check if allowed to rate
$canRate = false;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    // Check last rating time
    $res = $conn->prepare("SELECT rated_at FROM website_ratings WHERE user_id=? ORDER BY rated_at DESC LIMIT 1");
    $res->bind_param("i", $user_id);
    $res->execute();
    $res->store_result();
    $res->bind_result($lastRatedAt);
    if ($res->fetch()) {
        $lastDate = new DateTime($lastRatedAt);
        $now = new DateTime();
        $interval = $now->diff($lastDate);
        if ($interval->m >= 1 || $interval->y >= 1) {
            $canRate = true;
        }
    } else {
        $canRate = true; // Never rated before
    }
    $res->close();
}

// Handle rating submission
$ratingError = '';
if ($canRate && $_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['website_rating'])) {
    $rating = intval($_POST['website_rating']);
    if ($rating >= 1 && $rating <= 5) {
        $stmt = $conn->prepare("INSERT INTO website_ratings (user_id, rating) VALUES (?, ?)");
        $stmt->bind_param("ii", $user_id, $rating);
        $stmt->execute();
        $stmt->close();
        header("Location: homepage.php?rate=success");
        exit();
    } else {
        $ratingError = "Invalid rating submitted.";
    }
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

        .feature-box p {
            color: var(--background-colour);
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

        /* Rating Section */
        .website-rating-section {
            max-width: 500px;
            margin: 40px auto 50px auto;
            background: var(--container-background);
            padding: 28px 32px 24px 32px;
            border-radius: 16px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            text-align: center;
        }

        .website-rating-section h2 {
            color: var(--primary-colour);
            font-size: 1.5rem;
            margin-bottom: 10px;
        }

        .rating-stars {
            font-size: 2.3rem;
            color: gold;
            letter-spacing: 3px;
            margin-bottom: 6px;
            display: inline-block;
            vertical-align: middle;
        }

        .website-rating-section .rating-info {
            font-size: 1.1rem;
            color: var(--text);
            vertical-align: middle;
        }

        .website-rating-section .rating-count {
            font-size: 0.92rem;
            color: var(--placeholder-colour);
            margin-left: 5px;
        }

        .website-rating-section form {
            margin-top: 22px;
        }

        .website-rating-section label {
            font-weight: 600;
            color: var(--primary-colour);
            font-size: 1.08rem;
        }

        .website-rating-section select {
            padding: 8px 14px;
            border-radius: 7px;
            font-size: 1rem;
            border: 1px solid var(--border-colour);
            outline: none;
            margin-top: 7px;
        }

        .website-rating-section button[type="submit"] {
            background: var(--button-background);
            color: var(--button-text);
            font-weight: 600;
            padding: 7px 24px;
            border-radius: var(--border-radius);
            border: none;
            margin-left: 10px;
            cursor: pointer;
            transition: background 0.2s;
        }
        .website-rating-section button[type="submit"]:hover {
            background: var(--button-hover);
        }

        .website-rating-section .rating-error {
            color: red;
            font-size: 0.98rem;
            margin-top: 7px;
        }

        .website-rating-section .rating-limit-message {
            color: var(--placeholder-colour);
            margin-top: 18px;
        }

        .website-rating-section .rating-login-message {
            margin-top: 18px;
            color: var(--placeholder-colour);
            font-size: 1rem;
        }
        .website-rating-section .rating-login-message a {
            color: var(--link-colour);
            text-decoration: none;
        }

        .website-rating-section .rating-success-message {
            margin-top: 15px;
            color: green;
            font-size: 1rem;
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

    <!-- Rating Section -->
    <section class="website-rating-section">
    <h2>HearTogether Website Rating</h2>
    <!-- Display average rating -->
    <div>
        <span class="rating-stars">
            <?php
            $stars = round($avgRating);
            for ($i = 0; $i < $stars; $i++) echo "&#9733;"; // Filled star
            for ($i = $stars; $i < 5; $i++) echo "&#9734;"; // Empty star
            ?>
        </span>
        <!-- Display rating info -->
        <span class="rating-info">
            <?php echo $avgRating ? $avgRating : "No ratings yet"; ?>/5
            <span class="rating-count">(<?php echo $totalRatings; ?> ratings)</span>
        </span>
    </div>

    <!-- Rating form -->
    <?php if (isset($_SESSION['user_id'])): ?>
        <?php if ($canRate): ?>
            <form method="POST">
                <!-- Rating selection -->
                <label for="website_rating">Leave your rating:</label><br>
                <!-- Rating options -->
                <select name="website_rating" id="website_rating" required>
                    <option value="" disabled selected>Select</option>
                    <option value="5">&#9733;&#9733;&#9733;&#9733;&#9733; (5)</option>
                    <option value="4">&#9733;&#9733;&#9733;&#9733;&#9734; (4)</option>
                    <option value="3">&#9733;&#9733;&#9733;&#9734;&#9734; (3)</option>
                    <option value="2">&#9733;&#9733;&#9734;&#9734;&#9734; (2)</option>
                    <option value="1">&#9733;&#9734;&#9734;&#9734;&#9734; (1)</option>
                </select>
                <!-- Submit button -->
                <button type="submit">Submit</button>
                <!-- Error message -->
                <?php if ($ratingError): ?>
                    <div class="rating-error"><?php echo $ratingError; ?></div>
                <?php endif; ?>
            </form>
            <!-- Limit message -->
        <?php else: ?>
            <div class="rating-limit-message">You can only leave a rating once per month.</div>
        <?php endif; ?>
        <!-- Login message -->
    <?php else: ?>
        <div class="rating-login-message">
            <em>
                <a href="register.php">Register</a> or <a href="login.php">login</a> to leave a rating!
            </em>
        </div>
    <?php endif; ?>

    <!-- Success message -->
    <?php if (isset($_GET['rate']) && $_GET['rate'] === 'success'): ?>
        <div class="rating-success-message">Thank you for your feedback!</div>
        <script>
            setTimeout(() => {
                const url = new URL(window.location);
                url.searchParams.delete('rate');
                window.history.replaceState({}, document.title, url);
            }, 2000);
        </script>
    <?php endif; ?>
</section>

<?php include 'footer.php'; ?>

</body>
<!-- Toast for unauthorised access -->
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
    <!-- JavaScript to show and hide the toast -->
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

<!-- Logout Toast -->
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