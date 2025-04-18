<?php
session_start();
require_once '../common/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

// Get flashcards shared with the current user
$stmt = $conn->prepare("
    SELECT f.*, u.username as sender_name 
    FROM shared_flashcards sf
    JOIN flashcards f ON sf.flashcard_id = f.id
    JOIN users u ON sf.sender_id = u.id
    WHERE sf.recipient_id = ?
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$sharedFlashcards = $result->fetch_all(MYSQLI_ASSOC);
?>

<!-- HTML to display shared flashcards -->
<h2>Flashcards Shared With You</h2>
<?php if (count($sharedFlashcards) > 0): ?>
    <ul>
        <?php foreach ($sharedFlashcards as $card): ?>
            <li>
                <strong><?= htmlspecialchars($card['question']) ?></strong> - 
                <?= htmlspecialchars($card['answer']) ?> 
                (Shared by <?= htmlspecialchars($card['sender_name']) ?>)
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>No flashcards have been shared with you yet.</p>
<?php endif; ?>