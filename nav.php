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

    header {
        background-color: #333;
        color: #fff;
        padding: 10px 40px;
        margin: 20px 20px 0 20px;
        font-family: 'Roboto', sans-serif;
        display: flex;
        align-items: center;
        border-radius: 20px;
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
        color: #7AA3CC;
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
        color: #7AA3CC;
        text-decoration: none;
    }

    .greeting {
        justify-content: flex-end;
    }

    .greeting img {
        width: 25px;
        height: 25px;
        border-radius: 50%;
        margin-right: 10px;
    }

    .greeting span {
        font-size: 16px;
    }
</style>

</head>
<body>
        <header>
            <!-- Logo -->
                <h2 class="header-title">
                    <a href="#">
                        HearTogether
                    </a>
                </h2>

            <!-- Navigation Panel -->
            <nav>
                <ul class="nav-links">
                    <!-- Links when clicked -->
                    <li><a class='nav-anc' href="#">Home</a></li>
                    <li><a class='nav-anc' href="#">Videos</a></li>
                    <li><a class='nav-anc' href="#">FAQ</a></li>
                </ul>

            <!-- Greeting and Profile Picture -->
            <div class="greeting">
                <?php if (isset($_SESSION['username'])) : ?>
                    <img src="<?php echo $_SESSION['profile_img']; ?>" alt="Profile Picture">
                    <span>Hello, <?php echo $_SESSION['username']; ?></span>
                <?php else : ?>
                    <img src="icons/user.png" alt="Guest">
                    <span>Hello, Guest</span>
                <?php endif; ?>
            </div>

            </nav>
        </header>
</body>
</html>