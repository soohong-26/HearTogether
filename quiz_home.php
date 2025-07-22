<?php
require 'database.php';

// If the user is not logged in it will not allow them access
if (!isset($_SESSION['username'])) {
    header("Location: homepage.php?error=unauthorised");
    exit();
}

// Storing the current user's username
$username = $_SESSION['username'];

// Fetching the quiz attempt history from the database (latest first)
$history = [];
$histRes = $conn->prepare("SELECT attempt_id, score, attempt_date FROM quiz_attempts WHERE username=? ORDER BY attempt_date DESC");
$histRes->bind_param("s", $username);
$histRes->execute();
$historyRes = $histRes->get_result();

// Store each attempt as an asociative array
while ($row = $historyRes->fetch_assoc()) {
    $history[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>HearTogether - Quiz</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            background: var(--background-colour);
            font-family: 'Roboto', sans-serif;
            margin: 0;
            color: var(--text);
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            padding: 32px;
            background: var(--container-background);
            border-radius: 16px;
            box-shadow: 0 4px 16px rgba(106, 123, 162, 0.1);
        }
        .quiz-btn {
            background: var(--button-background);
            color: white;
            padding: 12px 28px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            display: block;
            margin: 20px auto;
            cursor: pointer;
            transition: background 0.2s;
        }
        .quiz-btn:hover {
            background: var(--button-hover);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
        }
        th, td {
            border: 1px solid var(--border-colour);
            padding: 10px;
            text-align: center;
        }
        h2, h3 {
            text-align: center;
            color: var(--text);
        }
    </style>
</head>
<body>
<?php include 'nav.php'; ?>
<main>
    <div class="container">
        <!-- Page title -->
        <h2>Quiz Centre</h2>

        <!-- Start new quiz button -->
        <form action="quiz_start.php" method="post">
            <button type="submit" class="quiz-btn">Start New Quiz</button>
        </form>

        <!-- Section title for quiz history -->
        <h3>Your Past Scores</h3>
        <?php if (count($history) > 0): ?>
            <!-- Show history table (if available) -->
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Score</th>
                        <th>Action</th>
                        <th>Download</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($history as $h): ?>
                        <tr>
                            <td><?= htmlspecialchars($h['attempt_date']) ?></td>
                            <td><?= htmlspecialchars($h['score']) ?></td>
                            <td>
                                <!-- Link to view the selected quiz attempt details -->
                                <a href="quiz_view.php?attempt_id=<?= $h['attempt_id'] ?>" style="text-decoration: none; color: var(--primary-colour); font-weight: bold;">View</a>
                            </td>
                            <td>
                                <!-- Link to download the selected quiz attempt as a file -->
                                <a href="quiz_download.php?attempt_id=<?= $h['attempt_id'] ?>" style="text-decoration: none; color: var(--primary-colour); font-weight: bold;">Download</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <!-- Message for the user who have never attempted a quiz -->
            <p style="text-align: center;">You haven't completed any quizzes yet. Click above to begin!</p>
        <?php endif; ?>
    </div>
</main>
</body>
</html>
