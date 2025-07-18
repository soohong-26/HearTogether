<?php
require 'database.php';

if (!isset($_SESSION['username']) || $_SESSION['roles'] !== 'admin') {
    header("Location: homepage.php?error=unauthorised");
    exit();
}

// Ensure all categories in videos table are in video_categories table
$conn->query("INSERT IGNORE INTO video_categories (name) SELECT DISTINCT category FROM videos");

// Handle category group reordering
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category_order'])) {
    $order = explode(',', $_POST['category_order']);
    foreach ($order as $index => $name) {
        $stmt = $conn->prepare("UPDATE video_categories SET category_order=? WHERE name=?");
        $stmt->bind_param("is", $index, $name);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: admin_videos.php?reorder=success");
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
        // Ensure the new category is in video_categories
        $stmt = $conn->prepare("INSERT IGNORE INTO video_categories (name) VALUES (?)");
        $stmt->bind_param("s", $category);
        $stmt->execute();
        $stmt->close();

        $stmt = $conn->prepare("INSERT INTO videos (filename, title, category) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $filename, $title, $category);
        $stmt->execute();
        $stmt->close();

        header("Location: admin_videos.php?upload=success");
        exit();
    } else {
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

    // **Clean up empty categories**
    $conn->query("DELETE FROM video_categories WHERE name NOT IN (SELECT DISTINCT category FROM videos)");

    header("Location: admin_videos.php?delete=success");
    exit();
}

// Fetch ordered categories
$cat_res = $conn->query("SELECT name FROM video_categories ORDER BY category_order ASC, name ASC");
$categories = [];
while ($cat = $cat_res->fetch_assoc()) {
    $categories[] = $cat['name'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>HearTogether - Admin Videos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;600&display=swap');

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
            padding-bottom: 30px;
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
            margin: 5px 10px;
            color: var(--heading-colour);
            text-align: center;
            background-color: #f5f7fa;
            font-weight: 500;
            cursor: pointer;
            user-select: none;
            transition: background-color var(--transition-speed);
            border-right: none;
            border-radius: 5px;
        }

        .video-title[contenteditable="true"] {
            outline: 2px solid var(--primary-colour);
            background-color: #e8f4fb;
        }

        .video-title-row {
            display: flex;
            align-items: stretch;
            overflow: hidden;
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

        .category-order-list {
            background: var(--container-background);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 20px;
            margin-bottom: 40px;
            max-width: 500px;
        }

        #categoryList {
            list-style: none;
            padding: 0;
            margin: 0 0 20px 0;
        }

        #categoryList li {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
            padding: 10px;
            background: #f3f6fa;
            border-radius: 6px;
        }

        #categoryList button {
            margin-left: 8px;
            background: var(--primary-colour);
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 4px 8px;
            cursor: pointer;
        }
        #categoryList button:disabled {
            background: #ddd;
            color: #aaa;
            cursor: not-allowed;
        }

        .save-order-btn {
            background: var(--button-background);
            color: var(--button-text);
            font-weight: bold;
            border: none;
            border-radius: 6px;
            padding: 10px 22px;
            font-size: 15px;
            cursor: pointer;
            transition: background var(--transition-speed);
            margin-top: 10px;
            box-shadow: 0 2px 6px rgba(60,200,255,0.08);
        }
        .save-order-btn:hover {
            background: var(--button-hover);
        }

        .delete-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 8px 12px;
            margin-right: 5px;
            background: var(--toast-error-bg);
            color: var(--button-text);
            border: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            box-shadow: 0 2px 6px rgba(60,200,255,0.08);
            transition: background 0.2s, transform 0.1s;
            position: relative;
            min-width: 36px;
            min-height: 36px;
        }
        .delete-btn:hover {
            background: #e84840;
            transform: translateY(-2px) scale(1.05);
        }
        .delete-btn .delete-icon {
            width: 18px;
            height: 18px;
            display: block;
            pointer-events: none;
        }
        .confirm-btn {
            display: none;
            padding: 8px 12px;
            background: var(--primary-colour);
            color: var(--button-text);
            border: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 15px;
            margin-left: 8px;
            cursor: pointer;
            box-shadow: 0 2px 6px rgba(60,200,255,0.08);
            transition: background 0.2s, transform 0.1s;
            position: relative;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            min-height: 36px;
        }

        .confirm-btn:hover {
            background: var(--primary-hover);
            transform: translateY(-2px) scale(1.05);
        }

        .confirm-btn .confirm-icon {
            width: 18px;
            height: 18px;
            display: block;
            pointer-events: none;
        }
    </style>
</head>
<body>
<?php include 'nav.php'; ?>

<!-- Main Content -->
<main class="video-section">
    <h2 class="section-title">Admin Video Management</h2>
    
    <!-- Upload Form -->
    <div class="upload-form">
        <form method="POST" enctype="multipart/form-data">
            <label for="title">Title</label>
            <!-- Input for entering the video title -->
            <input type="text" name="title" id="title" placeholder="Enter Title" required>

            <br><br>

            <!-- Dropdownn to choose an existing category from the database -->
            <label for="category">Select Existing Category</label>
            <select name="category" id="category" onchange="document.getElementById('new_category').disabled = !!this.value;">
                <option value="">-- None --</option>
                <!-- Dynamically populate the list of categories -->
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo htmlspecialchars($cat); ?>">
                        <?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $cat))); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <br><br>

            <!-- Field to optionally create a new category instead of choosing existing -->
            <label for="new_category">Or Add New Category</label>
            <input type="text" name="new_category" id="new_category" placeholder="e.g., Numbers, Greetings">

            <br><br>

            <!-- File input field for uploading the GIF file -->
            <label for="gif">Upload File</label>
            <input type="file" name="gif" id="gif" accept=".gif, .png, .jpeg, .jpg, image/gif, image/png, image/jpeg" required>

            <br>
            <!-- Submit upload button -->
            <button type="submit">Upload</button>
        </form>
    </div>

    <!-- Category Group Order (Moves all videos in that category as a block) -->
    <div class="category-order-list">
        <h3>Organise Category Groups</h3>
        <form method="post" id="reorderForm" style="margin-bottom:30px;">
            <!-- List of categories -->
            <ul id="categoryList" style="padding-left:0;">
                <?php foreach ($categories as $i => $cat): ?>
                    <li data-name="<?= htmlspecialchars($cat) ?>" style="list-style:none;display:flex;align-items:center;gap:8px;margin-bottom:8px;">
                        <span style="flex:1;"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $cat))); ?></span>
                        <!-- Disable up button for the first time -->
                        <button type="button" class="move-up" <?= $i==0?'disabled':'' ?>>&#8593;</button>
                        <!-- Disable the down button for the last time -->
                        <button type="button" class="move-down" <?= $i==count($categories)-1?'disabled':'' ?>>&#8595;</button>
                    </li>
                <?php endforeach; ?>
            </ul>
            <!-- Hidden input to submit the final category order -->
            <input type="hidden" name="category_order" id="categoryOrderInput">
            <button type="submit" class="save-order-btn">Save Order</button>
        </form>
    </div>

    <?php
    // Fetch videos grouped by ordered categories
    foreach ($categories as $catName):
        $videoSet = $conn->query("SELECT * FROM videos WHERE category = '" . $conn->real_escape_string($catName) . "' ORDER BY video_id DESC");
        if ($videoSet->num_rows > 0):
    ?>
        <!-- Category section title -->
        <h2 class="section-title"><?php echo ucwords(str_replace('_', ' ', htmlspecialchars($catName))); ?></h2>
        
        <!-- Horizontal scrollable list of video cards -->
        <div class="video-grid">
            <?php while ($video = $videoSet->fetch_assoc()): ?>
                <div class="video-card">

                    <!-- Preview box for the uploaded video file -->
                    <div class="video-wrapper">
                        <img src="videos/<?php echo htmlspecialchars($video['filename']); ?>" alt="<?php echo htmlspecialchars($video['title']); ?>">
                    </div>

                    <!-- Editable title with confirm and delete buttons -->
                    <div class="video-title-row" style="display:flex;align-items:center;gap:8px;">
                        <div class="video-title"
                            contenteditable="true"
                            data-video-id="<?php echo $video['video_id']; ?>"
                            title="Click to edit title. Press Enter to save, Esc to cancel."
                            style="flex:1;min-width:0;">
                            <?php echo htmlspecialchars($video['title']); ?>
                        </div>

                        <!-- Save title button (only appears when title field is edited) -->
                        <button class="confirm-btn" style="display:none;" data-field="title" title="Confirm">
                            <img src="icons/save_black.svg" alt="Confirm" class="confirm-icon">
                        </button>

                        <!-- Delete video form -->
                        <form method="get" style="margin:0;display:inline;">
                            <input type="hidden" name="delete" value="<?= $video['video_id'] ?>">
                            
                            <!-- Delete video button -->
                            <button type="submit" class="delete-btn" title="Delete" onclick="return confirm('Delete the video?')">
                                <img src="icons/delete_black.svg" alt="Delete" class="delete-icon">
                            </button>
                        </form>
                    </div>

                </div>
            <?php endwhile; ?>
        </div>
    <?php
        endif;
    endforeach;
    ?>
</main>

<!-- Toast container -->
<div id="toast"></div>

<script>
    // Category form logic
    document.getElementById('new_category').addEventListener('input', function() {
        document.getElementById('category').disabled = !!this.value;
    });

    document.getElementById('category').addEventListener('change', function() {
        document.getElementById('new_category').disabled = !!this.value;
    });

    // Category move up/down logic
    function updateMoveButtons() {
        const lis = document.querySelectorAll('#categoryList li');
        lis.forEach((li, i) => {
            li.querySelector('.move-up').disabled = i === 0;
            li.querySelector('.move-down').disabled = i === lis.length - 1;
        });
    }
    updateMoveButtons();

    // For each move button
    document.querySelectorAll('.move-up, .move-down').forEach(btn => {
        btn.addEventListener('click', function() {
            const li = this.closest('li');
            if (this.classList.contains('move-up') && li.previousElementSibling) {
                li.parentNode.insertBefore(li, li.previousElementSibling);
            }
            if (this.classList.contains('move-down') && li.nextElementSibling) {
                li.parentNode.insertBefore(li.nextElementSibling, li);
            }
            updateMoveButtons();
        });
    });

    // For the reorder form
    document.getElementById('reorderForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const order = Array.from(document.querySelectorAll('#categoryList li')).map(li => li.dataset.name);
        document.getElementById('categoryOrderInput').value = order.join(',');
        this.submit();
    });

    // For each video title row
    document.querySelectorAll('.video-title-row').forEach(row => {
        const titleDiv = row.querySelector('.video-title');
        const confirmBtn = row.querySelector('.confirm-btn');
        let originalTitle = titleDiv.innerText.trim();

        // Show confirm button when title changes
        titleDiv.addEventListener('input', function() {
            confirmBtn.style.display = (titleDiv.innerText.trim() !== originalTitle) ? 'inline-block' : 'none';
        });

        // Handle Enter/Esc keyboard events
        titleDiv.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                confirmBtn.click();
            } else if (e.key === 'Escape') {
                e.preventDefault();
                titleDiv.innerText = originalTitle;
                confirmBtn.style.display = 'none';
            }
        });

    // Confirm button click = save AJAX
    confirmBtn.addEventListener('click', function(e) {
        e.preventDefault();
        const newTitle = titleDiv.innerText.trim();
        const videoId = titleDiv.getAttribute('data-video-id');
        if (newTitle === '') {
            showToast('Title cannot be empty.', 'error');
            return;
        }
        fetch('admin_videos.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `edit_video_id=${videoId}&edit_title=${encodeURIComponent(newTitle)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Title updated successfully!', 'success');
                originalTitle = newTitle;
                confirmBtn.style.display = 'none';
                titleDiv.blur();
            } else {
                showToast(data.message || 'Failed to update title.', 'error');
            }
        })
        .catch(() => {
            showToast('Error updating title.', 'error');
        });
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
    // Video upload
    if (params.has('upload')) {
        if (params.get('upload') === 'success') {
            showToast('Video uploaded successfully!', 'success');
        } else if (params.get('upload') === 'fail') {
            showToast('Failed to upload Video.', 'error');
        }
        params.delete('upload');
        history.replaceState(null, '', window.location.pathname);
    }
    // Video delete
    if (params.has('delete') && params.get('delete') === 'success') {
        showToast('Video deleted successfully!', 'success');
        params.delete('delete');
        history.replaceState(null, '', window.location.pathname);
    }
    // Category reorder
    if (params.has('reorder') && params.get('reorder') === 'success') {
        showToast('Category order updated!', 'success');
        params.delete('reorder');
        history.replaceState(null, '', window.location.pathname);
    }
    // Unauthorised
    if (params.has('error') && params.get('error') === 'unauthorised') {
        showToast('Unauthorized access. Please log in.', 'error');
        params.delete('error');
        history.replaceState(null, '', window.location.pathname);
    }
</script>

</body>
</html>
