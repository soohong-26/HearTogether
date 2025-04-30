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
        --text: #ecf2f4;       
        --background: #0a161a;    
        --primary: #87c9e3;      
        --secondary: #127094;     
        --accent: #29bff9;         
    }

    header {
        background-color: var(--background);
        color: #fff;
        padding: 10px 40px;
        margin: 20px 20px 0 20px;
        font-family: 'Roboto', sans-serif;
        display: flex;
        align-items: center;
        border-radius: 20px;
        border: 0.5px solid var(--accent);
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
        color: var(--primary);
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
        color: #fff;
        text-decoration: none;
        font-size: 16px;
        transition: color 0.3s ease;
    }

    .nav-links li a:hover {
        color: var(--secondary);
        text-decoration: none;
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
    }

    .greeting span {
        font-size: 16px;
    }

    .logout-btn {
        background: none;
        border: none;
        cursor: pointer;
        padding: 0;
        margin-top: 7px;
    }

    .logout-btn img {
        width: 20px;
        height: 20px;
        transition: opacity 0.3s ease;
    }

    .logout-btn img:hover {
        opacity: 0.7;
    }
    </style>
</head>
<body>
    <header>
        <!-- Logo -->
        <h2 class="header-title">
            <a href="#">HearTogether</a>
        </h2>

        <!-- Navigation Panel -->
        <nav>
            <ul class="nav-links">
                <!-- Links when clicked -->
                <li><a class='nav-anc' href="#">Home</a></li>
                <li><a class='nav-anc' href="#">Videos</a></li>
                <li><a class='nav-anc' href="#">FAQ</a></li>
            </ul>

            <!-- Greeting and Logout -->
            <div class="greeting">
                <?php if (isset($_SESSION['username'])) : ?>
                    <!-- Profile Picture -->
                    <img src="<?php echo isset($_SESSION['profile_img']) ? $_SESSION['profile_img'] : 'icons/user.png'; ?>" alt="Profile Picture" class="profile">

                    <!-- Username -->
                    <span>Hello, <?php echo htmlspecialchars($_SESSION['username']); ?></span>

                    <!-- Logout Button -->
                    <form action="logout.php" method="post" style="display:inline;">
                        <button type="submit" class="logout-btn" title="Logout">
                            <img src="icons/logout.png" alt="Logout Icon">
                        </button>
                    </form>

                    <!-- No Account -->
                <?php else : ?>
                    <a href="no_account.php" style="display: flex; align-items: center; text-decoration: none; color: inherit;">
                        <img src="icons/user.png" alt="Guest" class="profile">
                        <span>Hello, Guest</span>
                    </a>
                <?php endif; ?>
            </div>
        </nav>
    </header>
</body>
</html>
