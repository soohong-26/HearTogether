<?php

require 'database.php';

// Check if user is an admin
if (!isset($_SESSION['username']) || $_SESSION['roles'] !== 'admin') {
    header("Location: homepage.php?error=unauthorised");
    exit();
}

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['gif'])) {
    $title = $_POST['title'];
    $category = $_POST['category'];
    $filename = basename($_FILES['gif']['name']);
    $targetDir = 'videos/';
    $targetFile = $targetDir . $filename;

    if (move_uploaded_file($_FILES['gif']['tmp_name'], $targetFile)) {
        $stmt = $conn->prepare("INSERT INTO videos (filename, title, category) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $filename, $title, $category);
        $stmt->execute();
        $stmt->close();
        $uploadMsg = "GIF uploaded successfully!";
    } else {
        $uploadMsg = "Failed to upload file.";
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("SELECT filename FROM videos WHERE video_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($filename);
    if ($stmt->fetch()) {
        unlink('videos/' . $filename);
    }
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM videos WHERE video_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: admin_videos.php");
    exit();
}

$result = $conn->query("SELECT * FROM videos ORDER BY video_id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>HearTogether - Admin Videos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;600&display=swap');

        :root {
            --text: #ecf2f4;
            --background: #0a161a;
            --primary: #87c9e3;
            --secondary: #127094;
            --accent: #29bff9;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--background);
            margin: 0;
            padding: 0;
            overflow-y: scroll;
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        body::-webkit-scrollbar {
            width: 0;
            background: transparent;
        }

        .video-section {
            padding: 40px;
        }

        .section-title {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 20px;
            color: var(--text);
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
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            scroll-snap-align: start;
        }

        .video-wrapper {
            aspect-ratio: 16 / 9;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f9f9f9;
        }

        .video-wrapper img {
            width: 100%;
            height: auto;
        }

        .video-title {
            font-size: 14px;
            padding: 10px;
            color: var(--background);
            text-align: center;
            background-color: #f5f5f5;
            font-weight: 500;
        }

        .admin-controls {
            text-align: center;
            background-color: #f5f5f5;
            padding: 10px;
        }

        .admin-controls a {
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
            color: red;
            margin: 0 10px;
        }

        .upload-form {
            background-color: var(--secondary);
            padding: 20px;
            margin: 20px;
            border-radius: 10px;
            color: white;
        }

        .upload-form input, .upload-form select, .upload-form button {
            margin-top: 10px;
            width: 100%;
            padding: 10px;
            border-radius: 6px;
            border: none;
        }

        .success {
            background-color: #1d8a47;
            color: white;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
<?php include 'nav.php'; ?>

<main class="video-section">
    <h2 class="section-title">Admin Video Management</h2>

    <?php if (isset($uploadMsg)) echo "<div class='success'>$uploadMsg</div>"; ?>

    <div class="upload-form">
        <form method="POST" enctype="multipart/form-data">
            <label for="title">GIF Title</label>
            <input type="text" name="title" id="title" required>

            <label for="category">Category</label>
            <select name="category" id="category" required>
                <option value="common_phrases">Common Phrases</option>
                <option value="basic_conversations">Basic Conversations</option>
            </select>

            <label for="gif">Upload GIF</label>
            <input type="file" name="gif" id="gif" accept=".gif" required>

            <button type="submit">Upload GIF</button>
        </form>
    </div>

    <div class="video-grid">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="video-card">
                <div class="video-wrapper">
                    <img src="videos/<?php echo htmlspecialchars($row['filename']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">
                </div>
                <div class="video-title"><?php echo htmlspecialchars($row['title']); ?></div>
                <div class="admin-controls">
                    <a href="admin_videos.php?delete=<?php echo $row['video_id']; ?>" onclick="return confirm('Delete this GIF?')">Delete</a>
                    <!-- You can add Edit link here -->
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</main>
</body>
</html>
