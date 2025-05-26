<?php

if (isset($_SESSION['username'])) {
    // Redirect to logged-in page if session exists
    header("Location: logged_in.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Account Required</title>
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap');

    :root {
        /* Primary Colours */
        --primary-colour: #6A7BA2;
        --primary-hover: #5C728A;

        /* Backgrounds */
        --background-colour: rgb(211, 229, 255);
        --container-background: #ffffff;

        /* Text Colours */
        --text: #333333;
        --heading-colour: #2C3E50;

        /* Borders & Lines */
        --border-colour: #cccccc;

        /* Buttons */
        --button-background: var(--primary-colour);
        --button-hover: var(--primary-hover);
        --button-text: #ffffff;

        /* Accent */
        --accent: #29bff9;

        /* Misc */
        --box-shadow: 0 2px 6px rgba(0, 0, 0, 0.12);
        --border-radius: 16px;
        --transition-speed: 0.3s;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Roboto', sans-serif;
        background-color: var(--background-colour);
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }

    .container {
        background-color: var(--container-background);
        padding: 40px;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        text-align: center;
        max-width: 400px;
        width: 100%;
    }

    h2 {
        color: var(--primary-colour);
        margin-bottom: 15px;
        font-weight: 700;
        letter-spacing: 1px;
    }

    p {
        color: var(--text);
        margin-bottom: 25px;
        font-size: 17px;
    }

    .btn {
        display: inline-block;
        padding: 10px 22px;
        margin: 10px 5px;
        border: none;
        border-radius: 8px;
        text-decoration: none;
        font-weight: bold;
        color: var(--button-text);
        transition: background-color var(--transition-speed);
        font-size: 16px;
        box-shadow: var(--box-shadow);
    }

    .login-btn {
        background-color: var(--button-background);
    }

    .login-btn:hover {
        background-color: var(--button-hover);
    }

    .register-btn {
        background-color: var(--accent);
    }

    .register-btn:hover {
        background-color: #22a2d4;
    }

    .back-btn {
        position: absolute;
        top: 20px;
        left: 20px;
        color: var(--primary-colour);
        text-decoration: none;
        font-size: 18px;
        font-weight: bold;
        background: none;
        border: none;
        padding: 5px 10px;
        cursor: pointer;
        transition: color var(--transition-speed);
    }

    .back-btn:hover {
        color: var(--accent);
    }

</style>
</head>
<body>
    <!-- Back button -->
    <a href="javascript:history.back()" class="back-btn" title="Go back">← Back</a>

    <div class="container">
        <h2>Welcome!</h2>
        <p>You need to log in or register before continuing.</p>
        <a href="login.php" class="btn login-btn">Log In</a>
        <a href="register.php" class="btn register-btn">Register</a>
    </div>
</body>
</html>
