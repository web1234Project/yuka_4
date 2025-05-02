<?php
session_start(); // Start the session to access $_SESSION
require_once '../common/config.php';

$success = "";
$error = "";
$reports = [];

// Make sure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['report_title'];
    $desc = $_POST['report_desc'];
    $status = 0;

    // Get current count
    $result = $conn->query("SELECT COUNT(*) AS count FROM report");
    $row = $result->fetch_assoc();
    $report_id = "R" . str_pad($row['count'] + 1, 3, "0", STR_PAD_LEFT); // e.g., R001, R002

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO report (report_id, report_title, report_desc, report_created, report_status) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $report_id, $title, $desc, $user_id, $status);

    if ($stmt->execute()) {
        $success = "Report created successfully!";
    } else {
        $error = "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch all reports created by this user
$stmt = $conn->prepare("SELECT * FROM report WHERE report_created = ?");
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $reports[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Report - RecallIt</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background: #0e0e10;
            color: #fff;
            min-height: 100vh;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background-color: #1a1a1a;
            box-shadow: 0 2px 10px rgba(0, 247, 255, 0.1);
            width: 100%;
        }

        .logo-section {
            display: flex;
            align-items: center;
        }

        .logo {
            width: 40px;
            height: 40px;
            margin-right: 10px;
        }

        .logo-name {
            font-size: 24px;
            font-weight: bold;
            color: #00f7ff;
        }

        .home-link {
            font-size: 30px;
            color: #00f7ff;
            text-decoration: none;
            transition: color 0.3s ease;
            margin-right: 30px;
        }

        .home-link:hover {
            color: #02c6d2;
        }

        .page-title {
            margin: 40px auto;
            font-size: 28px;
            color: #00d4ff;
            text-align: center;
            width: 95%;
            max-width: 1200px;
        }

        .form-container {
            background: #1a1a1a;
            margin: 20px auto;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 212, 255, 0.2);
            width: 90%;
            max-width: 600px;
        }

        .form-container h2 {
            color: #00f7ff;
            margin-bottom: 25px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #00d4ff;
        }

        input[type="text"],
        textarea {
            width: 100%;
            padding: 12px;
            background: #2c2c3e;
            border: 1px solid #3a3a4d;
            border-radius: 8px;
            color: #fff;
            font-family: 'Poppins', sans-serif;
        }

        textarea {
            min-height: 120px;
            resize: vertical;
        }

        button[type="submit"] {
            background-color: #00f7ff;
            color: #0e0e10;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            width: 100%;
            transition: background-color 0.3s ease;
        }

        button[type="submit"]:hover {
            background-color: #00e5ff;
        }

        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            text-align: center;
            font-weight: bold;
        }

        .success {
            background-color: rgba(0, 255, 0, 0.1);
            color: #00ff00;
            border: 1px solid #00ff00;
        }

        .error {
            background-color: rgba(255, 0, 0, 0.1);
            color: #ff5555;
            border: 1px solid #ff5555;
        }

        .report-list {
            background: #1a1a1a;
            margin: 40px auto;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 212, 255, 0.2);
            width: 90%;
            max-width: 1000px;
        }

        .report-list h3 {
            color: #00f7ff;
            margin-bottom: 25px;
            text-align: center;
            font-size: 24px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #3a3a4d;
        }

        th {
            background-color: #2c2c3e;
            color: #00f7ff;
            font-weight: bold;
        }

        tr:hover {
            background-color: #2c2c3e;
        }

        .status-open {
            color: #00ff00;
        }

        .status-closed {
            color: #ff5555;
        }

        .chat-link {
            color: #00f7ff;
            text-decoration: none;
            transition: color 0.3s;
        }

        .chat-link:hover {
            color: #00e5ff;
            text-decoration: underline;
        }

        .back-link {
            display: block;
            text-align: center;
            margin: 30px auto;
            color: #00d4ff;
            text-decoration: none;
            transition: color 0.3s;
            width: fit-content;
        }

        .back-link:hover {
            color: #00f7ff;
        }

        .back-link i {
            margin-right: 8px;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="logo-section">
            <img src="logo.png" alt="RecallIt Logo" class="logo" />
            <span class="logo-name">RecallIt</span>
        </div>
        <a href="user-dashboard.php" class="home-link">
            <i class="fa-solid fa-house"></i>
        </a>
    </div>

    <h1 class="page-title">Create Report</h1>

    <div class="form-container">
        <?php if ($success): ?>
            <div class="message success"><?= $success ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="message error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="report_title">Report Title</label>
                <input type="text" id="report_title" name="report_title" required>
            </div>
            
            <div class="form-group">
                <label for="report_desc">Report Description</label>
                <textarea id="report_desc" name="report_desc" required></textarea>
            </div>
            
            <button type="submit">Create Report</button>
        </form>
    </div>

    <?php if (!empty($reports)): ?>
        <div class="report-list">
            <h3>Your Reports</h3>
            <table>
                <thead>
                    <tr>
                        <th>Report ID</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reports as $r): ?>
                        <tr>
                            <td><?= htmlspecialchars($r['report_id']) ?></td>
                            <td><?= htmlspecialchars($r['report_title']) ?></td>
                            <td><?= htmlspecialchars($r['report_desc']) ?></td>
                            <td class="<?= $r['report_status'] == 0 ? 'status-open' : 'status-closed' ?>">
                                <?= $r['report_status'] == 0 ? 'Open' : 'Closed' ?>
                            </td>
                            <td><a class="chat-link" href="./report_chat.php?report_id=<?= $r['report_id'] ?>"><i class="fas fa-comments"></i> Chat</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <a href="user-dashboard.php" class="back-link">
        <i class="fas fa-arrow-left"></i> Back to Dashboard
    </a>
</body>
</html>