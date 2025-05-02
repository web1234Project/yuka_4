<?php
session_start();
$host = "127.0.0.1";
$dbname = "recallit_db";
$user = "root";
$pass = ""; // Change if needed

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Get report_id from URL
if (!isset($_GET['report_id'])) {
    die("Missing report ID.");
}

$report_id = $_GET['report_id'];

// Get user_id who created the report
$stmt = $conn->prepare("SELECT report_created FROM report WHERE report_id = ?");
$stmt->bind_param("s", $report_id);
$stmt->execute();
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close();

if (!$user_id) {
    die("Invalid report ID or user not found.");
}

// Handle admin message submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['message'])) {
    $msg = trim($_POST['message']);
    $sender_id = $report_id;       // Admin as report_id
    $receiver_id = $user_id;
    $sent_at = date("Y-m-d H:i:s");
    $msg_status = 0;

    if (!empty($msg)) {
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message, sent_at, msg_status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $sender_id, $receiver_id, $msg, $sent_at, $msg_status);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch messages for the chat between admin and the user
$stmt = $conn->prepare("SELECT * FROM messages WHERE 
    (sender_id = ? AND receiver_id = ?) OR 
    (sender_id = ? AND receiver_id = ?)
    ORDER BY sent_at ASC");
$stmt->bind_param("ssss", $report_id, $user_id, $user_id, $report_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Admin Chat - <?= htmlspecialchars($report_id) ?></title>
    <style>
        body {
            font-family: Arial;
            background: #f5f5f5;
            padding: 20px;
        }

        .chat-box {
            background: white;
            max-width: 600px;
            margin: auto;
            padding: 20px;
            border-radius: 10px;
            height: 400px;
            overflow-y: auto;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .message {
            margin: 10px 0;
            padding: 10px 15px;
            border-radius: 20px;
            max-width: 70%;
        }

        .from-admin {
            background: #007BFF;
            color: white;
            margin-left: auto;
        }

        .from-user {
            background: #ddd;
            color: black;
            margin-right: auto;
        }

        form {
            display: flex;
            justify-content: center;
            margin-top: 15px;
        }

        input[type="text"] {
            width: 80%;
            padding: 10px;
            border-radius: 20px;
            border: 1px solid #ccc;
        }

        button {
            margin-left: 10px;
            padding: 10px 20px;
            border: none;
            background: #28a745;
            color: white;
            border-radius: 20px;
            cursor: pointer;
        }
    </style>
</head>

<body>

    <h2>Admin Chat for Report ID: <?= htmlspecialchars($report_id) ?></h2>

    <div class="chat-box">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="message <?= $row['sender_id'] == $report_id ? 'from-admin' : 'from-user' ?>">
                <?= htmlspecialchars($row['message']) ?><br>
                <small><?= $row['sent_at'] ?></small>
            </div>
        <?php endwhile; ?>
    </div>

    <form method="POST">
        <input type="text" name="message" placeholder="Type your reply..." required />
        <button type="submit">Send</button>
    </form>

</body>

</html>