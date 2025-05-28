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
        background-color: rgb(243, 249, 255);
        color: var(--text);
        padding: 10px 40px;
        margin: 20px 20px 0 20px;
        font-family: 'Roboto', sans-serif;
        display: flex;
        align-items: center;
        border-radius: 20px;
        border: 1px solid var(--border-colour);
        box-shadow: var(--box-shadow);
        justify-content: space-between;
        flex-wrap: wrap;
        position: relative;
    }

    .header-title {
        flex: 1;
        display: flex;
        align-items: center;
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
        flex: 3 1 0%;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        position: relative;
    }

    .nav-links {
        list-style: none;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 48px;
        padding: 0;
        margin: 0;
        transition: max-height 0.4s ease;
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
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 10px;
        margin-left: 32px;
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

    .greeting form {
        display: flex;
        align-items: center;
        margin: 0 0 0 6px; /* small left margin for spacing */
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
        vertical-align: middle;
    }

    .logout-btn img:hover {
        opacity: 0.7;
    }

    .greeting a {
        color: var(--text);
        text-decoration: none;
        font-weight: 500;
        display: flex;
        align-items: center;
    }

    /* Hamburger styles */
    .hamburger {
        display: none;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        width: 34px;
        height: 34px;
        background: none;
        border: none;
        cursor: pointer;
        margin-left: 18px;
        z-index: 101;
    }
    .hamburger span {
        width: 26px;
        height: 3.5px;
        background: var(--primary-colour);
        margin: 4px 0;
        border-radius: 2px;
        transition: 0.4s;
        display: block;
    }

    /* Responsive Section */
    @media (max-width: 1100px) {
        .nav-links {
            gap: 28px;
        }
        header {
            padding: 10px 16px;
        }
        .greeting {
            margin-left: 16px;
        }
    }

    @media (max-width: 900px) {
        header {
            flex-direction: column;
            align-items: stretch;
            padding: 10px 6px;
        }
        nav {
            width: 100%;
            flex-direction: row;
            justify-content: space-between;
        }
        .nav-links {
            position: absolute;
            top: 56px;
            right: 0;
            width: 100vw;
            background: rgb(243, 249, 255);
            flex-direction: column;
            align-items: flex-start;
            gap: 0;
            padding: 0;
            max-height: 0;
            overflow: hidden;
            border-radius: 0 0 16px 16px;
            box-shadow: var(--box-shadow);
            z-index: 100;
            border-top: 1px solid var(--border-colour);
            transition: max-height 0.4s cubic-bezier(.68,-0.55,.27,1.55);
        }
        .nav-links.open {
            max-height: 450px;
            padding: 16px 0;
        }
        .nav-links li {
            width: 100%;
            padding: 10px 28px;
        }
        .greeting {
            margin: 10px 0 10px 18px;
            padding: 6px 0;
        }
        .hamburger {
            display: flex;
        }
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
        <ul class="nav-links" id="navLinks">
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
            <!-- FAQ Control Panel (Admins Only) -->
            <?php if (isset($_SESSION['roles']) && $_SESSION['roles'] === 'admin') : ?>
                <li>
                    <a class='nav-anc <?php echo $currentPage == "admin_faq.php" ? "active" : ""; ?>' href="admin_faq.php">
                        FAQ Control
                    </a>
                </li>
            <?php endif; ?>
            <!-- Greeting and Logout - MOBILE: duplicated for better menu flow -->
            <li class="greeting greeting-mobile" style="display: none;">
            <?php if (isset($_SESSION['username'])) : ?>
                <img src="<?php echo isset($_SESSION['profile_img']) ? $_SESSION['profile_img'] : 'images/profile.png'; ?>" alt="Profile Picture" class="profile">
                <a href="profile.php">
                    <span>Hello, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                </a>
                <form action="logout.php" method="post" style="display:inline;">
                    <button type="submit" class="logout-btn" title="Logout">
                        <img src="icons/logout.png" alt="Logout Icon">
                    </button>
                </form>
            <?php else : ?>
                <a href="no_account.php">
                    <img src="icons/user_black.svg" alt="Guest" class="profile">
                    <span>Hello, Guest</span>
                </a>
            <?php endif; ?>
            </li>
        </ul>
        <!-- Greeting and Logout - DESKTOP -->
        <div class="greeting greeting-desktop">
        <?php if (isset($_SESSION['username'])) : ?>
            <img src="<?php echo isset($_SESSION['profile_img']) ? $_SESSION['profile_img'] : 'images/profile.png'; ?>" alt="Profile Picture" class="profile">
            <a href="profile.php">
                <span>Hello, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            </a>
            <form action="logout.php" method="post" style="display:inline;">
                <button type="submit" class="logout-btn" title="Logout">
                    <img src="icons/logout.png" alt="Logout Icon">
                </button>
            </form>
        <?php else : ?>
            <a href="no_account.php">
                <img src="icons/user_black.svg" alt="Guest" class="profile">
                <span>Hello, Guest</span>
            </a>
        <?php endif; ?>
        </div>
        <!-- Hamburger Menu -->
        <button class="hamburger" id="hamburgerMenu" aria-label="Open navigation" tabindex="0">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </nav>
</header>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const hamburger = document.getElementById('hamburgerMenu');
    const navLinks = document.getElementById('navLinks');
    const greetingMobile = document.querySelector('.greeting-mobile');
    const greetingDesktop = document.querySelector('.greeting-desktop');

    function checkWidth() {
        if(window.innerWidth <= 900){
            greetingMobile.style.display = 'flex';
            greetingDesktop.style.display = 'none';
        } else {
            greetingMobile.style.display = 'none';
            greetingDesktop.style.display = 'flex';
            navLinks.classList.remove('open');
        }
    }

    // Initial check
    checkWidth();
    // Listen for resize
    window.addEventListener('resize', checkWidth);

    // Hamburger click
    hamburger.addEventListener('click', function() {
        navLinks.classList.toggle('open');
    });

    // Keyboard accessibility
    hamburger.addEventListener('keydown', function(e) {
        if (e.key === "Enter" || e.key === " ") {
            navLinks.classList.toggle('open');
        }
    });

    // Close menu when link clicked (mobile)
    document.querySelectorAll('.nav-links a').forEach(link => {
        link.addEventListener('click', function() {
            if(window.innerWidth <= 900){
                navLinks.classList.remove('open');
            }
        });
    });
});
</script>
</body>
</html>
