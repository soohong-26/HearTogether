<?php
require 'database.php';

// Ensuring the the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: homepage.php?error=unauthorised");
    exit();
}

$username = $_SESSION['username'];
$attempt_id = isset($_GET['attempt_id']) ? intval($_GET['attempt_id']) : 0;

// Admin override logic
$is_admin_view = isset($_GET['admin']) && $_GET['admin'] == 1 && isset($_SESSION['roles']) && $_SESSION['roles'] === 'admin';
$view_username = $is_admin_view ? $_GET['user'] ?? '' : $username;

// Validate ownership or admin access
$check = $conn->prepare("SELECT * FROM quiz_attempts WHERE attempt_id=? AND username=?");
$check->bind_param("is", $attempt_id, $view_username);
$check->execute();
$result = $check->get_result();
if ($result->num_rows === 0) {
    // Redirecting if there is no such attempts fuond
    header("Location: " . ($is_admin_view ? "admin_quiz_scores.php?error=invalid_attempt" : "quiz_home.php?error=invalid_attempt"));
    exit();
}

// Fetch user's responses
$responsesRes = $conn->prepare("
    SELECT qq.question_text, qq.image, qq.option_a, qq.option_b, qq.option_c, qq.option_d, qq.correct_option, qr.selected_option, qr.is_correct
    FROM quiz_responses qr
    JOIN quiz_questions qq ON qr.question_id = qq.question_id
    WHERE qr.attempt_id = ?
");

// Bind and execute
$responsesRes->bind_param("i", $attempt_id);
$responsesRes->execute();
$responses = $responsesRes->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quiz Attempt Review</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            background: var(--background-colour);
            font-family: 'Roboto', sans-serif;
            color: var(--text);
            margin: 0;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            padding: 32px;
            background: var(--container-background);
            border-radius: 16px;
            box-shadow: 0 4px 16px rgba(106, 123, 162, 0.1);
        }
        h2 {
            text-align: center;
            color: var(--text);
        }
        .question {
            margin-bottom: 24px;
        }
        .question p {
            font-weight: 600;
        }
        .question-img {
            display: block;
            margin: 0 auto 12px auto;
            max-width: 160px;
            max-height: 120px;
            object-fit: contain;
            border-radius: 7px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.09);
            background: #f9f9f9;
        }
        .option {
            padding: 8px 12px;
            margin: 4px 0;
            border-radius: 6px;
        }
        .selected {
            background: #f0f8ff;
            border-left: 5px solid var(--primary-colour);
        }
        .correct {
            color: green;
            font-weight: bold;
        }
        .incorrect {
            color: red;
            font-weight: bold;
        }
        .back-btn {
            background: var(--button-background);
            color: white;
            padding: 10px 24px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
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
        <h2>Review Your Attempt</h2>
        <!-- Looping through all the quiz questions and show the user's answers -->
        <?php while ($row = $responses->fetch_assoc()): ?>
            <div class="question">

            <!-- Display image if it exists and is not the default placeholder -->
            <?php if (!empty($row['image']) && $row['image'] !== 'images/quiz_default.svg'): ?>
                    <img src="<?= htmlspecialchars($row['image']) ?>" alt="Question Image" class="question-img">
                <?php endif; ?>
                
                <!-- Displaying the question text -->
                <p><?= htmlspecialchars($row['question_text']) ?></p>
                
                <!-- Looping through all the options which are A to D -->
                <?php foreach (['A', 'B', 'C', 'D'] as $opt): ?>
                    <?php
                        $option_text = $row["option_" . strtolower($opt)];
                        $classes = 'option';
                        if ($row['selected_option'] === $opt) $classes .= ' selected';
                    ?>
                    <div class="<?= $classes ?>">
                        <?= $opt ?>. <?= htmlspecialchars($option_text) ?>
                        
                        <!-- Displaying the correct or the incorrect one depending on the correctness -->
                        <?php if ($row['selected_option'] === $opt): ?>
                            <?= $opt === $row['correct_option']
                                ? '<span class="correct"> ✔ Correct</span>'
                                : '<span class="incorrect"> ✖ Incorrect</span>' ?>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endwhile; ?>
        <?php if ($is_admin_view): ?>

        <!-- Back button -->
        <a href="admin_quiz_scores.php?user=<?= urlencode($view_username) ?>" class="back-btn">Back to Admin Scores</a>
            <?php else: ?>
                <a href="quiz_home.php" class="back-btn">Back to Quiz Home</a>
            <?php endif; ?>
        </div>
</main>
</body>
</html>