<?php
// Start the session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Include the database connection
require_once '../common/config.php';

// Get logged-in user's ID
$userId = $_SESSION['user_id'];

// Fetch unique subjects for this user
$sql = "SELECT DISTINCT subject FROM flashcards WHERE user_id = ? ORDER BY subject ASC";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$subjects = $result->fetch_all(MYSQLI_ASSOC);

// Close the statement and connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Flashcard Subjects - RecallIt</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background: #0e0e10;
            color: #fff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .flashcard-header {
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

        .home-section {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 30px;
        }

        .home-link {
            font-size: 30px;
            color: #00f7ff;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .home-link:hover {
            color: #02c6d2;
        }

        .container {
            margin: 80px auto 40px auto; /* Adjust top margin */
            width: 90%;
            max-width: 800px; /* Adjust max width */
            background: #1a1a1a;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 212, 255, 0.2);
            min-height: auto; /* Adjust min height */
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h2 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #00d4ff;
            text-align: center;
        }

        .subject-folder {
            background-color: #2c2c3e;
            color: #fff;
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 8px;
            width: 80%;
            text-align: center;
            text-decoration: none;
            transition: background-color 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 212, 255, 0.1);
        }

        .subject-folder:hover {
            background-color: #3a3a4d;
        }

        .subject-folder i {
            margin-right: 10px;
            color: #00f7ff;
        }

        .back-link {
            display: inline-block;
            margin-top: 30px;
            color: #00d4ff;
            text-decoration: none;
            transition: color 0.3s;
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

<div class="flashcard-header">
    <div class="logo-section">
        <img src="logo.png" alt="RecallIt Logo" class="logo" />
        <span class="logo-name">RecallIt</span>
    </div>
    <div class="home-section">
        <a href="user-dashboard.php" class="home-link">
            <i class="fa-solid fa-house"></i>
        </a>
    </div>
</div>

<div class="container">
    <h2>Choose a Subject</h2>
    <?php if (count($subjects) > 0): ?>
        <?php foreach ($subjects as $subject): ?>
            <a href="my-flashcard.php?subject=<?= urlencode($subject['subject']) ?>" class="subject-folder">
                <i class="fas fa-folder"></i> <?= htmlspecialchars($subject['subject']) ?>
            </a>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No subjects found.</p>
    <?php endif; ?>
    <a href="user-dashboard.php" class="back-link">
        <i class="fas fa-arrow-left"></i> Back to Study Guide
    </a>
</div>

</body>
</html>