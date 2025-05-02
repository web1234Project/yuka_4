<?php
session_start();
require_once '../common/config.php';

if (!isset($_GET['report_id'])) {
    die("No report ID provided.");
}

$report_id = $_GET['report_id'];
$admin_id = $report_id;
$reporter_id = null;
$messages = [];
$error = "";

// Get the user (creator) from report
$stmt = $conn->prepare("SELECT report_created FROM report WHERE report_id = ?");
$stmt->bind_param("s", $report_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $reporter_id = $row['report_created'];
} else {
    die("Invalid report ID.");
}
$stmt->close();

// Handle sending message
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['message'])) {
    $msg = trim($_POST['message']);
    if ($msg != "") {
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message, sent_at, msg_status) VALUES (?, ?, ?, NOW(), 0)");
        $stmt->bind_param("sss", $reporter_id, $admin_id, $msg);
        if (!$stmt->execute()) {
            $error = "Error sending message: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Fetch all chat messages between admin and user
$stmt = $conn->prepare("
    SELECT * FROM messages
    WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)
    ORDER BY sent_at ASC
");
$stmt->bind_param("ssss", $admin_id, $reporter_id, $reporter_id, $admin_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Report Chat - <?= htmlspecialchars($report_id) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"/>
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: #0e0e10;
            color: #fff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .header {
            padding: 20px;
            background-color: #1a1a1a;
            box-shadow: 0 2px 10px rgba(0, 247, 255, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
        }

        .logo img {
            width: 40px;
            height: 40px;
            margin-right: 10px;
        }

        .logo-name {
            font-size: 24px;
            color: #00f7ff;
            font-weight: bold;
        }

        .container {
            flex: 1;
            padding: 120px 20px 20px;
            max-width: 800px;
            margin: auto;
            width: 100%;
        }

        .chat-box {
            background: #1a1a1a;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 0 15px rgba(0, 212, 255, 0.2);
            display: flex;
            flex-direction: column;
            max-height: 600px;
            overflow-y: auto;
        }

        h2 {
            text-align: center;
            color: #00d4ff;
            margin-bottom: 20px;
        }

        .chat-message {
            padding: 15px;
            border-radius: 12px;
            margin: 10px 0;
            max-width: 80%;
            word-wrap: break-word;
            background: #2c2c3e;
            position: relative;
        }

        .sent {
            background: #0072ff;
            align-self: flex-end;
            margin-left: auto;
            color: white;
        }

        .received {
            background: #02c6d2;
            align-self: flex-start;
            margin-right: auto;
            color: white;
        }

        .chat-form {
            margin-top: 20px;
            display: flex;
            gap: 10px;
        }

        .chat-form textarea {
            flex: 1;
            padding: 12px;
            border-radius: 10px;
            background: #2c2c3e;
            border: none;
            color: white;
            resize: none;
            font-size: 16px;
        }

        .chat-form button {
            background: linear-gradient(135deg, #0072ff, #00d4ff);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 10px;
            font-size: 16px;
            cursor: pointer;
        }

        .error {
            color: #ff4d4d;
            text-align: center;
            margin-bottom: 10px;
        }

        small {
            font-size: 12px;
            opacity: 0.7;
            display: block;
            margin-top: 5px;
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
    </style>
</head>

<body>
    <div class="header">
        <div class="logo">
            <img src="logo.png" alt="RecallIt Logo" />
            <span class="logo-name">RecallIt</span>
        </div>
        <a href="user-dashboard.php" class="home-link">
            <i class="fas fa-home"></i>
        </a>
    </div>

    <div class="container">
        <div class="chat-box" id="chatBox">
            <h2>Chat for Report: <?= htmlspecialchars($report_id) ?></h2>

            <?php if ($error): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php foreach ($messages as $msg): ?>
                <div class="chat-message <?= ($msg['sender_id'] == $admin_id) ? 'sent' : 'received' ?>">
                    <div><?= htmlspecialchars($msg['message']) ?></div>
                    <small><?= $msg['sent_at'] ?></small>
                </div>
            <?php endforeach; ?>
        </div>

        <form class="chat-form" method="POST">
            <textarea name="message" placeholder="Type your message..." required></textarea>
            <button type="submit">Send</button>
        </form>
    </div>

    <script>
        function scrollToBottom() {
            var chatBox = document.getElementById("chatBox");
            chatBox.scrollTop = chatBox.scrollHeight;
        }

        function loadMessages() {
            var reportId = "<?= $report_id ?>";

            if (!reportId) return;

            var xhr = new XMLHttpRequest();
            xhr.open("POST", "fetch_messages.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onload = function () {
                if (xhr.status == 200) {
                    var messages = JSON.parse(xhr.responseText);
                    var chatBox = document.getElementById("chatBox");
                    chatBox.innerHTML = '<h2>Chat for Report: <?= htmlspecialchars($report_id) ?></h2>';

                    if (messages.length === 0) {
                        chatBox.innerHTML += '<p style="color:#aaa;font-style:italic;text-align:center;">No messages exchanged yet.</p>';
                    } else {
                        messages.forEach(function (msg) {
                            var div = document.createElement("div");
                            div.className = "chat-message " + (msg.sender_id === "<?= $admin_id ?>" ? "sent" : "received");
                            div.innerHTML = '<div>' + escapeHtml(msg.message) + '</div><small>' + msg.sent_at + '</small>';
                            chatBox.appendChild(div);
                        });
                    }
                    scrollToBottom();
                }
            };
            xhr.send("report_id=" + encodeURIComponent(reportId));
        }

        // Escape HTML (to prevent XSS)
        function escapeHtml(text) {
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function (m) { return map[m]; });
        }

        // Load messages immediately, and then every 2 seconds
        window.onload = function() {
            loadMessages();
            setInterval(loadMessages, 2000); // refresh every 2 seconds
        };
    </script>
</body>

</html>
