<?php
session_start();
require_once '../common/config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    die("Access denied. Please log in as an administrator.");
}

// --- Report Management ---
// Initialize selected report ID
$selected_report_id = null;

// Handle 'Start Chat' action and message sending
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['start_chat'])) {
        $selected_report_id = $_POST['report_id'];
        $update = $conn->prepare("UPDATE report SET report_status = 1 WHERE report_id = ?");
        $update->bind_param("s", $selected_report_id);
        $update->execute();
    } elseif (isset($_POST['send_message']) && isset($_POST['report_id'])) {
        $report_id = $_POST['report_id'];
        $selected_report_id = $report_id; // Keep chat open

        // Get user_id who created the report
        $stmt = $conn->prepare("SELECT report_created FROM report WHERE report_id = ?");
        $stmt->bind_param("s", $report_id);
        $stmt->execute();
        $stmt->bind_result($user_id);
        $stmt->fetch();
        $stmt->close();

        if (!$user_id) {
            $chat_error = "Invalid report ID or user not found.";
        } else {
            $msg = trim($_POST['message']);
            $sender_id = $report_id;
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
    }  elseif (isset($_POST['report_id'])) {
        $selected_report_id = $_POST['report_id'];
    }
}

// Fetch all reports
$result = $conn->query("SELECT * FROM report ORDER BY report_id DESC");

// Fetch chat messages (moved outside the POST check)
$chat_messages = [];
if ($selected_report_id) {
    $stmt = $conn->prepare("SELECT report_created FROM report WHERE report_id = ?");
    $stmt->bind_param("s", $selected_report_id);
    $stmt->execute();
    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();
    
     if (!$user_id) {
        $chat_error = "Invalid report ID or user not found.";
    }
    else{
        $stmt = $conn->prepare("SELECT * FROM messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)");
        $stmt->bind_param("ssss", $selected_report_id, $user_id, $user_id, $selected_report_id);
        $stmt->execute();
        $result_message = $stmt->get_result();
        while ($row = $result_message->fetch_assoc()) {
            $chat_messages[] = $row;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Admin - Reports</title>
    <style>
        body {
            font-family: Arial;
            background-color: #eef;
            padding: 30px;
        }

        table {
            width: 100%;
            background: #fff;
            border-collapse: collapse;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #007BFF;
            color: white;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .chat-btn {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-right: 5px;
        }

        .chat-btn:hover {
            background-color: #218838;
        }

        .chat-btn:disabled {
            background-color: gray;
            cursor: not-allowed;
        }

        .chat-section {
            background-color: #fff;
            padding: 20px;
            margin-top: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: <?php echo $selected_report_id ? 'block' : 'none'; ?>;
        }

        .chat-box {
            background: #f0f0f0;
            border-radius: 5px;
            height: 300px;
            overflow-y: auto;
            margin-bottom: 10px;
            padding: 10px;
            display: flex;
            flex-direction: column;
            /* justify-content: flex-end;  Remove this line */
            position: relative; /* Add this */
        }

        .message {
            padding: 10px;
            margin-bottom: 5px;
            border-radius: 10px;
            max-width: 80%;
            margin-left: 0;
            margin-right: 0;
            display: inline-block;
        }

        .from-admin {
            background: #007BFF;
            color: white;
            align-self: flex-end;
            
        }

        .from-user {
            background: #ddd;
            color: black;
            align-self: flex-start;
        }

        .message-content {
           clear: both;
           display: block;
        }

        form.chat-form {
            display: flex;
            margin-top: 10px;
        }

        form.chat-form input[type="text"] {
            flex: 1;
            padding: 8px;
            border-radius: 5px 0 0 5px;
            border: 1px solid #ccc;
        }

        form.chat-form button {
            padding: 8px 12px;
            border: none;
            background: #28a745;
            color: white;
            border-radius: 0 5px 5px 0;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        form.chat-form button:hover {
            background-color: #218838;
        }
        
       .chat-box::-webkit-scrollbar {
            width: 10px;
        }

        .chat-box::-webkit-scrollbar-track {
            background: #f1f3f5;
            border-radius: 5px;
        }

        .chat-box::-webkit-scrollbar-thumb {
            background: #ced4da;
            border-radius: 5px;
        }

        .chat-box::-webkit-scrollbar-thumb:hover {
            background: #adb5bd;
        }
    </style>
</head>

<body>

    <h2>Admin - All Reports</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Description</th>
            <th>Created By</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['report_id']) ?></td>
                <td><?= htmlspecialchars($row['report_title']) ?></td>
                <td><?= htmlspecialchars($row['report_desc']) ?></td>
                <td><?= htmlspecialchars($row['report_created']) ?></td>
                <td><?= $row['report_status'] == 1 ? "Chat Started" : "Pending" ?></td>
                <td>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="report_id" value="<?= $row['report_id'] ?>">
                        <button type="submit" name="start_chat" class="chat-btn" <?= $row['report_status'] == 1 ? "disabled" : "" ?>>
                            <?= $row['report_status'] == 1 ? "Chatting" : "Start Chat" ?>
                        </button>
                         <button type="submit" name="report_id" class="chat-btn" value="<?= $row['report_id'] ?>" >View Chat</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

    <div class="chat-section">
        <h2>Chat with User (Report ID: <?= htmlspecialchars($selected_report_id) ?>)</h2>
        <?php if (isset($chat_error)): ?>
            <div style="color:red;"><?= $chat_error ?></div>
        <?php else: ?>
            <div class="chat-box" id="chatBox">
                <?php if (empty($chat_messages)): ?>
                    <p style="color:#aaa;font-style:italic;text-align:center;">No messages exchanged yet.</p>
                <?php else: ?>
                     <?php foreach ($chat_messages as $chat_message): ?>
                     <!-- <?php   print_r($chat_message) ; ?>
                     <?php print_r($selected_report_id);?> -->
                        <div class="message <?= $chat_message['sender_id'] == $selected_report_id ? 'from-admin' : 'from-user' ?>">
                            <div class="message-content">
                                 <?= htmlspecialchars($chat_message['message']) ?>
                                 <br>
                                 <small><?= $chat_message['sent_at'] ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <form method="POST" class="chat-form">
                <input type="hidden" name="report_id" value="<?= $selected_report_id ?>">
                <input type="text" name="message" placeholder="Type your reply..." required />
                <button type="submit" name="send_message">Send</button>
            </form>
        <?php endif; ?>
    </div>
    
    <script>
        // Function to scroll to the bottom of the chat box
        function scrollToBottom() {
            var chatBox = document.getElementById("chatBox");
            chatBox.scrollTop = chatBox.scrollHeight;
        }

        // Call scrollToBottom() when the page loads and after new messages are added
        window.onload = scrollToBottom; // Scroll on page load

        // You'll need to call scrollToBottom() whenever you add a new message to the chat box.
        // Since this is a server-side script, you might need to do this with JavaScript after the page updates.
        // Here's an example of how you might do it if you were adding messages dynamically with AJAX:
        
        // Example (Conceptual):  Call this function after you've added new message HTML to the chatBox
        function addNewMessageToChat(messageHTML) {
            var chatBox = document.getElementById("chatBox");
            chatBox.innerHTML += messageHTML; // Add the new message
            scrollToBottom();             // Scroll to the bottom
        }
        
    </script>
</body>

</html>
