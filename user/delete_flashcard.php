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

// Check if the flashcard ID is provided in the URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $flashcardId = $_GET['id'];
    $userId = $_SESSION['user_id'];

    // Prepare the SQL statement to delete the flashcard
    $sql = "DELETE FROM flashcards WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind the parameters
    $stmt->bind_param("ii", $flashcardId, $userId);

    // Execute the statement
    if ($stmt->execute()) {
        // Deletion successful, redirect back to my-flashcards.php with a success message
        header("Location: my-flashcard.php?delete=success");
        exit;
    } else {
        // Deletion failed, redirect back to my-flashcards.php with an error message
        header("Location: my-flashcard.php?delete=failed");
        exit;
    }

    // Close the statement
    $stmt->close();
} else {
    // If no valid ID is provided, redirect back to my-flashcards.php with an error
    header("Location: my-flashcard.php?delete=invalid_id");
    exit;
}

// Close the database connection
$conn->close();
?>