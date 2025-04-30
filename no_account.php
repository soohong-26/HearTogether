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
            --text: #ecf2f4;       
            --background: #0a161a;    
            --primary: #87c9e3;      
            --secondary: #127094;     
            --accent: #29bff9;         
            }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--background);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: var(--secondary);
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 400px;
            width: 100%;
        }

        h2 {
            color: var(--primary);
            margin-bottom: 15px;
        }

        p {
            color: var(--text);
            margin-bottom: 25px;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 10px 5px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            color: white;
            transition: background-color 0.3s ease;
        }

        .login-btn {
            background-color: var(--button);
        }

        .login-btn:hover {
            background-color: #357ABD;
        }

        .register-btn {
            background-color: var(--accent);
        }

        .register-btn:hover {
            background-color: #357ABD;
        }

        .back-btn {
            position: absolute;
            top: 20px;
            left: 20px;
            color: var(--text);
            text-decoration: none;
            font-size: 18px;
            font-weight: bold;
            background: none;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .back-btn:hover {
            color: var(--primary);
        }

    </style>
</head>
<body>
    <!-- Back button -->
    <a href="javascript:history.back()" class="back-btn" title="Go back">← Back</a>

    <div class="container">
        <h2>Account Required</h2>
        <p>You need to log in or register before continuing.</p>
        <a href="login.php" class="btn login-btn">Log In</a>
        <a href="register.php" class="btn register-btn">Register</a>
    </div>
</body>
</html>
