<?php
require 'database.php';
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: homepage.php?error=unauthorised");
    exit();
}

$username = $_SESSION['username'];
$selected = $_POST['selected'] ?? [];
$score = 0;

// Fetch questions
$questions = [];
$res = $conn->query("SELECT * FROM quiz_questions");
while ($row = $res->fetch_assoc()) {
    $questions[$row['question_id']] = $row;
}

// Insert attempt
$stmt = $conn->prepare("INSERT INTO quiz_attempts (username, score) VALUES (?, 0)");
$stmt->bind_param("s", $username);
$stmt->execute();
$attempt_id = $conn->insert_id;

// Evaluate and insert responses
foreach ($selected as $qid => $answer) {
    $correct = $questions[$qid]['correct_option'];
    $is_correct = ($answer === $correct) ? 1 : 0;
    if ($is_correct) $score++;

    $stmt = $conn->prepare("INSERT INTO quiz_responses (attempt_id, question_id, selected_option, is_correct) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iisi", $attempt_id, $qid, $answer, $is_correct);
    $stmt->execute();
}

// Update score
$stmt = $conn->prepare("UPDATE quiz_attempts SET score=? WHERE attempt_id=?");
$stmt->bind_param("ii", $score, $attempt_id);
$stmt->execute();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quiz Completed</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            background: var(--background-colour);
            font-family: 'Roboto', sans-serif;
            color: var(--text);
            margin: 0;
        }
        .container {
            max-width: 500px;
            margin: 40px auto;
            padding: 32px;
            background: var(--container-background);
            border-radius: 16px;
            box-shadow: 0 4px 16px rgba(106, 123, 162, 0.1);
            text-align: center;
        }
        h2 {
            color: var(--heading-colour);
        }
        .score {
            font-size: 1.5rem;
            margin: 20px 0;
            color: var(--primary-colour);
        }
        .back-btn {
            background: var(--button-background);
            color: white;
            padding: 10px 24px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            text-decoration: none;
        }
        .back-btn:hover {
            background: var(--button-hover);
        }
    </style>
</head>
<body>
<?php include 'nav.php'; ?>
<main>
    <div class="container">
        <h2>Quiz Completed!</h2>
        <p class="score">You scored <?= $score ?> out of <?= count($selected) ?>!</p>
        <a href="quiz_home.php" class="back-btn">Back to Quiz Home</a>
    </div>
</main>
</body>
</html>
