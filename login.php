<!-- PHP -->
<?php
// Include database connection file
require 'database.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get email and password from POST request
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare and bind statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    
    // Execute the statement
    $stmt->execute();
    
    // Get the result
    $result = $stmt->get_result();

    // Check if email exists
    if ($result->num_rows > 0) {
        // Fetch user data
        $user = $result->fetch_assoc();
        
        // Verify password (assuming passwords are hashed)
        if (password_verify($password, $user['password'])) {
            
            // Set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['roles'] = $user['roles'];
            $_SESSION['logged_in'] = true;

            // Redirect based on role
            if ($_SESSION['roles'] == 'mentor') {
                header("Location: home_mentors.php");  // Redirect to a different home page for mentors
                exit();
            } else {
                header("Location: home_buddies.php");  // Redirect to the general home page
                exit();
            }
        } else {
            // Invalid password
            echo "<script>
                    alert('Invalid Password!');
                  </script>";
        }
    } else {
        // When the email doesn't exist in the database
        echo "<script>
                    alert('Email does not exist in the database!');
                  </script>";
    }
    // Close statement
    $stmt->close();
}

// Close connection
$conn->close();
?>

<!-- HTML -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HearTogether - Login</title>
    <!-- CSS -->
    <link rel="stylesheet" href="css/login-register.css">
</head>
<body>
    <div class="login-container"></div>
        <!-- Floating box -->
        <div class="login-box">

            <!-- Title -->
            <h2>Login</h2>

            <!-- Input form -->
            <form id="loginForm" action="login.php" method="post">

                <!-- Email -->
                <div class="input-group">
                    <input type="email" id="loginEmail" placeholder="Email" name="email" required>
                    <span class="icon">
                        <img src="icons/mail.png" alt="Email Icon">
                    </span>
                </div>

                <!-- Password -->
                <div class="input-group">
                    <input type="password" id="loginPassword" placeholder="Password" name="password" required>
                    <span class="icon">
                        <img src="icons/lock2.png" alt="Password Icon" id="loginPasswordIcon" class="password-icon">
                    </span>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="login-btn" name="submit">Login</button>
            </form>
            <p class="register-text">Don't have an account?
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
                icon.src = "icons/unlock.png"; // Change to unlock icon
            } else {
                passwordField.type = "password"; // Hide the password
                icon.src = "icons/lock2.png"; // Change to lock icon
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
    </script>
</body>
</html>