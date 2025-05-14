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
    <title>User Profile</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;600&display=swap');

        :root {
            --text: #ecf2f4;
            --background: #0a161a;
            --primary: #87c9e3;
            --secondary: #127094;
            --accent: #29bff9;
        }

        body {
            margin: 0;
            font-family: 'Roboto', sans-serif;
            background-color: var(--background);
            color: var(--text);
        }

        .profile-container {
            max-width: 600px;
            margin: 60px auto;
            background-color: var(--secondary);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        .profile-container img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
            border: 3px solid var(--accent);
        }

        .profile-container h2 {
            margin: 10px 0;
            color: var(--accent);
        }

        .profile-container p {
            font-size: 16px;
            color: var(--text);
        }

        .back-home {
            display: inline-block;
            margin-top: 25px;
            padding: 10px 20px;
            background-color: var(--accent);
            color: var(--background);
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }

        .back-home:hover {
            background-color: #22a2d4;
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
