<?php
// Include the database connection file
include '../common/config.php';

// Start the session to access session variables
session_start();

// Get the logged-in user's ID from the session
$logged_in_user_id = $_SESSION['user_id'];

// Check if the user_id parameter is provided in the URL
if (!isset($_GET['user_id'])) {
    die("User not specified."); // Terminate if no user_id is provided
}

// Sanitize and store the chat user's ID
$chat_user_id = intval($_GET['user_id']);

// Query to fetch the name of the user to chat with
$user_query = "SELECT username FROM users WHERE id = $chat_user_id";
$user_result = $conn->query($user_query);

// Check if the user exists in the database
if ($user_result->num_rows == 0) {
    die("User not found."); // Terminate if the user does not exist
}

// Fetch the user's details
$user = $user_result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Chat with <?= htmlspecialchars($user['username']) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Resetting some default styles */
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
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 60px; /* Adjust for fixed header */
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
            width: 100%;
            background-color: #1a1a1a;
            border-radius: 12px;
            box-shadow: 0 2px 15px rgba(18, 239, 247, 0.2);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            margin-top: 20px; /* Adjusted margin */
            flex-grow: 1; /* Allow it to take available vertical space */
        }

        .chat-header {
            background-color: #26262c;
            color: #00f7ff;
            padding: 15px;
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            border-bottom: 1px solid #00f7ff;
            position: relative;
        }

        .back-button {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            color: #00f7ff;
            border: none;
            cursor: pointer;
            font-size: 16px;
            transition: color 0.3s ease;
        }

        .back-button:hover {
            color: #02c6d2;
        }

        .chat-messages {
            flex-grow: 1;
            overflow-y: auto;
            padding: 15px;
            background-color: #121212; /* Dark background for messages */
            display: flex;
            flex-direction: column;
            scrollbar-width: thin; /* For Firefox */
            scrollbar-color: #26262c #1a1a1a; /* Thumb and track color */
        }

        /* WebKit (Chrome, Safari) scrollbar styles */
        .chat-messages::-webkit-scrollbar {
            width: 8px;
        }

        .chat-messages::-webkit-scrollbar-track {
            background: #1a1a1a;
        }

        .chat-messages::-webkit-scrollbar-thumb {
            background-color: #26262c;
            border-radius: 4px;
        }

        .message {
            padding: 10px 15px;
            margin-bottom: 10px;
            border-radius: 10px;
            max-width: 70%;
            word-wrap: break-word;
            color: #ffffff;
        }

        .sent {
            background: #00f7ff;
            color: #121212;
            align-self: flex-end;
            margin-left: auto;
        }

        .received {
            background: #26262c;
            align-self: flex-start;
        }

        .chat-footer {
            display: flex;
            padding: 10px;
            background-color: #1a1a1a;
            border-top: 1px solid #2c2c3e;
        }

        .message-input {
            flex-grow: 1;
            padding: 10px;
            border: 1px solid #2c2c3e;
            border-radius: 5px;
            outline: none;
            background-color: #121212;
            color: #ffffff;
        }

        .send-button {
            background: #00f7ff;
            color: #121212;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            margin-left: 10px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .send-button:hover {
            background-color: #02c6d2;
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

    <div class="chat-container">
        <div class="chat-header">
            <button class="back-button" onclick="window.history.back()">
                <i class="fas fa-arrow-left"></i> Back
            </button>
            <?= htmlspecialchars($user['username']) ?>
        </div>

        <div class="chat-messages" id="chat-messages"></div>

        <div class="chat-footer">
            <form id="chat-form" style="display: flex; width: 100%;">
                <input type="text" id="message" class="message-input" placeholder="Type a message..." required>
                <button type="submit" class="send-button">
                    <i class="fas fa-paper-plane"></i> Send
                </button>
            </form>
        </div>
    </div>

    <script>
        // Reference to the chat messages container
        const chatBox = document.getElementById('chat-messages');

        // Function to fetch messages from the server
        function fetchMessages() {
            const xhr = new XMLHttpRequest();
            xhr.open("GET", "get_messages.php?user_id=<?= $chat_user_id ?>", true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    chatBox.innerHTML = xhr.responseText; // Update chat messages
                    chatBox.scrollTop = chatBox.scrollHeight; // Scroll to the bottom
                }
            };
            xhr.send();
        }

        // Event listener for the chat form submission
        document.getElementById("chat-form").addEventListener("submit", function(e) {
            e.preventDefault(); // Prevent default form submission
            const messageInput = document.getElementById("message");
            const message = messageInput.value.trim();
            if (message === "") return; // Do nothing if the message is empty

            // Send the message to the server
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "send_message.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.onload = function() {
                if (xhr.status === 200) {
                    messageInput.value = ""; // Clear the input field
                    fetchMessages(); // Refresh the chat messages
                }
            };
            xhr.send("message=" + encodeURIComponent(message) + "&receiver_id=<?= $chat_user_id ?>");
        });

        // Start polling for new messages every 2 seconds
        setInterval(fetchMessages, 2000);
        window.onload = fetchMessages; // Fetch messages when the page loads
    </script>

</body>

</html>