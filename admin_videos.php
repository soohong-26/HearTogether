<?php
require 'database.php';

// Check if user is an admin
if (!isset($_SESSION['username']) || $_SESSION['roles'] !== 'admin') {
    header("Location: homepage.php?error=unauthorised");
    exit();
}

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['gif'], $_POST['title'])) {
    $title = trim($_POST['title']);
    $newCategory = trim($_POST['new_category']);
    $selectedCategory = trim($_POST['category']);
    $category = !empty($newCategory) ? $newCategory : $selectedCategory;

    $filename = basename($_FILES['gif']['name']);
    $targetDir = 'videos/';
    $targetFile = $targetDir . $filename;

    if (!empty($title) && !empty($category) && move_uploaded_file($_FILES['gif']['tmp_name'], $targetFile)) {
        $stmt = $conn->prepare("INSERT INTO videos (filename, title, category) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $filename, $title, $category);
        $stmt->execute();
        $stmt->close();

        // Redirect with success flag
        header("Location: admin_videos.php?upload=success");
        exit();
    } else {
        // Redirect with failure flag
        header("Location: admin_videos.php?upload=fail");
        exit();
    }
}

// Handle edit title (AJAX POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_video_id'], $_POST['edit_title'])) {
    $videoId = (int)$_POST['edit_video_id'];
    $newTitle = trim($_POST['edit_title']);
    if ($newTitle !== '') {
        $stmt = $conn->prepare("UPDATE videos SET title = ? WHERE video_id = ?");
        $stmt->bind_param("si", $newTitle, $videoId);
        $stmt->execute();
        $stmt->close();
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Title cannot be empty.']);
    }
    exit();
}

// Handle delete (only if 'delete' is numeric)
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    // Get filename to delete physical file
    $stmt = $conn->prepare("SELECT filename FROM videos WHERE video_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($filename);
    if ($stmt->fetch() && file_exists('videos/' . $filename)) {
        unlink('videos/' . $filename);
    }
    $stmt->close();

    // Delete from database
    $stmt = $conn->prepare("DELETE FROM videos WHERE video_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: admin_videos.php?delete=success");
    exit();
}

// Fetch categories
$categories = $conn->query("SELECT DISTINCT category FROM videos ORDER BY category ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>HearTogether - Admin Videos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;600&display=swap');

        :root {
            /* Primary Colours */
            --primary-colour: #6A7BA2;
            --primary-hover: #5C728A;

            /* Backgrounds */
            --background-colour:rgb(211, 229, 255);
            --container-background: #ffffff;
            --input-background: #ffffff;

            /* Text Colours */
            --text: #333333;
            --placeholder-colour: #999999;
            --heading-colour: #2C3E50;

            /* Borders & Lines */
            --border-colour: #cccccc;
            --focus-border-colour: #738678;

            /* Buttons */
            --button-background: var(--primary-colour);
            --button-hover: var(--primary-hover);
            --button-text: #ffffff;

            /* Links */
            --link-colour: #1a73e8;
            --link-hover: #1558b0;

            /* Toast */
            --toast-success-bg: #1d8a47;
            --toast-error-bg: #ff5e57;

            /* Misc */
            --box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            --border-radius: 8px;
            --transition-speed: 0.3s;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--background-colour);
            margin: 0;
            padding: 0;
            overflow-y: scroll;
            -ms-overflow-style: none;
            scrollbar-width: none;
            color: var(--text);
        }

        body::-webkit-scrollbar {
            width: 0;
            background: transparent;
        }

        .video-section {
            padding: 0 30px;
        }

        .section-title {
            font-size: 22px;
            font-weight: bold;
            margin: 40px 0 20px;
            color: var(--heading-colour);
        }

        .video-grid {
            display: flex;
            overflow-x: auto;
            gap: 20px;
            padding-bottom: 10px;
            scroll-snap-type: x mandatory;
        }

        .video-card {
            min-width: 320px;
            flex: 0 0 auto;
            background-color: var(--container-background);
            border-radius: 10px;
            box-shadow: var(--box-shadow);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            scroll-snap-align: start;
        }

        .video-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f9f9f9;
            width: 100%;
            height: 200px;
            overflow: hidden;
        }

        .video-wrapper img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            display: block;
        }

        .video-title {
            font-size: 14px;
            padding: 10px;
            color: var(--heading-colour);
            text-align: center;
            background-color: #f5f7fa;
            font-weight: 500;
            cursor: pointer;
            user-select: none;
            transition: background-color var(--transition-speed);
        }

        .video-title[contenteditable="true"] {
            outline: 2px solid var(--primary-colour);
            background-color: #e8f4fb;
        }

        .admin-controls {
            text-align: center;
            background-color: #f5f7fa;
            padding: 10px;
        }

        .admin-controls a {
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
            color: #ff5e57;
            margin: 0 10px;
            transition: color var(--transition-speed);
        }
        .admin-controls a:hover {
            color: var(--primary-colour);
        }

        .upload-form {
            background-color: var(--primary-colour);
            padding: 20px;
            margin: 20px 0;
            border-radius: 10px;
            color: var(--button-text);
        }

        .upload-form label {
            font-weight: bold;
            color: var(--button-text);
        }

        .upload-form input,
        .upload-form select,
        .upload-form button {
            margin-top: 8px;
            width: 100%;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid var(--border-colour);
            font-size: 14px;
            background-color: var(--input-background);
            color: var(--text);
        }

        .upload-form input[type="file"] {
            background-color: var(--container-background);
        }

        .upload-form button {
            background-color: var(--button-background);
            color: var(--button-text);
            border: none;
            font-weight: bold;
            cursor: pointer;
            transition: background-color var(--transition-speed);
            margin-top: 18px;
        }

        .upload-form button:hover {
            background-color: var(--button-hover);
        }

        .upload-form input::placeholder {
            font-size: 14px;
            color: var(--placeholder-colour);
        }

        /* Toast styles */
        #toast {
            position: fixed;
            top: 20px;
            right: 20px;
            max-width: 320px;
            padding: 14px 20px;
            border-radius: var(--border-radius);
            font-weight: 600;
            color: white;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.5s ease;
            z-index: 9999;
        }

        #toast.success {
            background-color: var(--toast-success-bg);
        }

        #toast.error {
            background-color: var(--toast-error-bg);
        }

        input[type="text"],
        input[type="file"],
        select {
            height: 45px;
            font-size: 16px;
            padding: 10px 15px;
            border-radius: 5px;
            width: 100%;
            box-sizing: border-box;
            border: 1px solid var(--border-colour);
        }

        ::placeholder {
            font-size: 16px;
            color: var(--placeholder-colour);
        }
    </style>
</head>
<body>
<?php include 'nav.php'; ?>

<main class="video-section">
    <h2 class="section-title">Admin Video Management</h2>

    <div class="upload-form">
        <form method="POST" enctype="multipart/form-data">
            <label for="title">GIF Title</label>
            <input type="text" name="title" id="title" placeholder="Enter GIF Title" required>

            <br><br>

            <label for="category">Select Existing Category</label>
            <select name="category" id="category" onchange="document.getElementById('new_category').disabled = !!this.value;">
                <option value="">-- None --</option>
                <?php while ($cat = $categories->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($cat['category']); ?>">
                        <?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $cat['category']))); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <br><br>

            <label for="new_category">Or Add New Category</label>
            <input type="text" name="new_category" id="new_category" placeholder="e.g., Numbers, Greetings">

            <br><br>

            <label for="gif">Upload GIF File</label>
            <input type="file" name="gif" id="gif" accept=".gif, .png, .jpeg, .jpg, image/gif, image/png, image/jpeg" required>

            <br>
            <button type="submit">Upload GIF</button>
        </form>
    </div>

    <?php
    // Re-fetch categories for display (since previous loop exhausted it)
    $categoryResult = $conn->query("SELECT DISTINCT category FROM videos ORDER BY category ASC");
    while ($cat = $categoryResult->fetch_assoc()):
        $catName = $cat['category'];
        $videoSet = $conn->query("SELECT * FROM videos WHERE category = '" . $conn->real_escape_string($catName) . "' ORDER BY video_id DESC");
    ?>
        <h2 class="section-title"><?php echo ucwords(str_replace('_', ' ', htmlspecialchars($catName))); ?></h2>
        <div class="video-grid">
            <?php while ($video = $videoSet->fetch_assoc()): ?>
                <div class="video-card">
                    <div class="video-wrapper">
                        <img src="videos/<?php echo htmlspecialchars($video['filename']); ?>" alt="<?php echo htmlspecialchars($video['title']); ?>">
                    </div>
                    <div class="video-title" contenteditable="true" 
                         data-video-id="<?php echo $video['video_id']; ?>"
                         title="Click to edit title. Press Enter to save, Esc to cancel."><?php echo htmlspecialchars($video['title']); ?></div>
                    <div class="admin-controls">
                        <a href="admin_videos.php?delete=<?php echo $video['video_id']; ?>" onclick="return confirm('Delete this GIF?')">Delete</a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endwhile; ?>
</main>

<!-- Toast container -->
<div id="toast"></div>

<script>
    // Editable title save logic
    document.querySelectorAll('.video-title').forEach(div => {
        div.addEventListener('keydown', e => {
            if (e.key === 'Enter') {
                e.preventDefault();
                const newTitle = e.target.innerText.trim();
                const videoId = e.target.getAttribute('data-video-id');

                if (newTitle === '') {
                    showToast('Title cannot be empty.', 'error');
                    return;
                }

                // Save via fetch POST
                fetch('admin_videos.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `edit_video_id=${videoId}&edit_title=${encodeURIComponent(newTitle)}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('Title updated successfully!', 'success');
                        e.target.blur();
                    } else {
                        showToast(data.message || 'Failed to update title.', 'error');
                    }
                })
                .catch(() => {
                    showToast('Error updating title.', 'error');
                });
            } else if (e.key === 'Escape') {
                e.preventDefault();
                // Revert changes by reloading page (simplest way)
                window.location.reload();
            }
        });
    });

    // Toast helper
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

    if (params.has('upload')) {
        if (params.get('upload') === 'success') {
            showToast('GIF uploaded successfully!', 'success');
        } else if (params.get('upload') === 'fail') {
            showToast('Failed to upload GIF.', 'error');
        }
        params.delete('upload');
        history.replaceState(null, '', window.location.pathname);
    }

    if (params.has('delete') && params.get('delete') === 'success') {
        showToast('GIF deleted successfully!', 'success');
        params.delete('delete');
        history.replaceState(null, '', window.location.pathname);
    }

    if (params.has('error') && params.get('error') === 'unauthorised') {
        showToast('Unauthorized access. Please log in.', 'error');
        params.delete('error');
        history.replaceState(null, '', window.location.pathname);
    }
</script>

</body>
</html>