<?php
require 'database.php';

if (!isset($_SESSION['username'])) {
    header("Location: homepage.php?error=unauthorised");
    exit();
}

$username = $_SESSION['username'];

// Handle quiz submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quiz_submit'])) {
    $selected = $_POST['selected'] ?? [];
    $score = 0;

    // Fetch questions for answer checking
    $questionsRes = $conn->query("SELECT * FROM quiz_questions");
    $questions = [];
    while ($row = $questionsRes->fetch_assoc()) {
        $questions[] = $row;
    }

    // Insert attempt first
    $stmt = $conn->prepare("INSERT INTO quiz_attempts (username, score) VALUES (?, 0)");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $attempt_id = $conn->insert_id;

    // Check answers and insert responses
    foreach ($questions as $q) {
        $qid = $q['question_id'];
        $correct = $q['correct_option'];
        $user_choice = $selected[$qid] ?? '';
        $is_correct = ($user_choice === $correct) ? 1 : 0;
        if ($is_correct) $score++;

        $stmt = $conn->prepare("INSERT INTO quiz_responses (attempt_id, question_id, selected_option, is_correct) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iisi", $attempt_id, $qid, $user_choice, $is_correct);
        $stmt->execute();
    }

    // Update attempt score
    $stmt = $conn->prepare("UPDATE quiz_attempts SET score=? WHERE attempt_id=?");
    $stmt->bind_param("ii", $score, $attempt_id);
    $stmt->execute();

    // Store last score in session for display
    $_SESSION['quiz_last_score'] = $score;
    header("Location: quiz.php?done=1");
    exit();
}

// Fetch questions (limit to 5 random per attempt)
$questionsRes = $conn->query("SELECT * FROM quiz_questions ORDER BY RAND() LIMIT 5");
$questions = [];
while ($row = $questionsRes->fetch_assoc()) {
    $questions[] = $row;
}

// Fetch user's quiz history
$history = [];
$histRes = $conn->prepare("SELECT attempt_id, score, attempt_date FROM quiz_attempts WHERE username=? ORDER BY attempt_date DESC");
$histRes->bind_param("s", $username);
$histRes->execute();
$historyRes = $histRes->get_result();
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
    }

    body {
        background: var(--background-colour);
        font-family: 'Roboto', sans-serif;
        margin: 0;
        color: var(--text);
    }

    .container {
        background: var(--container-background);
        max-width: 600px;
        margin: 40px auto 20px;
        padding: 32px 24px;
        border-radius: 16px;
        box-shadow: 0 4px 16px rgba(106, 123, 162, 0.1);
    }

    .quiz-title, .history-title {
        color: var(--heading-colour);
        text-align: center;
        margin-bottom: 20px;
    }

    .quiz-form {
        margin-bottom: 32px;
    }

    .quiz-question {
        margin-bottom: 18px;
    }

    .q-num {
        font-weight: 600;
        color: var(--primary-colour);
    }

    .quiz-options label {
        display: block;
        margin: 4px 0;
        padding-left: 12px;
        cursor: pointer;
    }

    .quiz-submit-container {
        display: flex;
        justify-content: center;
        margin-top: 12px;
    }
    
    .quiz-submit-btn {
        background: var(--button-background);
        color: #fff;
        border: none;
        padding: 10px 32px;
        border-radius: 8px;
        font-size: 1rem;
        cursor: pointer;
        margin-top: 12px;
        transition: background 0.2s;
        ;
    }

    .quiz-submit-btn:hover {
        background: var(--button-hover);
    }

    .quiz-history table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 8px;
    }

    .quiz-history th, .quiz-history td {
        border: 1px solid var(--border-colour);
        padding: 8px;
        text-align: center;
    }

    .toast.success {
        background: #1d8a47;
        color: #fff;
        padding: 12px 16px;
        border-radius: 8px;
        text-align: center;
        margin-bottom: 24px;
    }
    </style>
</head>
<body>
<?php include 'nav.php'; ?>
<main>
    <div class="container">
        <h2 class="quiz-title">Mini-Quiz: Fun Facts About Hearing Impairment</h2>

        <?php if (isset($_GET['done']) && isset($_SESSION['quiz_last_score'])): ?>
            <div class="toast success">
                You scored <?= $_SESSION['quiz_last_score'] ?> out of <?= count($questions) ?>!
            </div>
            <?php unset($_SESSION['quiz_last_score']); ?>
        <?php endif; ?>

        <form method="POST" class="quiz-form">
            <?php foreach ($questions as $idx => $q): ?>
                <div class="quiz-question">
                    <span class="q-num">Q<?= $idx+1 ?>.</span> <?= htmlspecialchars($q['question_text']) ?>
                    <div class="quiz-options">
                        <label><input type="radio" name="selected[<?= $q['question_id'] ?>]" value="A" required> <?= htmlspecialchars($q['option_a']) ?></label>
                        <label><input type="radio" name="selected[<?= $q['question_id'] ?>]" value="B"> <?= htmlspecialchars($q['option_b']) ?></label>
                        <label><input type="radio" name="selected[<?= $q['question_id'] ?>]" value="C"> <?= htmlspecialchars($q['option_c']) ?></label>
                        <label><input type="radio" name="selected[<?= $q['question_id'] ?>]" value="D"> <?= htmlspecialchars($q['option_d']) ?></label>
                    </div>
                </div>
            <?php endforeach; ?>

           <div class="quiz-submit-container">
                <button type="submit" name="quiz_submit" class="quiz-submit-btn">Submit Quiz</button>
            </div>

        </form>

        <h3 class="history-title">Your Quiz History</h3>
        <div class="quiz-history">
            <?php if (count($history) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($history as $h): ?>
                            <tr>
                                <td><?= htmlspecialchars($h['attempt_date']) ?></td>
                                <td><?= htmlspecialchars($h['score']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No quiz attempts yet. Try the quiz above!</p>
            <?php endif; ?>
        </div>
    </div>
</main>
</body>
</html>