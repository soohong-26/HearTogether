<?php
require 'database.php';

if (!isset($_SESSION['username'])) {
    header("Location: homepage.php?error=unauthorised");
    exit();
}

$username = $_SESSION['username'];

$questionsRes = $conn->query("SELECT * FROM quiz_questions ORDER BY RAND() LIMIT 5");
$questions = [];
while ($row = $questionsRes->fetch_assoc()) {
    $questions[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Take Quiz</title>
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
            margin-bottom: 20px;
        }
        .question p {
            font-weight: 600;
        }
        label {
            display: block;
            margin-bottom: 5px;
            padding-left: 10px;
        }
        .submit-btn {
            background: var(--button-background);
            color: white;
            padding: 10px 24px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
            display: block;
            margin: 0 auto;
        }
        .submit-btn:hover {
            background: var(--button-hover);
        }
    </style>
</head>
<body>
<?php include 'nav.php'; ?>
<main>
    <div class="container">
        <h2>Quiz Time!</h2>
        <form action="quiz_complete.php" method="post">
            <?php foreach ($questions as $i => $q): ?>
                <div class="question">
                    <p>Q<?= $i + 1 ?>. <?= htmlspecialchars($q['question_text']) ?></p>
                    <?php foreach (['A', 'B', 'C', 'D'] as $opt): ?>
                        <label>
                            <input type="radio" name="selected[<?= $q['question_id'] ?>]" value="<?= $opt ?>" required>
                            <?= htmlspecialchars($q["option_" . strtolower($opt)]) ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
            <button type="submit" name="quiz_submit" class="submit-btn">Submit Quiz</button>
        </form>
    </div>
</main>
</body>
</html>