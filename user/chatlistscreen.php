<?php
include '../common/config.php';
session_start();
$logged_in_user_id = $_SESSION['user_id'];

$query = "SELECT DISTINCT u.id, u.username 
          FROM users u 
          JOIN messages m ON (u.id = m.sender_id OR u.id = m.receiver_id) 
          WHERE (m.sender_id = $logged_in_user_id OR m.receiver_id = $logged_in_user_id) 
          AND u.id != $logged_in_user_id";

$result = $conn->query($query);
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chats - RecallIt</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
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

        .chat-container {
            max-width: 600px;
            margin: 80px auto 20px;
            background-color: #1a1a1a;
            border-radius: 12px;
            box-shadow: 0 2px 15px rgba(18, 239, 247, 0.2);
            overflow: hidden;
        }

        .chat-header {
            background-color: #26262c;
            color: #00f7ff;
            padding: 15px;
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            border-bottom: 1px solid #00f7ff;
        }

        .chat-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .chat-item {
            display: flex;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #2c2c3e;
            cursor: pointer;
            transition: background 0.3s;
        }

        .chat-item:hover {
            background-color: #2c2c3e;
        }

        .chat-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #00f7ff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: #121212;
            font-weight: bold;
            margin-right: 15px;
        }

        .chat-name {
            flex-grow: 1;
            font-size: 16px;
            font-weight: 500;
            color: #ffffff;
        }

        .goto-chat {
            background-color: #00d4ff;
            color: #000;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .goto-chat:hover {
            background-color: #02a8c2;
            transform: translateY(-2px);
        }

        .fab {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background-color: #00f7ff;
            color: #121212;
            border: none;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            font-size: 30px;
            text-align: center;
            line-height: 60px;
            text-decoration: none;
            box-shadow: 0 2px 15px rgba(0, 247, 255, 0.3);
            transition: all 0.3s ease;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .fab:hover {
            background-color: #02c6d2;
            transform: scale(1.1);
        }

        .empty-message {
            text-align: center;
            padding: 30px;
            color: #aaa;
        }
    </style>
</head>
<body>
    <!-- Header matching RecallIt theme -->
    <div class="header">
        <div class="logo-section">
            <img src="logo.png" alt="RecallIt Logo" class="logo">
            <span class="logo-name">RecallIt</span>
        </div>
        <a href="user-dashboard.php" class="home-link">
            <i class="fas fa-home"></i>
        </a>
    </div>

    <!-- Chat container -->
    <div class="chat-container">
        <div class="chat-header">
            <i class="fas fa-comments"></i> My Chats
        </div>

        <?php if ($result->num_rows > 0): ?>
            <ul class="chat-list">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <li class="chat-item">
                        <div class="chat-avatar">
                            <?= strtoupper(substr($row['username'], 0, 1)) ?>
                        </div>
                        <div class="chat-name"><?= htmlspecialchars($row['username']) ?></div>
                        <a href="chat.php?user_id=<?= $row['id'] ?>" class="goto-chat">
                            Chat <i class="fas fa-arrow-right"></i>
                        </a>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <div class="empty-message">
                <p>No chat conversations yet</p>
                <p>Start a new chat using the button below</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Floating action button -->
    <a href="create_chat.php" class="fab" title="Start New Chat">
        <i class="fas fa-plus"></i>
    </a>
</body>
</html>