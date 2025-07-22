<?php
require 'database.php';

// Making sure the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: homepage.php?error=unauthorised");
    exit();
}

// Check if the admin is trying to download on behalf of another user
$is_admin = isset($_GET['admin']) && $_GET['admin'] == 1 && isset($_SESSION['roles']) && $_SESSION['roles'] === 'admin';

// If admin, use the username from the URL; else use the logged-in user's own username
$view_username = $is_admin ? ($_GET['user'] ?? '') : $_SESSION['username'];

// Attempt ID
$attempt_id = isset($_GET['attempt_id']) ? intval($_GET['attempt_id']) : 0;

// Cheking ig the attempt belongs to the current user
$check = $conn->prepare("SELECT * FROM quiz_attempts WHERE attempt_id=? AND username=?");
$check->bind_param("is", $attempt_id, $view_username);
$check->execute();
$result = $check->get_result();

// If the attempt does not belong to the current user, it will not allow them access
if ($result->num_rows === 0) {
    die("Invalid attempt.");
}

// Fetching attempt details
$attempt = $result->fetch_assoc();

// Getting all of hte questions and answers from the quiz attempts
$stmt = $conn->prepare("
    SELECT qq.question_text, qq.option_a, qq.option_b, qq.option_c, qq.option_d,
           qq.correct_option, qr.selected_option, qr.is_correct
    FROM quiz_responses qr
    JOIN quiz_questions qq ON qr.question_id = qq.question_id
    WHERE qr.attempt_id = ?
");

$stmt->bind_param("i", $attempt_id);
$stmt->execute();
$responses = $stmt->get_result();

// Start building content (Top of the page)
$content = "Quiz Attempt ID: {$attempt_id}\n";
$content .= "Username: {$view_username}\n";
$content .= "Date: {$attempt['attempt_date']}\n";
$content .= "Score: {$attempt['score']}\n";
$content .= "------------------------------------------\n\n";

$qNum = 1;
while ($row = $responses->fetch_assoc()) {
    // Add question number and question text
    $content .= "Q{$qNum}. " . $row['question_text'] . "\n";
    // Loop through each answer option from A to D
    foreach (['A', 'B', 'C', 'D'] as $opt) {
        // Display a >> in front of the user's selected answer
        $marker = ($row['selected_option'] == $opt) ? '>>' : '  ';
        $content .= "$marker $opt. " . $row['option_' . strtolower($opt)] . "\n";
    }
    // Displaying the user's selected answer and if it's correct or not
    $content .= "Your Answer: " . $row['selected_option'] . " (" . ($row['is_correct'] ? "Correct" : "Incorrect") . ")\n";
    
    // Show the correct answer for this option
    $content .= "Correct Answer: " . $row['correct_option'] . "\n";
    $content .= "------------------------------------------\n\n";
    $qNum++;
}

// Output as downloadable .txt file
header('Content-Type: text/plain');
header('Content-Disposition: attachment; filename="quiz_attempt_' . $attempt_id . '.txt"');
// Output the final content so it can be downloaded
echo $content;
exit();