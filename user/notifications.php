<?php
session_start();
require_once '../common/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

// Handle flashcard acceptance
if (isset($_POST['accept_share'])) {
    $notification_id = $_POST['notification_id'];
    $flashcard_id = $_POST['flashcard_id'];
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Verify this is still a pending share request
        $share_stmt = $conn->prepare("
            SELECT sf.*, f.* 
            FROM shared_flashcards sf
            JOIN flashcards f ON sf.flashcard_id = f.id
            WHERE sf.flashcard_id = ? 
            AND sf.recipient_id = ?
            AND sf.status = 'Pending'
        ");
        $share_stmt->bind_param("ii", $flashcard_id, $user_id);
        $share_stmt->execute();
        $share = $share_stmt->get_result()->fetch_assoc();
        
        if (!$share) {
            throw new Exception("This sharing request is no longer valid");
        }

        // Get the original flashcard
        $original = $share;
        
        // Check if subject exists for recipient, if not create it
        $subject_name = $original['subject'];
        $subject_check = $conn->prepare("SELECT id FROM subjects WHERE subject_name = ? AND user_id = ?");
        $subject_check->bind_param("si", $subject_name, $user_id);
        $subject_check->execute();
        $subject_result = $subject_check->get_result();
        
        if ($subject_result->num_rows == 0) {
            $insert_subject = $conn->prepare("INSERT INTO subjects (subject_name, user_id) VALUES (?, ?)");
            $insert_subject->bind_param("si", $subject_name, $user_id);
            $insert_subject->execute();
            $subject_id = $conn->insert_id;
        } else {
            $subject_id = $subject_result->fetch_assoc()['id'];
        }
        
        // Copy to recipient's flashcards (without files)
        // $stmt = $conn->prepare("
        //     INSERT INTO flashcards 
        //     (user_id, question, answer, subject, subject_id) 
        //     VALUES (?, ?, ?, ?, ?)
        // ");
        // $stmt->bind_param("isssi", 
        //     $user_id, 
        //     $original['question'], 
        //     $original['answer'], 
        //     $original['subject'],
        //     $subject_id
        // );
        // $stmt->execute();
        // $new_flashcard_id = $conn->insert_id;
        
        // Update share status to accepted and store recipient flashcard ID
        $update_share = $conn->prepare("
        UPDATE shared_flashcards SET 
        status = 'Accepted', 
        permissions = ?, 
        subjectid = ? 
        WHERE share_id = ?
    ");
    $update_share->bind_param("sii", $share['permissions'], $subject_id, $share['share_id']);
    $update_share->execute();
        
        // Update notification
        $update_notification = $conn->prepare("
            UPDATE notifications SET 
            status = 'Read' 
            WHERE notification_id = ?
        ");
        $update_notification->bind_param("i", $notification_id);
        $update_notification->execute();
        
        $conn->commit();
        $success = "Flashcard added to your collection!";
    } catch (Exception $e) {
        $conn->rollback();
        $error = "Error accepting flashcard: " . $e->getMessage();
    }
}

// Handle flashcard rejection
if (isset($_POST['reject_share'])) {
    $notification_id = $_POST['notification_id'];
    $flashcard_id = $_POST['flashcard_id'];
    
    try {
        // Verify this is still a pending share request
        $share_stmt = $conn->prepare("
            SELECT share_id FROM shared_flashcards 
            WHERE flashcard_id = ? 
            AND recipient_id = ?
            AND status = 'Pending'
        ");
        $share_stmt->bind_param("ii", $flashcard_id, $user_id);
        $share_stmt->execute();
        $share = $share_stmt->get_result()->fetch_assoc();
        
        if (!$share) {
            throw new Exception("This sharing request is no longer valid");
        }

        // Update share status to rejected
        $update_share = $conn->prepare("
            UPDATE shared_flashcards 
            SET status = 'Rejected' 
            WHERE share_id = ?
        ");
        $update_share->bind_param("i", $share['id']);
        $update_share->execute();
        
        // Update notification
        $update_notification = $conn->prepare("
            UPDATE notifications 
            SET status = 'Read' 
            WHERE notification_id = ?
        ");
        $update_notification->bind_param("i", $notification_id);
        $update_notification->execute();
        
        $success = "Flashcard sharing declined";
    } catch (Exception $e) {
        $error = "Error rejecting flashcard: " . $e->getMessage();
    }
}

// Get all notifications with flashcard details
$notifications = $conn->prepare("
    SELECT n.*, f.question, f.answer, f.subject, s.permissions, s.status as share_status
    FROM notifications n
    JOIN flashcards f ON n.flashcard_id = f.id
    JOIN shared_flashcards s ON s.flashcard_id = f.id AND s.recipient_id = ?
    WHERE n.user_id = ? AND n.status = 'Unread'
    ORDER BY n.notification_id DESC
");
$notifications->bind_param("ii", $user_id, $user_id);
$notifications->execute();
$notification_result = $notifications->get_result();
?>

<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Notifications - RecallIt</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="noti.css">

</head>
<body>
    <div class="header">
        <div class="logo-section">
            <img src="logo.png" alt="RecallIt Logo" class="logo" />
            <span class="logo-name">RecallIt</span>
        </div>
        <div class="home-section">
            <a href="user-dashboard.php" class="home-link">
                <i class="fa-solid fa-house"></i>
            </a>
        </div>
    </div>


<div class="container">
    <h1>Notifications</h1>
    
    <?php if (!empty($success)): ?>
        <div class="success-message"><?= htmlspecialchars($success) ?></div>
    <?php elseif (!empty($error)): ?>
        <div class="error-message"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <?php if ($notification_result->num_rows > 0): ?>
        <?php while ($notification = $notification_result->fetch_assoc()): ?>
            <div class="notification">
                <div class="notification-message"><?= htmlspecialchars($notification['message']) ?></div>
                <div class="notification-time">
                    <?= date('M j, Y g:i a', strtotime($notification['created_at'])) ?>
                </div>
                
                <div class="flashcard-preview">
                    <p><strong>Question:</strong> <?= htmlspecialchars($notification['question']) ?></p>
                    <p><strong>Answer:</strong> <?= htmlspecialchars($notification['answer']) ?></p>
                    <p><strong>Subject:</strong> <?= htmlspecialchars($notification['subject']) ?></p>
                    
                    <div class="permission-badge">
                        <?php if ($notification['permissions'] == 'edit'): ?>
                            <i class="fas fa-edit"></i> Can Edit
                        <?php else: ?>
                            <i class="fas fa-eye"></i> View Only
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($notification['share_status'] == 'Pending'): ?>
                        <div class="notification-actions">
                            <form method="post">
                                <input type="hidden" name="notification_id" value="<?= $notification['notification_id'] ?>">
                                <input type="hidden" name="flashcard_id" value="<?= $notification['flashcard_id'] ?>">
                                <button type="submit" name="accept_share" class="btn-accept">
                                    <i class="fas fa-check"></i> Accept
                                </button>
                                <button type="submit" name="reject_share" class="btn-reject">
                                    <i class="fas fa-times"></i> Reject
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="empty-notifications">
            <i class="fas fa-bell-slash"></i>
            <p>No new notifications</p>
        </div>
    <?php endif; ?>
    
    <a href="user-dashboard.php" class="back-link">
        <i class="fas fa-arrow-left"></i> Back to Dashboard
    </a>
</div>


</body>
</html> 
