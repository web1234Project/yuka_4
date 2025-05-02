<?php
session_start();
require_once '../common/config.php';

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access");
}

$owner_id = $_SESSION['user_id'];
$flashcard_id = $_POST['flashcard_id'];
$recipient_username = trim($_POST['recipient_username']);
$permissions = $_POST['permissions'];
$subject = isset($_GET['subject']) ? $_GET['subject'] : '';

// Validate input
if (empty($recipient_username)) {
    header("Location: my-flashcard.php?subject=".urlencode($subject)."&message=Username cannot be empty");
    exit;
}

// Verify the user owns the flashcard they're trying to share
$stmt = $conn->prepare("SELECT user_id FROM flashcards WHERE id = ?");
$stmt->bind_param("i", $flashcard_id);
$stmt->execute();
$result = $stmt->get_result();
$flashcard = $result->fetch_assoc();

if (!$flashcard || $flashcard['user_id'] != $owner_id) {
    header("Location: my-flashcard.php?subject=".urlencode($subject)."&message=You can only share flashcards you own");
    exit;
}

// Check if sharing with self
$stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$stmt->bind_param("i", $owner_id);
$stmt->execute();
$result = $stmt->get_result();
$owner = $result->fetch_assoc();

if (strtolower($recipient_username) == strtolower($owner['username'])) {
    header("Location: my-flashcard.php?subject=".urlencode($subject)."&message=Cannot share with yourself");
    exit;
}

// Get recipient user ID
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $recipient_username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: my-flashcard.php?subject=".urlencode($subject)."&message=User not found");
    exit;
}

$recipient = $result->fetch_assoc();
$recipient_id = $recipient['id'];

// Check if already shared and pending/accepted
$stmt = $conn->prepare("
    SELECT * FROM shared_flashcards 
    WHERE flashcard_id = ? 
    AND recipient_id = ? 
    AND status IN ('Pending', 'Accepted')
");
$stmt->bind_param("ii", $flashcard_id, $recipient_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    header("Location: my-flashcard.php?subject=".urlencode($subject)."&message=Already shared with this user");
    exit;
}

// Start transaction
$conn->begin_transaction();

try {
    // Insert into shared_flashcards as pending
    $stmt = $conn->prepare("
        INSERT INTO shared_flashcards 
        (flashcard_id, owner_id, recipient_id, permissions, status) 
        VALUES (?, ?, ?, ?, 'Pending')
    ");
    $stmt->bind_param("iiis", $flashcard_id, $owner_id, $recipient_id, $permissions);
    $stmt->execute();
    
    // Create notification
    $message = "You've received a shared flashcard from " . $owner['username'] . 
               " (" . ucfirst($permissions) . " permissions)";
    $stmt = $conn->prepare("
        INSERT INTO notifications 
        (user_id, message, flashcard_id, status) 
        VALUES (?, ?, ?, 'Unread')
    ");
    $stmt->bind_param("isi", $recipient_id, $message, $flashcard_id);
    $stmt->execute();
    
    $conn->commit();
    header("Location: my-flashcard.php?subject=".urlencode($subject)."&message=Flashcard shared successfully");
} catch (Exception $e) {
    $conn->rollback();
    header("Location: my-flashcard.php?subject=".urlencode($subject)."&message=Error: " . urlencode($e->getMessage()));
}
exit;
?>