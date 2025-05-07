<?php
session_start();
require_once '../common/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['flashcard_id'])) {
    $flashcardId = $_POST['flashcard_id'];

    // Confirm ownership
    $check_query = "SELECT * FROM flashcards WHERE id = ? AND user_id = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("ii", $flashcardId, $userId);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        $delete_query = "DELETE FROM flashcards WHERE id = ?";
        $delete_stmt = $conn->prepare($delete_query);
        $delete_stmt->bind_param("i", $flashcardId);
        $delete_stmt->execute();

        $subject = $result->fetch_assoc()['subject'];
        header("Location: my-flashcard.php?subject=" . urlencode($subject) . "&message=" . urlencode($message));
        exit;
    } else {
        header("Location: subjects.php?message=" . urlencode("Unauthorized delete attempt."));
        exit;
    }
}
?>
