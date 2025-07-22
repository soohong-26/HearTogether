<?php
require 'database.php';

// Only admins can view this page
if (!isset($_SESSION['username']) || $_SESSION['roles'] !== 'admin') {
    header("Location: homepage.php?error=unauthorised");
    exit();
}

$msg = "";

// Handle edit (update email or role)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user'])) {
    $user_id = intval($_POST['user_id']);
    $email = trim($_POST['email']);
    $roles = trim($_POST['roles']);

    // Only allow "admin" or "user" roles for safety
    if (!in_array($roles, ['admin', 'user'])) $roles = 'user';

    // Check if user is an admin and is trying to be demoted
    $getOldRole = $conn->prepare("SELECT roles FROM users WHERE user_id = ?");
    $getOldRole->bind_param("i", $user_id);
    $getOldRole->execute();
    $oldRoleResult = $getOldRole->get_result();
    $oldRoleRow = $oldRoleResult->fetch_assoc();
    $oldRole = $oldRoleRow['roles'] ?? 'user';
    $getOldRole->close();

    if ($oldRole === 'admin' && $roles === 'user') {
        // Count how many admins remain
        $adminCountRes = $conn->query("SELECT COUNT(*) AS admin_count FROM users WHERE roles = 'admin'");
        $adminCount = $adminCountRes->fetch_assoc()['admin_count'];
        if ($adminCount <= 1) {
            $msg = "There must be at least one admin account!";
            // Don't allow the update
        } else {
            // Safe to demote, perform update
            $stmt = $conn->prepare("UPDATE users SET email = ?, roles = ? WHERE user_id = ?");
            $stmt->bind_param("ssi", $email, $roles, $user_id);
            if ($stmt->execute()) {
                $msg = "User updated successfully!";
                if ($user_id == $_SESSION['user_id']) {
                    session_destroy();
                    header("Location: login.php?msg=Your role was changed. Please log in again.");
                    exit();
                }
            } else {
                $msg = "Failed to update user.";
            }
            $stmt->close();
        }
    } else {
        // Not demoting the last admin, safe to update
        $stmt = $conn->prepare("UPDATE users SET email = ?, roles = ? WHERE user_id = ?");
        $stmt->bind_param("ssi", $email, $roles, $user_id);
        if ($stmt->execute()) {
            $msg = "User updated successfully!";
            if ($user_id == $_SESSION['user_id']) {
                session_destroy();
                header("Location: login.php?msg=Your role was changed. Please log in again.");
                exit();
            }
        } else {
            $msg = "Failed to update user.";
        }
        $stmt->close();
    }
}

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    $user_id = intval($_POST['user_id']);
    // Prevent self-deletion
    if ($user_id != $_SESSION['user_id']) {
        $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            $msg = "User deleted successfully!";
        } else {
            $msg = "Failed to delete user.";
        }
        $stmt->close();
    } else {
        $msg = "You cannot delete your own account!";
    }
}

// Fetch all users (columns based on your structure)
$sql = "SELECT user_id, username, email, profile_img, roles, is_approved FROM users ORDER BY user_id DESC";
$result = $conn->query($sql);

$users = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

// Count approved and pending users for the pie chart
$statsRes = $conn->query("SELECT is_approved, COUNT(*) as count FROM users GROUP BY is_approved");

$approvedCount = 0;
$pendingCount = 0;

while ($row = $statsRes->fetch_assoc()) {
    if ($row['is_approved'] == 1) {
        $approvedCount = $row['count'];
    } else {
        $pendingCount = $row['count'];
    }
}

// New users this month statistics
$currentMonthStart = date('Y-m-01 00:00:00');
$nextMonthStart    = date('Y-m-01 00:00:00', strtotime('+1 month'));

// count how many users registered this calendar month
$monthStmt = $conn->prepare("
    SELECT COUNT(*) AS month_count
    FROM users
    WHERE created_at >= ? AND created_at < ?
");
$monthStmt->bind_param("ss", $currentMonthStart, $nextMonthStart);
$monthStmt->execute();
$monthUsersCount = $monthStmt->get_result()->fetch_assoc()['month_count'] ?? 0;
$monthStmt->close();

// how many users are NOT new this month
$totalUsers        = count($users); // we already built $users[] above
$existingUsersLong = $totalUsers - $monthUsersCount;

// Get user registration counts for the past 6 months
$growthData = [];
$labels = [];
$currentDate = new DateTime();

// Loop through last 6 months
for ($i = 5; $i >= 0; $i--) {
    // Formatting the current loop month as "YYYY-MM"
    $month = $currentDate->format('Y-m');
    // Define the start o the month
    $start = $month . '-01 00:00:00';
    // Move the $currentDate one month forward to get the end boundary of that current month
    $end = $currentDate->modify('+1 month')->format('Y-m-01 00:00:00');

    // Prepare SQL to count users created between the start and the end of that month
    $stmt = $conn->prepare("
        SELECT COUNT(*) as count 
        FROM users 
        WHERE created_at >= ? AND created_at < ?
    ");

    // Bind parameters which is the start and the end of the month
    $stmt->bind_param("ss", $start, $end);
    $stmt->execute();

    // Fetching the results 
    $res = $stmt->get_result();
    $count = $res->fetch_assoc()['count'] ?? 0;
    $stmt->close();

    // Store label and count
    $labels[] = date('M Y', strtotime($month)); // e.g. "Jul 2025"
    $growthData[] = $count;

    // Move back a month for the loop, because ealier 1 month has been added with the (modify('+1 month), and now subtracting 2 to end up with 1 month behind the original one)
    $currentDate->modify('-2 month');
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - User Overview</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Consistent minimal theme -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;600&display=swap');
        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--background-colour);
            margin: 0;
            padding: 0;
        }
        main {
            max-width: 900px;
            margin: 40px auto;
            background: var(--container-background);
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(106, 123, 162, 0.08);
            padding: 32px;
        }
        h2 {
            color: var(--heading-colour);
            font-weight: 600;
            margin-top: 0;
            margin-bottom: 24px;
        }
        .user-search-box {
            width: 100%;
            margin-bottom: 20px;
        }
        .user-search-box input {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border-colour);
            border-radius: 8px;
            font-size: 1rem;
            background: var(--input-background);
        }
        .user-table {
            width: 100%;
            border-collapse: collapse;
        }
        .user-table th, .user-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid var(--border-colour);
        }
        .user-table th {
            background: var(--background-colour);
            color: var(--heading-colour);
            font-weight: 600;
        }
        .user-table tr:last-child td {
            border-bottom: none;
        }
        .profile-img {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--border-colour);
            background: #f2f2f2;
        }
        .is-approved {
            font-weight: 600;
            padding: 5px 12px;
            border-radius: 12px;
            display: inline-block;
        }
        .approved { background: #c6f7d0; color: #207441;}
        .pending { background: #fff4be; color: #ad8a00;}

        .admin-actions {
            display: flex;
            gap: 4px;
        }
        .btn-minimal {
            background: var(--primary-colour);
            color: #fff;
            padding: 7px 16px;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.18s;
            margin-right: 2px;
        }
        .btn-minimal:active,
        .btn-minimal:hover {
            background: var(--primary-hover);
        }
        .btn-delete {
            background: var(--danger);
            color: #fff;
            padding: 7px 12px;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.18s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .btn-delete:active,
        .btn-delete:hover {
            background: #c62828;
        }
        
        .action-icon, .delete-icon {
            width: 20px;
            height: 20px;
            vertical-align: middle;
            display: inline-block;
        }
        .edit-fields input, .edit-fields select {
            border: 1px solid var(--border-colour);
            background: var(--input-background);
            border-radius: 7px;
            padding: 5px 7px;
            font-size: 1rem;
            min-width: 120px;
        }
        /* Toast styles */
        .toast {
            position: fixed;
            top: 32px;
            right: 32px;
            z-index: 9999;
            min-width: 240px;
            max-width: 350px;
            padding: 18px 26px;
            border-radius: 10px;
            box-shadow: 0 6px 32px rgba(106, 123, 162, 0.14);
            font-weight: 500;
            font-size: 1rem;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.25s, top 0.4s;
            color: #fff;
            border-left: 7px solid transparent;
            background: #fff;
        }
        .toast-success {
            background: #1d8a47;
            border-left-color: #1d8a47;
            color: #fff;
        }
        .toast-fail {
            background: #ff5e57;
            border-left-color: #ff5e57;
            color: #fff;
        }
        .toast.show {
            opacity: 1;
            pointer-events: auto;
            top: 32px;
        }

        .msg-success {
            display: none;
        }
        .msg-fail {
            display: none;
        }

        .chart-box {
            flex: 1 1 300px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .chart-box canvas {
            margin-top: 20px;
            max-width: 280px;
        } 

        .chart-box-chart canvas {
            margin-top: 10px;
            max-width: 100%;
            height: 250px;
            margin-bottom: 50px;
        }

        .chart-box h3 {
            margin: 0;
            color: var(--heading-colour);
            font-weight: 600;
        }

        .chart-row {
            display:flex; 
            flex-wrap:wrap; 
            gap:30px; 
            justify-content:center; 
            margin-top:10px;
        }

        @media (max-width: 700px) {
            main { padding: 12px; }
            .user-table th, .user-table td { padding: 7px; font-size: 14px;}
            .toast { right: 10px; top: 10px; }
        }
    </style>
</head>
<body>
    <?php include 'nav.php'; ?>

    <main>
        <!-- Charts row -->
        <div class="chart-row">

            <!-- Approval pie -->
            <div class="chart-box">
                <h3>User Approval Status</h3>
                <canvas id="userChart"></canvas>
            </div>

            <!-- New‑users doughnut -->
            <div class="chart-box">
                <h3>New Users This Month</h3>
                <canvas id="newUserChart"></canvas>
            </div>

            <!-- User growth chart -->
            <div class="chart-box-chart">
                <h3>User Growth (Past 6 Months)</h3>
                <canvas id="growthChart"></canvas>
            </div>
        </div>

        <!-- Page header -->
    <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
        <h2 style="margin-bottom: 0;">User Overview</h2>
        
        <!-- Link to user approval -->
        <a href="admin_approval.php" 
           style="
                padding: 10px 22px;
                background: var(--primary-colour);
                color: #fff;
                border-radius: 8px;
                text-decoration: none;
                font-weight: 600;
                box-shadow: 0 1px 6px rgba(106,123,162,0.06);
                transition: background 0.2s;
            "
           onmouseover="this.style.background= 'var(--primary-hover)'"
           onmouseout="this.style.background= 'var(--primary-colour)'"
        >User Approval</a>
    </div>

    <!-- Toast notification -->
    <div id="toast" class="toast" style="display: none;">
        <span id="toast-msg"></span>
    </div>

    <!-- Search box to filter users by username or email -->
    <div class="user-search-box" style="margin-top: 18px;">
        <input type="text" id="userSearch" placeholder="Search users by username or email...">
    </div>
    
    <!-- Scrollable container for table -->
    <div style="overflow-x:auto;">
        <table class="user-table" id="userTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Profile</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <!-- Loop through all users and generate a row for each one -->
            <?php foreach ($users as $index => $user): ?>
                <tr>
                    <!-- Displaying their name -->
                    <td><?= $index + 1; ?></td>
                    <!-- Display the profile image -->
                    <td>
                        <?php
                            $imgFile = trim($user['profile_img']);
                            $imgFileClean = ltrim(str_replace('profile/', '', $imgFile), '/\\');
                            $imgPath = 'profile/' . $imgFileClean;
                            if (!empty($imgFileClean) && file_exists(__DIR__ . '/' . $imgPath)) {
                                $finalImg = $imgPath;
                            } else {
                                $finalImg = 'images/default_profile.png';
                            }
                        ?>
                        <img src="<?= htmlspecialchars($finalImg) ?>" class="profile-img" alt="Profile">
                    </td>
                    <!-- Displaying their username -->
                    <td><?= htmlspecialchars($user['username']); ?></td>

                    <!-- Inline edit for email and role -->
                    <form method="post" style="display: contents;">
                        <input type="hidden" name="user_id" value="<?= $user['user_id']; ?>">
                        <!-- Editable email input -->
                        <td class="edit-fields">
                            <input type="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" required>
                        </td>
                        <!-- Role selection dropdown -->
                        <td class="edit-fields">
                            <select name="roles" required>
                                <option value="user" <?= $user['roles'] === 'user' ? 'selected' : '' ?>>User</option>
                                <option value="admin" <?= $user['roles'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                            </select>
                        </td>
                        <!-- Show approved or pending -->
                        <td>
                            <?php if ($user['is_approved']): ?>
                                <span class="is-approved approved">Approved</span>
                            <?php else: ?>
                                <span class="is-approved pending">Pending</span>
                            <?php endif; ?>
                        </td>
                        <!-- Edit and Delete action buttons -->
                        <td class="admin-actions">
                            <button type="submit" name="edit_user" class="btn-minimal" title="Save Changes">
                                <img src="icons/save_black.svg" alt="Save" class="action-icon">
                            </button>
                    </form>
                            <!-- Form to handle the deletion of the user -->
                            <form method="post" onsubmit="return confirm('Are you sure you want to delete this user?');" style="display: contents;">
                                <input type="hidden" name="user_id" value="<?= $user['user_id']; ?>">
                                <button type="submit" name="delete_user" class="btn-delete" title="Delete User">
                                    <img src="icons/delete_black.svg" alt="Delete" class="delete-icon">
                                </button>
                            </form>
                        </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>
    <script>
        // Toast show/hide function
        function showToast(message, type = "success") {
        var toast = document.getElementById('toast');
        var toastMsg = document.getElementById('toast-msg');
        toastMsg.textContent = message;
        toast.className = 'toast ' + (type === "success" ? "toast-success" : "toast-fail") + ' show';
        toast.style.display = 'block';

        setTimeout(function(){
            toast.classList.remove('show');
            setTimeout(function() {
                toast.style.display = 'none';
                // Reload only if success
                if (type === "success") {
                    window.location.reload();
                }
            }, 500);
        }, 2800);
    }

        // Display toast if redirected with message (PHP to JS)
        <?php if ($msg): ?>
            showToast("<?= addslashes($msg) ?>", "<?= (strpos($msg, 'success') !== false) ? 'success' : 'fail' ?>");
        <?php endif; ?>

        // Live search function (by username or email)
        document.getElementById('userSearch').addEventListener('keyup', function() {
            let input = this.value.toLowerCase();
            let rows = document.querySelectorAll('#userTable tbody tr');
            rows.forEach(function(row) {
                let username = row.cells[2].textContent.toLowerCase();
                let email = row.cells[3].textContent.toLowerCase();
                if (username.includes(input) || email.includes(input)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
    <!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- For the pie chart -->
<script>
    // Chart data from PHP
    const approvedCount = <?= $approvedCount ?>;
    const pendingCount = <?= $pendingCount ?>;
    
    const growthLabels = <?= json_encode($labels) ?>;
    const growthData   = <?= json_encode($growthData) ?>;

    const ctx = document.getElementById('userChart').getContext('2d');
    const newUsersThisMonth = <?= $monthUsersCount ?>;
    const existingUsers     = <?= $existingUsersLong ?>;

    const ctxNew = document.getElementById('newUserChart').getContext('2d');
    new Chart(ctxNew, {
        type: 'doughnut',
        data: {
            labels: ['New This Month', 'Existing Users'],
            datasets: [{
                data: [newUsersThisMonth, existingUsers],
                backgroundColor: ['#4caf50', '#90a4ae'],     // green & muted grey‑blue
                borderColor:    ['#388e3c', '#607d8b'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    labels: {
                        color: getComputedStyle(document.body).getPropertyValue('--heading-colour'),
                        font: { family: 'Roboto', size: 14 }
                    }
                },
                tooltip: { callbacks: {
                    label: ctx => `${ctx.label}: ${ctx.parsed} user${ctx.parsed !== 1 ? 's' : ''}`
                }}
            }
        }
    });

    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Approved Users', 'Pending Users'],
            datasets: [{
                label: 'User Status',
                data: [approvedCount, pendingCount],
                backgroundColor: ['#1d8a47', '#d93434'],
                borderColor: ['#146c36', '#a12525'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    labels: {
                        color: getComputedStyle(document.body).getPropertyValue('--heading-colour'),
                        font: {
                            family: 'Roboto',
                            size: 14
                        }
                    }
                }
            }
        }
    });

    // Line Chart for User Growth
    const ctxGrowth = document.getElementById('growthChart').getContext('2d');

    // Creating a new Chart.js
    new Chart(ctxGrowth, {
        type: 'line', // Type: line chart
        data: {
            labels: growthLabels,
            datasets: [{
                label: 'New Users',
                data: growthData, // Y-axis values
                // Line styling
                borderColor: '#1d8a47',
                backgroundColor: 'rgba(29, 138, 71, 0.1)',
                borderWidth: 2,
                pointBackgroundColor: '#1d8a47',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true, // It will resize with the screen
            scales: {
                y: {
                    beginAtZero: true, // y-axis always begins at 0
                    ticks: {
                        precision: 0, // No decimal place
                        color: getComputedStyle(document.body).getPropertyValue('--heading-colour')
                    },
                    grid: {
                        color: 'rgba(0,0,0,0.05)' // Faint grid lines
                    }
                },
                x: {
                    ticks: {
                        color: getComputedStyle(document.body).getPropertyValue('--heading-colour')
                    },
                    grid: {
                        display: false // Hiding the grid lines
                    }
                }
            },
            plugins: {
                legend: {
                    labels: {
                        color: getComputedStyle(document.body).getPropertyValue('--heading-colour'),
                        font: {
                            family: 'Roboto',
                            size: 14
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: ctx => `+${ctx.parsed.y} users`
                    }
                }
            }
        }
    });
</script>

</body>
</html>