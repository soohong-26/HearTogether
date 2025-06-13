<?php
require 'database.php';

// Only admins allowed
if (!isset($_SESSION['username']) || $_SESSION['roles'] !== 'admin') {
    header("Location: homepage.php?error=unauthorised");
    exit();
}

// --- Handle Adding New Question ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_question'])) {
    $q = trim($_POST['question_text']);
    $a = trim($_POST['option_a']);
    $b = trim($_POST['option_b']);
    $c = trim($_POST['option_c']);
    $d = trim($_POST['option_d']);
    $correct = $_POST['correct_option'];

    $image_filename = null;
if (isset($_FILES['question_image']) && $_FILES['question_image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
    $fileTmpPath = $_FILES['question_image']['tmp_name'];
    $fileName = uniqid() . '_' . basename($_FILES['question_image']['name']);
    $destPath = $uploadDir . $fileName;
    $imageType = exif_imagetype($fileTmpPath);

    // Only allow gif, png, jpg, jpeg
    $allowedTypes = [IMAGETYPE_GIF, IMAGETYPE_PNG, IMAGETYPE_JPEG];
    $allowedExtensions = ['gif', 'png', 'jpg', 'jpeg'];
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    if (in_array($imageType, $allowedTypes) && in_array($fileExt, $allowedExtensions)) {
        move_uploaded_file($fileTmpPath, $destPath);
        $image_filename = $destPath;
    }
}


    if ($q && $a && $b && $c && $d && in_array($correct, ['A','B','C','D'])) {
        $stmt = $conn->prepare("INSERT INTO quiz_questions (question_text, image, option_a, option_b, option_c, option_d, correct_option) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $q, $image_filename, $a, $b, $c, $d, $correct);
        $stmt->execute();
        header("Location: admin_quiz.php?quiz_action=add_success");
        exit();
    } else {
        header("Location: admin_quiz.php?quiz_action=add_error");
        exit();
    }
}

// --- Handle Editing a Question ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_question'])) {
    $qid = $_POST['edit_question'];
    $q = trim($_POST['edit_question_text']);
    $a = trim($_POST['edit_option_a']);
    $b = trim($_POST['edit_option_b']);
    $c = trim($_POST['edit_option_c']);
    $d = trim($_POST['edit_option_d']);
    $correct = $_POST['edit_correct_option'];

    // Fetch current image filename
    $old_img_stmt = $conn->prepare("SELECT image FROM quiz_questions WHERE question_id=?");
    $old_img_stmt->bind_param("i", $qid);
    $old_img_stmt->execute();
    $old_img_res = $old_img_stmt->get_result();
    $old_img_row = $old_img_res->fetch_assoc();
    $old_image_filename = $old_img_row ? $old_img_row['image'] : null;

    // Handle image upload (allow .gif, .png, .jpg, .jpeg only)
    $image_filename = $old_image_filename;
    if (isset($_FILES['question_image']) && $_FILES['question_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $fileTmpPath = $_FILES['question_image']['tmp_name'];
        $fileName = uniqid() . '_' . basename($_FILES['question_image']['name']);
        $destPath = $uploadDir . $fileName;
        $imageType = exif_imagetype($fileTmpPath);
        $allowedTypes = [IMAGETYPE_GIF, IMAGETYPE_PNG, IMAGETYPE_JPEG];
        $allowedExtensions = ['gif', 'png', 'jpg', 'jpeg'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if (in_array($imageType, $allowedTypes) && in_array($fileExt, $allowedExtensions)) {
            move_uploaded_file($fileTmpPath, $destPath);
            $image_filename = $destPath;
        }
    }

    if ($q && $a && $b && $c && $d && in_array($correct, ['A','B','C','D'])) {
        // Now always update the image column!
        $stmt = $conn->prepare("UPDATE quiz_questions SET question_text=?, image=?, option_a=?, option_b=?, option_c=?, option_d=?, correct_option=? WHERE question_id=?");
        $stmt->bind_param("sssssssi", $q, $image_filename, $a, $b, $c, $d, $correct, $qid);
        $stmt->execute();
        header("Location: admin_quiz.php?quiz_action=edit_success");
        exit();
    } else {
        header("Location: admin_quiz.php?quiz_action=edit_error");
        exit();
    }
}

// --- Handle Deleting a Question ---
if (isset($_GET['delete'])) {
    $qid = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM quiz_questions WHERE question_id=?");
    $stmt->bind_param("i", $qid);
    $stmt->execute();
    header("Location: admin_quiz.php?quiz_action=delete_success");
    exit();
}

// --- Fetch all quiz questions ---
$qres = $conn->query("SELECT * FROM quiz_questions ORDER BY question_id ASC");
$questions = [];
while ($row = $qres->fetch_assoc()) {
    $questions[] = $row;
}

// --- Fetch all users who took the quiz ---
$users = [];
$ures = $conn->query("SELECT DISTINCT username FROM quiz_attempts ORDER BY username ASC");
while ($row = $ures->fetch_assoc()) {
    $users[] = $row['username'];
}

// --- If a user is selected, fetch their history ---
$user_history = [];
$selected_user = '';
if (isset($_GET['user'])) {
    $selected_user = $_GET['user'];
    $hstmt = $conn->prepare("SELECT score, attempt_date FROM quiz_attempts WHERE username=? ORDER BY attempt_date DESC");
    $hstmt->bind_param("s", $selected_user);
    $hstmt->execute();
    $hres = $hstmt->get_result();
    while ($row = $hres->fetch_assoc()) {
        $user_history[] = $row;
    }
}

$selected_profile_img = '';
if ($selected_user) {
    $profile_stmt = $conn->prepare("SELECT profile_img FROM users WHERE username=?");
    $profile_stmt->bind_param("s", $selected_user);
    $profile_stmt->execute();
    $profile_res = $profile_stmt->get_result();
    if ($profile_res && $profile_res->num_rows > 0) {
        $img_row = $profile_res->fetch_assoc();
        $selected_profile_img = !empty($img_row['profile_img']) ? $img_row['profile_img'] : 'icons/user.png';
    } else {
        $selected_profile_img = 'icons/user.png';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>HearTogether - Admin Quiz Manager</title>
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
    form {
        margin-bottom: 32px;
    }
    .quiz-table, .user-table {
        width: 100%;
        border-collapse: collapse;
        margin: 18px 0;
        font-size: 1rem;
    }
    .quiz-table th, .quiz-table td, .user-table th, .user-table td {
        border: 1px solid var(--border-colour);
        padding: 8px;
        text-align: left;
    }
    .quiz-table th, .user-table th {
        background: var(--primary-colour);
        color: #fff;
    }
    .action-btn, .edit-btn, .delete-btn, .save-btn {
        background: var(--button-background);
        color: #fff;
        border: none;
        padding: 5px 16px;
        border-radius: 6px;
        margin-right: 6px;
        font-size: 0.95rem;
        cursor: pointer;
        transition: background 0.2s;
        text-decoration: none;
    }
    .action-btn:hover, .edit-btn:hover, .save-btn:hover {
        background: var(--button-hover);
    }

    button.save-btn {
        all: unset; /* Reset default browser styles */
        display: inline-block;
        background: var(--button-background);
        color: #fff;
        border: none;
        padding: 5px 16px;
        border-radius: 6px;
        font-size: 0.95rem;
        cursor: pointer;
        text-align: center;
        width: 80px;
        transition: background 0.2s;
    }
    button.save-btn:hover {
        background: var(--button-hover);
    }

    .action-buttons {
        display: flex;
        flex-direction: column;
        gap: 8px;
        align-items: flex-start; 
    }

    .action-buttons button,
    .action-buttons a {
        width: 80px;
        text-align: center;
    }

    .back-link {
        display: inline-block;
        margin-top: 28px;
        margin-bottom: 28px;
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

    .delete-btn {
        background: var(--danger);
    }
    .delete-btn:hover {
        background: #dc3545;
    }
    .form-row {
        display: flex;
        gap: 12px;
        margin-bottom: 12px;
        flex-wrap: wrap;
    }
    .form-row label {
        flex: 1 1 120px;
        min-width: 120px;
        font-weight: 600;
    }
    .form-row input[type="text"], .form-row select {
        flex: 2 1 250px;
        padding: 5px 10px;
        border: 1px solid var(--border-colour);
        border-radius: 6px;
        font-size: 1rem;
    }

    input[type="file"] {
        font-size: 1rem;
        font-family: 'Roboto', sans-serif;
        padding: 5px 10px;
        border-radius: 6px;
        border: 1px solid var(--border-colour);
        background: #fff;
        color: var(--text);
        max-width: 140px;    /* << add this */
        box-sizing: border-box;
    }

    .user-select-form {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 18px 0;
        justify-content: center;
    }
    .user-table {
        margin-bottom: 30px;
    }
    .center {
        text-align: center;
    }

    .quiz-table .quiz-image {
        max-width:80px; 
        max-height:60px; 
        object-fit:contain; 
        fill: var(--heading-colour);
    }

    /* Toast styles */
    #toast {
        position: fixed;
        top: 20px;
        right: 20px;
        max-width: 320px;
        padding: 14px 20px;
        border-radius: 8px;
        font-weight: 600;
        color: white;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.5s ease;
        z-index: 9999;
    }
    #toast.success { background-color: #1d8a47; }
    #toast.error { background-color: #ff5e57; }
    </style>
</head>
<body>
<?php include 'nav.php'; ?>
<main>
    <div class="container">
        <h2>Quiz Question Manager</h2>

        <!-- Add Question Form -->
        <form method="POST" enctype="multipart/form-data" autocomplete="off">
            <h3>Add New Question</h3>
            <div class="form-row">
                <label for="question_text">Question</label>
                <input type="text" id="question_text" name="question_text" maxlength="255" required>
            </div>
            <div class="form-row">
                <label for="option_a">Option A</label>
                <input type="text" id="option_a" name="option_a" maxlength="100" required>
            </div>
            <div class="form-row">
                <label for="option_b">Option B</label>
                <input type="text" id="option_b" name="option_b" maxlength="100" required>
            </div>
            <div class="form-row">
                <label for="option_c">Option C</label>
                <input type="text" id="option_c" name="option_c" maxlength="100" required>
            </div>
            <div class="form-row">
                <label for="option_d">Option D</label>
                <input type="text" id="option_d" name="option_d" maxlength="100" required>
            </div>

            <!-- Correct Answer Option -->
            <div class="form-row">
                <label for="correct_option">Correct Answer</label>
                <select id="correct_option" name="correct_option" required>
                    <option value="">Choose...</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                </select>
            </div>

            <!-- Image Attachment -->
            <div class="form-row">
                <label for="question_image">Question Image</label>
               <input type="file" id="question_image" name="question_image" accept="image/gif, image/png, image/jpeg, image/jpg">
            </div>

            <div class="form-row center">
                <button type="submit" name="add_question" class="action-btn">Add Question</button>
            </div>
        </form>

        <hr>
        <div class="center">
            <a href="admin_quiz_scores.php" class="back-link">To View User's Scores</a>
        </div>
        <hr>

        <!-- List & Edit Quiz Questions -->
        <h3>All Questions</h3>
        <table class="quiz-table">
            <thead>
                <tr>
                    <th>Question</th>
                    <th style="width: 150px;">Image</th>
                    <th>Options</th>
                    <th>Correct</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($questions as $q): ?>
                <?php if (isset($_GET['edit']) && $_GET['edit'] == $q['question_id']): ?>
                <tr>
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="edit_question" value="<?= $q['question_id'] ?>">
                        <td>
                            <input type="text" name="edit_question_text" value="<?= htmlspecialchars($q['question_text']) ?>" required maxlength="255">
                        </td>
                        <td style="text-align:center; width: 150px;">
                            <?php if (!empty($q['image'])): ?>
                                <img src="<?= htmlspecialchars($q['image']) ?>" alt="Current Image" style="max-width:80px; max-height:60px; object-fit:contain; margin-bottom:5px;"><br>
                            <?php else: ?>
                                <img src="images/quiz_default.svg" alt="Default Image" style="max-width:80px; max-height:60px; object-fit:contain; margin-bottom:5px;"><br>
                            <?php endif; ?>
                            <input type="file" name="question_image" accept=".gif,.png,.jpg,.jpeg,image/gif,image/png,image/jpeg">
                            <br>
                            <small>Leave blank to keep existing image</small>
                        </td>
                        <td>
                            <input type="text" name="edit_option_a" value="<?= htmlspecialchars($q['option_a']) ?>" required maxlength="100" style="width:60px;"> A<br>
                            <input type="text" name="edit_option_b" value="<?= htmlspecialchars($q['option_b']) ?>" required maxlength="100" style="width:60px;"> B<br>
                            <input type="text" name="edit_option_c" value="<?= htmlspecialchars($q['option_c']) ?>" required maxlength="100" style="width:60px;"> C<br>
                            <input type="text" name="edit_option_d" value="<?= htmlspecialchars($q['option_d']) ?>" required maxlength="100" style="width:60px;"> D
                        </td>
                        <td>
                            <select name="edit_correct_option" required>
                                <option value="A" <?= $q['correct_option']=='A'?'selected':'' ?>>A</option>
                                <option value="B" <?= $q['correct_option']=='B'?'selected':'' ?>>B</option>
                                <option value="C" <?= $q['correct_option']=='C'?'selected':'' ?>>C</option>
                                <option value="D" <?= $q['correct_option']=='D'?'selected':'' ?>>D</option>
                            </select>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button type="submit" name="edit_question" value="<?= $q['question_id'] ?>" class="save-btn">Save</button>
                                <a href="admin_quiz.php" class="action-btn">Cancel</a>
                            </div>
                        </td>
                    </form>
                </tr>
                <?php else: ?>
                    <tr>
                        <td>
                            <?= htmlspecialchars($q['question_text']) ?>
                        </td>
                        <td style="text-align:center; width: 150px;">
                            <?php if (!empty($q['image'])): ?>
                                <img src="<?= htmlspecialchars($q['image']) ?>" alt="Question Image" style="max-width:80px; max-height:60px; object-fit:contain;">
                            <?php else: ?>
                                <img src="images/quiz_default.svg" alt="Default Image" class="quiz_image">
                            <?php endif; ?>
                        </td>
                        <td>
                            A. <?= htmlspecialchars($q['option_a']) ?><br>
                            B. <?= htmlspecialchars($q['option_b']) ?><br>
                            C. <?= htmlspecialchars($q['option_c']) ?><br>
                            D. <?= htmlspecialchars($q['option_d']) ?>
                        </td>
                        <td><?= $q['correct_option'] ?></td>
                        <td>
                            <div class="action-buttons">
                                <a href="admin_quiz.php?edit=<?= $q['question_id'] ?>" class="edit-btn">Edit</a>
                                <a href="admin_quiz.php?delete=<?= $q['question_id'] ?>" class="delete-btn" onclick="return confirm('Delete this question?');">Delete</a>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
            </tbody>
        </table>

        <br>
        <hr>
        
    </div>
</main>

<!-- Toast container -->
<div id="toast"></div>
<script>
    const toast = document.getElementById('toast');
    let toastTimeout;
    function showToast(message, type = 'success') {
        toast.textContent = message;
        toast.className = type;
        toast.style.opacity = '1';
        toast.style.pointerEvents = 'auto';
        clearTimeout(toastTimeout); 
        toastTimeout = setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.pointerEvents = 'none';
        }, 3500);
    }
    // Show notification based on URL query parameters
    const params = new URLSearchParams(window.location.search);
    if (params.has('quiz_action')) {
        const action = params.get('quiz_action');
        const messages = {
            'add_success': ['Question added successfully!', 'success'],
            'edit_success': ['Question updated successfully!', 'success'],
            'delete_success': ['Question deleted successfully!', 'success'],
            'add_error': ['Failed to add question. Check all fields.', 'error'],
            'edit_error': ['Failed to update question. Check all fields.', 'error']
        };
        if (messages[action]) {
            const [text, type] = messages[action];
            showToast(text, type);
        }
        params.delete('quiz_action');
        history.replaceState(null, '', window.location.pathname);
    }
</script>
</body>
</html>
