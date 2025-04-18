<?php
session_start();
require_once '../common/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['flashcard_id'], $_POST['recipient_email'])) {
    $flashcardId = $_POST['flashcard_id'];
    $senderId = $_SESSION['user_id'];
    $recipientEmail = $_POST['recipient_email'];
    
    // Verify flashcard belongs to sender
    $stmt = $conn->prepare("SELECT id FROM flashcards WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $flashcardId, $senderId);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows === 0) {
        $_SESSION['error'] = "Flashcard not found or you don't have permission to share it";
        header("Location: my-flashcard.php");
        exit;
    }
    
    // Get recipient user ID
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $recipientEmail);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $_SESSION['error'] = "User with that email not found";
        header("Location: my-flashcard.php?subject=".urlencode($_POST['subject']));
        exit;
    }
    
    $recipient = $result->fetch_assoc();
    
    // Create share record
    $stmt = $conn->prepare("INSERT INTO flashcard_shares (flashcard_id, sender_id, recipient_id) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $flashcardId, $senderId, $recipient['id']);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "";
    } else {
        $_SESSION['error'] = "Error sharing flashcard: " . $conn->error;
    }
    
    header("Location: my-flashcard.php?subject=".urlencode($_POST['subject']));
    exit;
}