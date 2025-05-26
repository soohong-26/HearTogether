<!-- HTML -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navigation Bar</title>
    <!-- CSS -->
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap');

    :root {
            /* Primary Colours */
            --primary-colour: #6A7BA2;
            --primary-hover: #5C728A;

            /* Backgrounds */
            --background-colour:rgb(211, 229, 255);
            --container-background: #ffffff;
            --input-background: #ffffff;

            /* Text Colours */
            --text: #333333;
            --placeholder-colour: #999999;
            --heading-colour: #2C3E50;

            /* Borders & Lines */
            --border-colour: #cccccc;
            --focus-border-colour: #738678;

            /* Buttons */
            --button-background: var(--primary-colour);
            --button-hover: var(--primary-hover);
            --button-text: #ffffff;

            /* Links */
            --link-colour: #1a73e8;
            --link-hover: #1558b0;

            /* Toast */
            --toast-success-bg: #1d8a47;
            --toast-error-bg: #ff5e57;

            /* Misc */
            --box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            --border-radius: 8px;
            --transition-speed: 0.3s;
        }

    header {
        background-color:rgb(243, 249, 255);
        color: var(--text);
        padding: 10px 40px;
        margin: 20px 20px 0 20px;
        font-family: 'Roboto', sans-serif;
        display: flex;
        align-items: center;
        border-radius: 20px;
        border: 1px solid var(--border-colour);
        box-shadow: var(--box-shadow);
    }

    .header-title, nav,
    .greeting {
        flex: 1;
        display: flex;
        align-items: center;
    }

    .header-title {
        justify-content: flex-start;
        font-family: "Poppins", sans-serif;
    }

    .header-title a {
        text-decoration: none;
        color: var(--heading-colour);
        font-size: 22px;
        font-weight: 600;
    }

    nav {
        justify-content: center;
    }

    .nav-links {
        list-style: none;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 48px;
        padding: 0;
        margin: 0;
    }

    .nav-links li a {
        color: var(--link-colour);
        text-decoration: none;
        font-size: 16px;
        transition: color var(--transition-speed) ease;
    }

    .nav-links li a:hover {
        color: var(--link-hover);
        text-decoration: none;
    }

    .nav-links li a.active {
        color: var(--primary-colour);
        font-weight: 600;
    }

    .greeting {
        justify-content: flex-end;
        align-items: center;
        gap: 10px;
    }

    .greeting img.profile {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        margin-right: 5px;
        border: 1.5px solid var(--border-colour);
        background: var(--container-background);
    }

    .greeting span {
        font-size: 16px;
        color: var(--text);
    }

    .logout-btn {
        background: none;
        border: none;
        cursor: pointer;
        padding: 0;
    }

    .logout-btn img {
        width: 20px;
        height: 20px;
        transition: opacity var(--transition-speed) ease;
    }

    .logout-btn img:hover {
        opacity: 0.7;
    }

    /* For guest link styling */
    .greeting a {
        color: var(--text);
        text-decoration: none;
        font-weight: 500;
        display: flex;
        align-items: center;
    }

    /* Specific for Videos nav link (if ever used for special styling) */
    .video-section .nav-anc[href="videos.php"] {
        color: var(--text);
    }
    </style>
</head>
<body>

    <?php $currentPage = basename($_SERVER['PHP_SELF']); ?>

    <header>
        <!-- Logo -->
        <h2 class="header-title">
            <a href="homepage.php">HearTogether</a>
        </h2>

        <!-- Navigation Panel -->
        <nav>
            <ul class="nav-links">
                <!-- Home Link -->
                <li>
                    <a class='nav-anc <?php echo $currentPage == "homepage.php" ? "active" : ""; ?>' 
                    href="<?php echo (isset($_SESSION['roles']) && $_SESSION['roles'] === 'admin') ? 'homepage.php' : 'homepage.php'; ?>">
                    Home
                    </a>
                </li>

                <!-- Videos Link -->
                <li>
                    <a class='nav-anc <?php echo $currentPage == "videos.php" || $currentPage == "admin_videos.php" ? "active" : ""; ?>' 
                    href="<?php echo (isset($_SESSION['roles']) && $_SESSION['roles'] === 'admin') ? 'admin_videos.php' : 'videos.php'; ?>">
                    Videos
                    </a>
                </li>

                <!-- User Approval (Admins Only) -->
                <?php if (isset($_SESSION['roles']) && $_SESSION['roles'] === 'admin') : ?>
                    <li>
                        <a class='nav-anc <?php echo $currentPage == "admin_approval.php" ? "active" : ""; ?>' href="admin_approval.php">
                            User Approval
                        </a>
                    </li>
                <?php endif; ?>

                <!-- FAQ Link -->
                <li>
                    <a class='nav-anc <?php echo $currentPage == "faq.php" ? "active" : ""; ?>' href="faq.php">
                        FAQ
                    </a>
                </li>
            </ul>

            <!-- Greeting and Logout -->
            <div class="greeting">
                <?php if (isset($_SESSION['username'])) : ?>
                    <!-- Profile Picture -->
                    <img src="<?php echo isset($_SESSION['profile_img']) ? $_SESSION['profile_img'] : 'images/profile.png'; ?>" alt="Profile Picture" class="profile">

                    <!-- Username with link to profile -->
                    <a href="profile.php">
                        <span>Hello, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    </a>

                    <!-- Logout Button -->
                    <form action="logout.php" method="post" style="display:inline;">
                        <button type="submit" class="logout-btn" title="Logout">
                            <img src="icons/logout.png" alt="Logout Icon">
                        </button>
                    </form>

                <?php else : ?>
                    <a href="no_account.php">
                        <img src="icons/user.png" alt="Guest" class="profile">
                        <span>Hello, Guest</span>
                    </a>
                <?php endif; ?>
            </div>
        </nav>
    </header>
</body>
</html>