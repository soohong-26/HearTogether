<?php
require 'database.php'; // This connects to your DB using $conn

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: homepage.php?error=unauthorised");
    exit();
}

$username = $_SESSION['username'];
$toast_msg = '';
$toast_type = 'success';

// Handle profile image upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_img'])) {
    if ($_FILES['profile_img']['error'] === UPLOAD_ERR_OK && is_uploaded_file($_FILES['profile_img']['tmp_name'])) {
        $targetDir = 'profile/';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        $ext = pathinfo($_FILES['profile_img']['name'], PATHINFO_EXTENSION);
        $filename = $username . '_' . uniqid() . '.' . strtolower($ext); // Unique filename
        $targetFile = $targetDir . $filename;

        // Allow only jpg, jpeg, png, gif
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array(strtolower($ext), $allowed)) {
            if (move_uploaded_file($_FILES['profile_img']['tmp_name'], $targetFile)) {
                // Save path (relative to site root)
                $img_path = $targetFile;
                // Update DB
                $stmt = $conn->prepare("UPDATE users SET profile_img=? WHERE username=?");
                $stmt->bind_param("ss", $img_path, $username);
                $stmt->execute();
                // Update session for nav bar
                $_SESSION['profile_img'] = $img_path;
                $toast_msg = "Profile picture updated!";
                $toast_type = 'success';
            } else {
                $toast_msg = "Failed to upload image.";
                $toast_type = 'error';
            }
        } else {
            $toast_msg = "Only JPG, JPEG, PNG, and GIF are allowed.";
            $toast_type = 'error';
        }
    } else {
        $toast_msg = "No file selected or upload error.";
        $toast_type = 'error';
    }
}

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

// Calculate average quiz score
$avg_score = null;
$qstmt = $conn->prepare("SELECT AVG(score) AS avg_score FROM quiz_attempts WHERE username=?");
$qstmt->bind_param("s", $username);
$qstmt->execute();
$qresult = $qstmt->get_result();
if ($row = $qresult->fetch_assoc()) {
    $avg_score = is_null($row['avg_score']) ? null : round($row['avg_score'], 2);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>HearTogether - User Profile</title>
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;600&display=swap');
    
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
    /* Custom styled file input */
    .profile-form label.file-label {
        display: inline-block;
        background: var(--button-background);
        color: var(--button-text);
        padding: 10px 24px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        transition: background 0.2s;
        margin-bottom: 16px;
    }
    .profile-form label.file-label:hover {
        background: var(--button-hover);
    }
    .profile-form input[type="file"] {
        display: none;
    }
    .profile-form button {
        background: var(--button-background);
        color: var(--button-text);
        border: none;
        padding: 8px 22px;
        border-radius: 6px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        margin-left: 8px;
        transition: background 0.2s;
        margin-top: 10px;
    }
    .profile-form button:hover {
        background: var(--button-hover);
    }
    .quiz-score {
        font-size: 17px;
        font-weight: 500;
        margin-top: 15px;
        margin-bottom: 5px;
        color: var(--primary-colour);
    }
    /* Toast styles */
    #toast {
        position: fixed;
        top: 20px;
        right: 20px;
        max-width: 320px;
        padding: 14px 20px;
        border-radius: var(--border-radius);
        font-weight: 600;
        color: white;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.5s ease;
        z-index: 9999;
    }
    #toast.success { background-color: var(--toast-success-bg); }
    #toast.error { background-color: var(--toast-error-bg); }
    </style>
</head>
<body>

<?php include 'nav.php'; ?>

<div class="profile-container">
    <img src="<?php echo htmlspecialchars($profile_img); ?>" alt="Profile Picture">
    <h2><?php echo htmlspecialchars($username); ?></h2>
    
    <p><b style="color:var(--primary-colour);">Email:</b> <?php echo $email; ?></p>

    <form method="POST" enctype="multipart/form-data" class="profile-form" style="margin-top:18px;">
        <label class="file-label" for="profile_img">Choose New Profile Picture</label>
        <input type="file" id="profile_img" name="profile_img" accept="image/png, image/jpeg, image/jpg, image/gif">
        <button type="submit">Upload</button>
    </form>
    <?php if (!is_null($avg_score)) : ?>
        <div class="quiz-score">
            Average Quiz Score: <?php echo htmlspecialchars($avg_score); ?>
        </div>
    <?php endif; ?>
    <a href="homepage.php" class="back-home">Back to Home</a>
</div>
<div id="toast"></div>
<script>
    document.getElementById('profile_img').addEventListener('change', function() {
        if (this.files.length > 0) {
            document.querySelector('.file-label').textContent = this.files[0].name;
        } else {
            document.querySelector('.file-label').textContent = "Choose New Profile Picture";
        }
    });

    // Toast helper
    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast');
        toast.textContent = message;
        toast.className = type;
        toast.style.opacity = '1';
        toast.style.pointerEvents = 'auto';
        clearTimeout(window.toastTimeout);
        window.toastTimeout = setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.pointerEvents = 'none';
        }, 3500);
    }
    <?php if (!empty($toast_msg)): ?>
        showToast("<?php echo addslashes($toast_msg); ?>", "<?php echo $toast_type; ?>");
    <?php endif; ?>
</script>
</body>
</html>
