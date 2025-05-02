<?php
session_start();
require_once '../common/config.php';

$search_email = "";
$search_result = null;
$chat_exists = false;

if (!isset($_SESSION['user_id'])) {
    die("Access denied. Please log in.");
}

$logged_in_user_id = $_SESSION['user_id'];

// Search logic
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
    $search_email = trim($_POST['email']);

    // Exact match on email
    $stmt = $conn->prepare("SELECT id, username, email FROM users WHERE email = ? AND id != ?");
    $stmt->bind_param("si", $search_email, $logged_in_user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $search_result = $result->fetch_assoc();

        // Check if chat exists
        $target_user_id = $search_result['id'];
        $chat_stmt = $conn->prepare("SELECT COUNT(*) as count FROM messages WHERE
            (sender_id = ? AND receiver_id = ?) OR
            (sender_id = ? AND receiver_id = ?)");
        $chat_stmt->bind_param("iiii", $logged_in_user_id, $target_user_id, $target_user_id, $logged_in_user_id);
        $chat_stmt->execute();
        $chat_result = $chat_stmt->get_result()->fetch_assoc();
        $chat_exists = $chat_result['count'] > 0;
        $chat_stmt->close();
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Start New Chat - RecallIt</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Basic reset and font */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #121212;
            color: #f8f6f6;
            min-height: 100vh;
            padding-top: 60px; /* Adjust for fixed header */
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background-color: #1a1a1a;
            box-shadow: 0 2px 10px rgba(0, 247, 255, 0.1);
            z-index: 1000;
        }

        .logo-section {
            display: flex;
            align-items: center;
        }

        .logo {
            width: 30px;
            height: 30px;
            margin-right: 10px;
        }

        .logo-name {
            font-size: 20px;
            font-weight: bold;
            color: #00f7ff;
        }

        .home-link {
            font-size: 24px;
            color: #00f7ff;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .home-link:hover {
            color: #02c6d2;
        }

        .container {
            background-color: #1a1a1a;
            padding: 25px;
            border-radius: 12px;
            max-width: 500px;
            width: 100%;
            margin-top: 20px;
            box-shadow: 0 2px 15px rgba(18, 239, 247, 0.2);
        }

        h2 {
            text-align: center;
            color: #00f7ff;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            margin-bottom: 20px;
        }

        input[type="text"] {
            flex: 1;
            padding: 10px;
            border: 1px solid #2c2c3e;
            border-radius: 8px 0 0 8px;
            font-size: 16px;
            background-color: #121212;
            color: #ffffff;
            outline: none;
        }

        button {
            padding: 10px 15px;
            background-color: #00f7ff;
            color: #121212;
            border: none;
            border-radius: 0 8px 8px 0;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #02c6d2;
        }

        .result {
            margin-top: 20px;
        }

        .user-box {
            padding: 15px;
            border: 1px solid #2c2c3e;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #26262c;
            margin-bottom: 10px;
        }

        .user-info {
            flex: 1;
        }

        .user-info strong {
            color: #ffffff;
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        .user-info small {
            color: #aaa;
            display: block;
        }

        .chat-btn {
            background-color: #00d4ff;
            color: #000;
            padding: 8px 14px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .chat-btn:hover {
            background-color: #02a8c2;
        }

        .not-found {
            color: #ff6b6b;
            font-weight: bold;
            text-align: center;
            padding: 10px;
            background-color: #333;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="logo-section">
            <img src="logo.png" alt="RecallIt Logo" class="logo">
            <span class="logo-name">RecallIt</span>
        </div>
        <a href="user-dashboard.php" class="home-link">
            <i class="fas fa-home"></i>
        </a>
    </div>

    <div class="container">
        <h2>Start New Chat</h2>

        <form method="POST">
            <input type="text" name="email" placeholder="Enter user email..." value="<?= htmlspecialchars($search_email) ?>" required>
            <button type="submit">
                <i class="fas fa-search"></i> Search
            </button>
        </form>

        <div class="result">
            <?php if ($search_result): ?>
                <div class="user-box">
                    <div class="user-info">
                        <strong><?= htmlspecialchars($search_result['username']) ?></strong><br>
                        <small><?= htmlspecialchars($search_result['email']) ?></small>
                    </div>
                    <a class="chat-btn" href="chat.php?user_id=<?= $search_result['id'] ?>">
                        <i class="fas fa-comments"></i> <?= $chat_exists ? 'Go to Chat' : 'Start Chat' ?>
                    </a>
                </div>
            <?php elseif ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
                <p class="not-found"><i class="fas fa-exclamation-triangle"></i> No user found with this email.</p>
            <?php endif; ?>
        </div>
    </div>

</body>

</html> 