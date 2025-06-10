<!-- PHP -->
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check only users who are approved
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND is_approved = 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['roles'] = $user['roles'];
            $_SESSION['logged_in'] = true;

            if ($_SESSION['roles'] === 'admin') {
                header("Location: homepage.php"); // Adjust if admin has a separate dashboard
                exit();
            } else {
                header("Location: homepage.php");
                exit();
            }
        } else {
            echo "<script>alert('Invalid password!'); window.location.href = 'login.php';</script>";
        }
    } else {
        // Either the email doesn’t exist OR the user isn’t approved yet
        // So let's do another query just to confirm the cause and give better feedback
        $checkPendingStmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $checkPendingStmt->bind_param("s", $email);
        $checkPendingStmt->execute();
        $pendingResult = $checkPendingStmt->get_result();

        if ($pendingResult->num_rows > 0) {
            echo "<script>alert('Your account is pending approval. Please wait for an admin to approve your account.'); window.location.href = 'login.php';</script>";
        } else {
            echo "<script>alert('Email does not exist in the system.'); window.location.href = 'login.php';</script>";
        }

        $checkPendingStmt->close();
    }

    $stmt->close();
}
$conn->close();

$msg = "";
if (isset($_GET['msg'])) {
    $msg = $_GET['msg'];
}
?>

<!-- HTML -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HearTogether - Login</title>
    <!-- CSS -->
    <link rel="stylesheet" href="css/login-register.css?v=<?= time(); ?>">
</head>
<body>
    <div id="toast" style="display:none;">
        <span id="toast-msg"></span>
    </div>

    <div class="login-container"></div>
        <!-- Floating box -->
        <div class="login-box">

            <!-- Title -->
            <h2>Login</h2>

            <!-- Input form -->
            <form id="loginForm" action="login.php" method="post" autocomplete="off">

                <!-- Email -->
                <div class="input-group">
                    <input type="email" id="loginEmail" placeholder="Email" name="email" required>
                    <span class="icon">
                        <img src="icons/mail_black.svg" alt="Email Icon">
                    </span>
                </div>

                <!-- Password -->
                <div class="input-group">
                    <input type="password" id="loginPassword" placeholder="Password" name="password" required> 
                    <span class="icon">
                        <img src="icons/lock2_black.svg" alt="Password Icon" id="loginPasswordIcon" class="password-icon">
                    </span>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="login-btn" name="submit">Login</button>
            </form>
            <p class="register-text" style="margin-bottom: 30px">Don't have an account?
                <a href="register.php"> Register Now</a>
            </p>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // Toggle Password Visibility Function 
        function togglePasswordVisibility(passwordFieldId, iconId) {
            const passwordField = document.getElementById(passwordFieldId);
            const icon = document.getElementById(iconId);
            
            // Check current type of the password field
            if (passwordField.type === "password") {
                passwordField.type = "text"; // Show the password
                icon.src = "icons/unlock_black.svg"; // Change to unlock icon
            } else {
                passwordField.type = "password"; // Hide the password
                icon.src = "icons/lock2_black.svg"; // Change to lock icon
            }
        }

        // Add event listeners for each password field's icon
        const registerPasswordIcon = document.getElementById('registerPasswordIcon');
        const confirmPasswordIcon = document.getElementById('confirmPasswordIcon');
        const loginPasswordIcon = document.getElementById('loginPasswordIcon');

        if (registerPasswordIcon) {
            registerPasswordIcon.addEventListener('click', function() {
                togglePasswordVisibility('registerPassword', 'registerPasswordIcon');
            });
        }

        if (confirmPasswordIcon) {
            confirmPasswordIcon.addEventListener('click', function() {
                togglePasswordVisibility('confirmPassword', 'confirmPasswordIcon');
            });
        }

        if (loginPasswordIcon) {
            loginPasswordIcon.addEventListener('click', function() {
                togglePasswordVisibility('loginPassword', 'loginPasswordIcon');
            });
        }

        // Toast show/hide function
        function showToast(message) {
            var toast = document.getElementById('toast');
            var toastMsg = document.getElementById('toast-msg');
            toastMsg.textContent = message;
            toast.className = 'toast show';
            toast.style.display = 'block';
            setTimeout(function(){
                toast.classList.remove('show');
                setTimeout(function() {
                    toast.style.display = 'none';
                }, 500);
            }, 2800);
        }
        <?php if (!empty($msg)) : ?>
            showToast("<?= addslashes($msg) ?>");
        <?php endif; ?>
    </script>
</body>
</html>