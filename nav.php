<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navigation Bar</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap');

        :root {
            /* Primary Colours */
            --primary-colour: #6A7BA2;
            --primary-hover: #5C728A;

            /* Backgrounds */
            --background-colour: rgb(211, 229, 255);
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
            padding: 35px 40px;
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
            gap: 0;
        }

        .header-title {
            flex: 1 1 0%;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            font-family: "Poppins", sans-serif;
            min-width: 180px;
        }

        .header-title a {
            text-decoration: none;
            color: var(--heading-colour);
            font-size: 22px;
            font-weight: 600;
        }

        nav {
            flex: 2 1 0%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            min-width: 250px;
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

        /* Greeting */
        .greeting-desktop {
            flex: 1 1 0%;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            min-width: 180px;
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

        .greeting form {
            display: flex;
            align-items: center;
            margin: 0 0 0 6px;
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

            .greeting-desktop {
                min-width: 130px;
            }
        }

        @media (max-width: 900px) {
            header {
                flex-direction: column;
                align-items: stretch;
                padding: 10px 6px;
            }

            .header-title, .greeting-desktop {
                min-width: 0;
                width: 100%;
                justify-content: flex-start;
            }

            nav {
                width: 100%;
                min-width: 0;
                justify-content: flex-end;
            }

            .nav-links {
                position: absolute;
                top: 56px;
                left: 0;
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

            .hamburger {
                display: flex;
                margin-left: auto;
            }

            .greeting-desktop {
                display: none;
            }

            .greeting-mobile {
                display: flex !important;
                width: 100%;
                padding: 12px 28px;
                border-top: 1px solid var(--border-colour);
                background: rgb(243, 249, 255);
                gap: 10px;
            }
        }

        @media (min-width: 901px) {
            .greeting-mobile {
                display: none !important;
            }
        }

        .font-size-toggle {
            display: flex;
            align-items: center;
            gap: 4px;
            margin-left: 12px;
            user-select: none;
        }
        .font-size-toggle button {
            background: var(--button-background);
            color: var(--button-text);
            border: none;
            border-radius: 5px;
            padding: 5px 10px;
            font-size: 13px;
            cursor: pointer;
            transition: background 0.2s;
            line-height: 1.1;
        }
        .font-size-toggle button:hover,
        .font-size-toggle button:focus {
            background: var(--button-hover);
        }
        #fontSizeLabel {
            min-width: 18px;
            display: inline-block;
            text-align: center;
            transition: font-size 0.2s;
        }
        
        /* Universal heading override for scaling with font-size toggle */
        h1, h2, h3, h4, h5, h6 {
            font-size: revert;
            font-size: unset;
        }

        h1 { font-size: 2.2rem !important; }
        h2 { font-size: 1.6rem !important; }
        h3 { font-size: 1.3rem !important; }
        h4 { font-size: 1.1rem !important; }
        h5 { font-size: 1rem !important; }
        h6 { font-size: 0.95rem !important; }
    </style>
</head>
<body>
<header>
    <!-- Logo -->
    <div class="header-title">
        <a href="homepage.php">HearTogether</a>
    </div>

    <!-- Font Size Toggle -->
    <div class="font-size-toggle" id="fontSizeToggle">
        <button type="button" id="fontSizeReset" title="Reset font size">Reset</button>
        <button type="button" id="fontSizeSmall" title="Decrease font size">–</button>
        <span id="fontSizeLabel" style="margin: 0 4px; font-size: 15px;">A</span>
        <button type="button" id="fontSizeLarge" title="Increase font size">+</button>
    </div>


    <!-- Navigation Links (CENTER) -->
    <nav>
        <ul class="nav-links" id="navLinks">
            <!-- Homepage -->
            <li>
                <a class='nav-anc <?php echo $currentPage == "homepage.php" ? "active" : ""; ?>' 
                href="homepage.php">Home</a>
            </li>

            <!-- Videos Page -->
            <li>
                <a class='nav-anc <?php echo $currentPage == "videos.php" || $currentPage == "admin_videos.php" ? "active" : ""; ?>' 
                href="<?php echo (isset($_SESSION['roles']) && $_SESSION['roles'] === 'admin') ? 'admin_videos.php' : 'videos.php'; ?>">Videos</a>
            </li>

            <!-- ADMIN ONLY - User Approval -->
            <?php if (isset($_SESSION['roles']) && $_SESSION['roles'] === 'admin') : ?>
                <li>
                    <a class='nav-anc <?php echo $currentPage == "admin_approval.php" ? "active" : ""; ?>' href="admin_approval.php">User Approval</a>
                </li>
            <?php endif; ?>
            
            <!-- FAQ Page -->
            <li>
                <a class='nav-anc <?php echo ($currentPage == "faq.php" || $currentPage == "admin_faq.php") ? "active" : ""; ?>' 
                href="<?php echo (isset($_SESSION['roles']) && $_SESSION['roles'] === 'admin') ? 'admin_faq.php' : 'faq.php'; ?>">
                FAQ
                </a>
            </li>

            <!-- Quiz Page -->
            <li>
                <a class='nav-anc <?php echo ($currentPage == "quiz_home.php" || $currentPage == "admin_quiz.php") ? "active" : ""; ?>' 
                href="<?php echo (isset($_SESSION['roles']) && $_SESSION['roles'] === 'admin') ? 'admin_quiz.php' : 'quiz_home.php'; ?>">
                Quiz
                </a>
            </li>

            <!-- Greeting and Logout (MOBILE) -->
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

        <!-- Hamburger Menu -->
        <button class="hamburger" id="hamburgerMenu" aria-label="Open navigation" tabindex="0">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </nav>

    <!-- Greeting and Logout (DESKTOP) -->
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
</header>

<script>
// Hamburger Menu & Responsive Nav
document.addEventListener("DOMContentLoaded", function() {
    const hamburger = document.getElementById('hamburgerMenu');
    const navLinks = document.getElementById('navLinks');
    const greetingMobile = document.querySelector('.greeting-mobile');
    const greetingDesktop = document.querySelector('.greeting-desktop');

    // Check window size and display correct greeting block
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

    // Hamburger click toggles menu
    hamburger.addEventListener('click', function() {
        navLinks.classList.toggle('open');
    });

    // Keyboard accessibility for hamburger
    hamburger.addEventListener('keydown', function(e) {
        if (e.key === "Enter" || e.key === " ") {
            navLinks.classList.toggle('open');
        }
    });

    // Close menu when a link is clicked (mobile)
    document.querySelectorAll('.nav-links a').forEach(link => {
        link.addEventListener('click', function() {
            if(window.innerWidth <= 900){
                navLinks.classList.remove('open');
            }
        });
    });
});

// Font Size Toggle
const fontSizeSmallBtn = document.getElementById('fontSizeSmall');
const fontSizeLargeBtn = document.getElementById('fontSizeLarge');
const fontSizeResetBtn = document.getElementById('fontSizeReset');
const fontSizeLabel = document.getElementById('fontSizeLabel');
const htmlRoot = document.documentElement;

const FONT_SIZE_KEY = "ht_fontsize";
const DEFAULT_SIZE = 16;
const MIN_SIZE = 12;
const MAX_SIZE = 24;

// Helper to set font size on <html> for whole site
function setFontSize(size) {
    size = Math.max(MIN_SIZE, Math.min(MAX_SIZE, size));
    htmlRoot.style.fontSize = size + "px";
    fontSizeLabel.textContent = "A";
    fontSizeLabel.style.fontSize = size + "px";
    localStorage.setItem(FONT_SIZE_KEY, size);
}

// On load, use saved font size or default
let currentSize = parseInt(localStorage.getItem(FONT_SIZE_KEY)) || DEFAULT_SIZE;
setFontSize(currentSize);

fontSizeSmallBtn.addEventListener('click', function() {
    if (currentSize > MIN_SIZE) {
        currentSize -= 2;
        setFontSize(currentSize);
    }
});
fontSizeLargeBtn.addEventListener('click', function() {
    if (currentSize < MAX_SIZE) {
        currentSize += 2;
        setFontSize(currentSize);
    }
});
fontSizeResetBtn.addEventListener('click', function() {
    currentSize = DEFAULT_SIZE;
    setFontSize(currentSize);
});
</script>
</body>
</html>