<?php
require 'database.php';

// Handle approval or decline
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'];
    $action = $_POST['action'];

    if ($action === 'approve') {
        $sql = "UPDATE users SET is_approved = 1 WHERE user_id = ?";
    } elseif ($action === 'decline') {
        $sql = "DELETE FROM users WHERE user_id = ?";
    }

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $userId);
        if ($stmt->execute()) {
            header("Location: admin_approval.php?status=" . $action);
            exit;
        }
    }
}

// Fetch users pending approval
$result = $conn->query("SELECT user_id, username, email FROM users WHERE is_approved = 0");
$pendingUsers = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - User Approvals</title>
    <style>
        body {
            background-color: #0a161a;
            color: #ecf2f4;
            font-family: 'Roboto', sans-serif;
            margin: 0;
        }

        .title {
            color: #87c9e3;
            margin-bottom: 20px;
            padding: 0 20px 0 20px;
            margin-top: 40px;
        }

        .status {
            padding: 0 20px 0 20px;
        }

        .approval-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .approval-table th,
        .approval-table td {
            padding: 12px 15px;
            border: 1px solid #87c9e3;
            text-align: left;
        }

        .approval-table th {
            background-color: #127094;
        }

        .approval-table td {
            background-color: #0f2c35;
        }

        .action-btn {
            padding: 6px 14px;
            margin-right: 6px;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
        }

        .approve {
            background-color: #29bff9;
            color: #0a161a;
        }

        .decline {
            background-color: #ff5e57;
            color: white;
        }

        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #29bff9;
            color: #0a161a;
            padding: 12px 18px;
            border-radius: 8px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            opacity: 0;
            transition: opacity 0.5s ease;
            z-index: 9999;
        }
    </style>
</head>
<body>
    <?php include 'nav.php'; ?>

    <h2 class="title">Pending User Registrations</h2>

    <?php if (count($pendingUsers) === 0): ?>
        <p class="status">No new registration requests.</p>
    <?php else: ?>
        <table class="approval-table">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pendingUsers as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                                <input type="hidden" name="action" value="approve">
                                <button class="action-btn approve" type="submit">Approve</button>
                            </form>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                                <input type="hidden" name="action" value="decline">
                                <button class="action-btn decline" type="submit">Decline</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <?php if (isset($_GET['status'])): ?>
        <div id="toast" class="toast">
            <?= $_GET['status'] === 'approve' ? 'User approved successfully!' : 'User declined and removed.' ?>
        </div>
        <script>
            const toast = document.getElementById('toast');
            if (toast) {
                setTimeout(() => toast.style.opacity = '1', 100);
                setTimeout(() => {
                    toast.style.opacity = '0';
                    const url = new URL(window.location);
                    url.searchParams.delete('status');
                    window.history.replaceState({}, document.title, url);
                }, 3000);
            }
        </script>
    <?php endif; ?>
</body>
</html>
