<?php
require 'database.php';

// Only admins allowed
if (!isset($_SESSION['username']) || $_SESSION['roles'] !== 'admin') {
    header("Location: homepage.php?error=unauthorised");
    exit();
}

// Fetch all users who took the quiz
$users = [];
$ures = $conn->query("SELECT DISTINCT username FROM quiz_attempts ORDER BY username ASC");
while ($row = $ures->fetch_assoc()) {
    $users[] = $row['username'];
}

// Selected user logic
$user_history = [];
$selected_user = '';
$user_email = '';
$profile_img = 'icons/user.png';

// If a user is selected
if (isset($_GET['user']) && $_GET['user'] !== '') {
    $selected_user = $_GET['user'];

    // Fetch quiz history for selected user
    $hstmt = $conn->prepare("SELECT score, attempt_date FROM quiz_attempts WHERE username=? ORDER BY attempt_date DESC");
    $hstmt->bind_param("s", $selected_user);
    $hstmt->execute();
    $hres = $hstmt->get_result();
    while ($row = $hres->fetch_assoc()) {
        $user_history[] = $row;
    }
    $hstmt->close();

    // Fetch user info for profile display
    $ustmt = $conn->prepare("SELECT email, profile_img FROM users WHERE username=?");
    $ustmt->bind_param("s", $selected_user);
    $ustmt->execute();
    $ures = $ustmt->get_result();
    if ($ures && $ures->num_rows > 0) {
        $uinfo = $ures->fetch_assoc();
        $user_email = htmlspecialchars($uinfo['email']);
        $profile_img = !empty($uinfo['profile_img']) ? htmlspecialchars($uinfo['profile_img']) : 'icons/user.png';
    }
    $ustmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>HearTogether - User Quiz Scores</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;600&display=swap');
    :root {
        --primary-colour: #6A7BA2;
        --primary-hover: #5C728A;
        --background-colour: rgb(211, 229, 255);
        --container-background: #ffffff;
        --text: #333333;
        --heading-colour: #2C3E50;
        --border-colour: #cccccc;
        --button-background: var(--primary-colour);
        --button-hover: var(--primary-hover);
        --danger: #ff5e57;
        --success: #1d8a47;
    }
    body {
        background: var(--background-colour);
        font-family: 'Roboto', sans-serif;
        margin: 0;
        color: var(--text);
    }
    .container {
        background: var(--container-background);
        max-width: 800px;
        margin: 40px auto 20px;
        padding: 32px 24px;
        border-radius: 16px;
        box-shadow: 0 4px 16px rgba(106, 123, 162, 0.1);
    }
    h2, h3 {
        color: var(--heading-colour);
        text-align: center;
    }
    .form-row {
        display: flex;
        gap: 12px;
        margin-bottom: 18px;
        flex-wrap: wrap;
        align-items: center;
        justify-content: center;
    }
    .form-row label {
        font-weight: 600;
        min-width: 110px;
    }
    .form-row input[type="text"], .form-row select {
        flex: 2 1 250px;
        padding: 5px 10px;
        border: 1px solid var(--border-colour);
        border-radius: 6px;
        font-size: 1rem;
    }
    .user-profile-summary {
        display: flex;
        align-items: center;
        gap: 18px;
        justify-content: center;
        margin: 28px 0 18px 0;
    }
    .user-profile-summary img {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        border: 2.5px solid var(--primary-colour);
        background: var(--background-colour);
        object-fit: cover;
    }
    .user-profile-summary .info {
        font-size: 1.1rem;
        color: var(--heading-colour);
        font-weight: 600;
    }
    .user-profile-summary .email {
        font-size: 0.95rem;
        color: var(--text);
        font-weight: 400;
    }
    .user-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 24px;
        font-size: 1rem;
    }
    .user-table th, .user-table td {
        border: 1px solid var(--border-colour);
        padding: 8px;
        text-align: left;
    }
    .user-table th {
        background: var(--primary-colour);
        color: #fff;
        font-size: 1.05rem;
    }
    .center {
        text-align: center;
    }
    .no-attempts {
        text-align: center;
        color: #999;
        font-style: italic;
        padding: 16px 0;
    }
    .back-link {
        display: inline-block;
        margin-top: 28px;
        padding: 8px 22px;
        background: var(--button-background);
        color: #fff;
        border-radius: 7px;
        text-decoration: none;
        font-weight: 500;
        font-size: 1rem;
        transition: background 0.2s;
        box-shadow: 0 2px 6px rgba(60,200,255,0.08);
    }
    .back-link:hover {
        background: var(--button-hover);
    }
    </style>
</head>
<body>
<?php include 'nav.php'; ?>
<main>
    <div class="container">
        <h2>User Quiz Attempts</h2>
        <!-- User selection -->
        <form method="get" class="form-row" style="justify-content:center;">
            <label for="user" style="margin-top: 5px;">Select user:</label>
            <select name="user" id="user" class="styled-select" onchange="this.form.submit()">
                <option value="">-- Choose User --</option>
                <?php foreach ($users as $u): ?>
                    <option value="<?= htmlspecialchars($u) ?>" <?= ($selected_user === $u) ? 'selected' : '' ?>><?= htmlspecialchars($u) ?></option>
                <?php endforeach; ?>
            </select>
        </form>
        <!-- User profile -->
        <?php if ($selected_user): ?>
            <div class="user-profile-summary">
                <img src="<?= htmlspecialchars($profile_img) ?>" alt="Profile of <?= htmlspecialchars($selected_user) ?>">
                <div>
                    <!-- User info -->
                    <div class="info"><?= htmlspecialchars($selected_user) ?></div>
                    <!-- User email -->
                    <div class="email"><?= $user_email ?></div>
                </div>
            </div>

            <h4 class="center"><?= htmlspecialchars($selected_user) ?>'s Quiz History</h4>
            <!-- User history -->
            <table class="user-table">
                <thead>
                    <tr><th>Date</th><th>Score</th></tr>
                </thead>

                <tbody>
                    <?php if (count($user_history) > 0): ?>
                        <!-- Each attempt -->
                        <?php foreach ($user_history as $h): ?>
                        <tr>
                            <td><?= htmlspecialchars($h['attempt_date']) ?></td>
                            <td><?= htmlspecialchars($h['score']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- No attempts -->
                        <tr><td colspan="2" class="no-attempts">No attempts yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?>
        <!-- Back to the main quiz manager -->
        <div class="center">
            <a href="admin_quiz.php" class="back-link">&larr; Back to Quiz Manager</a>
        </div>
    </div>
</main>
</body>
</html>
