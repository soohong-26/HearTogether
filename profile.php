<?php
require 'database.php'; // This connects to your DB using $conn

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: homepage.php?error=unauthorised");
    exit();
}

$username = $_SESSION['username'];

// Fetch user details from the database
$stmt = $conn->prepare("SELECT email, profile_img FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "User not found.";
    exit();
}

$user = $result->fetch_assoc();
$email = htmlspecialchars($user['email']);
$profile_img = !empty($user['profile_img']) ? $user['profile_img'] : 'icons/user.png';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>HearTogether - User Profile</title>
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;600&display=swap');

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

    body {
        margin: 0;
        font-family: 'Roboto', sans-serif;
        background-color: var(--background-colour);
        color: var(--text);
    }

    .profile-container {
        max-width: 600px;
        margin: 60px auto;
        background-color: var(--container-background);
        padding: 30px;
        border-radius: 20px;
        box-shadow: var(--box-shadow);
        text-align: center;
    }

    .profile-container img {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 20px;
        border: 3px solid var(--primary-colour);
        background: var(--background-colour);
    }

    .profile-container h2 {
        margin: 10px 0;
        color: var(--primary-colour);
        font-weight: 700;
    }

    .profile-container p {
        font-size: 16px;
        color: var(--text);
    }

    .back-home {
        display: inline-block;
        margin-top: 25px;
        padding: 10px 20px;
        background-color: var(--button-background);
        color: var(--button-text);
        text-decoration: none;
        border-radius: var(--border-radius);
        font-weight: 600;
        transition: background-color var(--transition-speed);
        box-shadow: var(--box-shadow);
    }

    .back-home:hover {
        background-color: var(--button-hover);
    }
</style>
</head>
<body>

<?php include 'nav.php'; ?>

<div class="profile-container">
    <img src="<?php echo htmlspecialchars($profile_img); ?>" alt="Profile Picture">
    <h2><?php echo htmlspecialchars($username); ?></h2>
    <p>Email: <?php echo $email; ?></p>
    <a href="homepage.php" class="back-home">Back to Home</a>
</div>

</body>
</html>
