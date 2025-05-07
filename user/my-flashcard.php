<?php
session_start();
require_once '../common/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$subject = isset($_GET['subject']) ? $_GET['subject'] : '';
$message = isset($_GET['message']) ? urldecode($_GET['message']) : '';

if (empty($subject)) {
    header("Location: subjects.php");
    exit;
}

// Get owned flashcards
$owned_query = "SELECT f.*, 'owner' as permissions, 
                NULL as share_id, NULL as owner_id, NULL as status
                FROM flashcards f 
                WHERE f.user_id = ? AND f.subject = ?";
$owned_stmt = $conn->prepare($owned_query);
$owned_stmt->bind_param("is", $userId, $subject);
$owned_stmt->execute();
$owned_flashcards = $owned_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get shared flashcards with proper handling of view/edit permissions
$shared_query = "SELECT 
                   *
                FROM shared_flashcards sf
                JOIN flashcards f ON (
                    (sf.permissions = 'edit' AND sf.recipient_flashcard_id = f.id) OR 
                    (sf.permissions = 'view' AND sf.flashcard_id = f.id)
                )
                WHERE sf.recipient_id = ? 
                AND sf.status = 'Accepted'
                AND f.subject = ?";
$shared_stmt = $conn->prepare($shared_query);
$shared_stmt->bind_param("is", $userId, $subject);
$shared_stmt->execute();
$shared_flashcards = $shared_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Combine results
// Deduplicate flashcards (avoid showing shared ones that are already owned)
$owned_ids = array_column($owned_flashcards, 'id');
$filtered_shared_flashcards = array_filter($shared_flashcards, function($card) use ($owned_ids) {
    return !in_array($card['id'], $owned_ids);
});
$flashcards = array_merge($owned_flashcards, $filtered_shared_flashcards);

?>

<!DOCTYPE html>

<html lang="en">
<head>
        <meta charset="UTF-8">
        <title>Flashcards - <?= htmlspecialchars($subject) ?> - RecallIt</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
        <link rel="stylesheet" href="myflash.css">
</head>
<body>
    <div class="flashcard-header">
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

    
<?php if (!empty($message)): ?>
    <div class="message-alert" id="messageAlert">
        <?= htmlspecialchars($message) ?>
    </div>
    <script>
        setTimeout(() => {
            document.getElementById('messageAlert').style.display = 'none';
        }, 3000);
    </script>
<?php endif; ?>

<h2 class="subject-title"><?= htmlspecialchars($subject) ?> Flashcards</h2>
    
<div class="container">
    <?php if (count($flashcards) > 0): ?>
        <?php foreach ($flashcards as $card): ?>
            <?php
            $is_owner = ($card['permissions'] === 'owner');
            $share_id = $card['share_id']; // Now available for shared cards
            $share_status = $card['status']; // Now available
            $can_edit = $is_owner || $card['permissions'] === 'edit';
            
            $baseUrl = '/RECALLIT1/yuka_4/';
            $imagePath = !empty($card['image_path']) ? $card['image_path'] : '';
            $imageUrl = $imagePath ? $baseUrl . $imagePath : '';
            $imageExists = $imagePath && file_exists($_SERVER['DOCUMENT_ROOT'] . $imageUrl);

            $pdfPath = !empty($card['pdf_path']) ? $card['pdf_path'] : '';
            $pdfUrl = $pdfPath ? $baseUrl . $pdfPath : '';
            $pdfExists = $pdfPath && file_exists($_SERVER['DOCUMENT_ROOT'] . $pdfUrl);
            ?>
            
            <div class="flashcard-wrapper">
                <div class="flashcard">
                    <div class="card-face front">
                        <h3>Question</h3>
                        <p><?= htmlspecialchars($card['question']) ?></p>
                        <button class="flip-button">Flip</button>
                    </div>
                    
                    <div class="card-face back">
                        <h3 class="answer-heading">Answer</h3>
                        <p><?= htmlspecialchars($card['answer']) ?></p>
                        
                        <div class="permission-badge">
                            <?php if ($is_owner): ?>
                                <i class="fas fa-crown"></i> Owner
                            <?php elseif ($card['permissions'] === 'edit'): ?>
                                <i class="fas fa-edit"></i> Can Edit
                            <?php else: ?>
                                <i class="fas fa-eye"></i> View Only
                            <?php endif; ?>
                        </div>

                        <div class="action-buttons">
                            <?php if ($imageExists || $pdfExists): ?>
                                <?php if ($pdfExists): ?>
                                    <a href="<?= htmlspecialchars($pdfUrl) ?>" class="action-button" target="_blank">
                                        <i class="fas fa-file-pdf"></i> View PDF
                                    </a>
                                <?php endif; ?>
                                <?php if ($imageExists): ?>
                                    <a href="<?= htmlspecialchars($imageUrl) ?>" class="action-button" target="_blank">
                                        <i class="fas fa-image"></i> View Image
                                    </a>
                                <?php endif; ?>
                            <?php else: ?>
                                <p class="no-files">No files uploaded</p>
                            <?php endif; ?>

                            <?php if ($can_edit): ?>
                                <a href="update_flashcard.php?id=<?= $card['id'] ?>" class="action-button">
                                    <i class="fas fa-edit"></i> Update
                                </a>
                            <?php endif; ?>
                       
                            <?php if ($is_owner): ?>
                                <button class="action-button share-button" data-card-id="<?= $card['id'] ?>">
                                    <i class="fas fa-share-alt"></i> Share
                                </button>
                            <?php endif; ?>
                            <?php if ($is_owner): ?>
                            <form method="post" action="delete_flashcard.php" style="display:inline;">
                                <input type="hidden" name="flashcard_id" value="<?= $card['id'] ?>">
                                <button type="submit" class="action-button" onclick="return confirm('Are you sure you want to delete this flashcard?');">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        <?php endif; ?>
   
                        </div>
                        <button class="flip-button">Flip</button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No flashcards found for this subject.</p>
    <?php endif; ?>
</div>

<a href="subjects.php" class="back-link">
    <i class="fas fa-arrow-left"></i> Back to Subjects
</a>
    <div id="shareModal" class="share-modal">
        <div class="share-modal-content">
            <span class="close-share-modal">&times;</span>
            <h3 style="color: #00d4ff; margin-bottom: 20px; text-align: center;">Share Flashcard</h3>
            <form id="shareForm" method="post" action="share_flashcard.php?subject=<?= urlencode($subject) ?>">
                <input type="hidden" name="flashcard_id" id="shareFlashcardId">
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; color: #eee;">Recipient's Username:</label>
                    <input type="text" name="recipient_username" required 
                        style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #333; 
                        background: #2c2c3e; color: #fff; box-sizing: border-box;"
                        placeholder="Enter username">
                </div>
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; color: #eee;">Permissions:</label>
                    <select name="permissions" required style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #333; background: #2c2c3e; color: #fff; box-sizing: border-box;">
                        <option value="view">View Only</option>
                        
                    </select>
                </div>
                <button type="submit" style="background-color: #00f7ff; color: #0e0e10; border: none; 
                        padding: 12px 24px; border-radius: 8px; cursor: pointer; font-size: 16px; width: 100%;">
                    Send
                </button>
            </form>
        </div>
    </div>

    <script>
        // Flip functionality
        document.querySelectorAll('.flip-button').forEach(button => {
            button.addEventListener('click', function(e) {
                e.stopPropagation();
                this.closest('.flashcard').classList.toggle('flipped');
            });
        });

        // Share modal functionality
        const shareButtons = document.querySelectorAll('.share-button');
        const shareModal = document.getElementById('shareModal');
        const shareFlashcardId = document.getElementById('shareFlashcardId');
        
        shareButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.stopPropagation();
                shareFlashcardId.value = this.getAttribute('data-card-id');
                shareModal.style.display = 'block';
            });
        });
        
        document.querySelector('.close-share-modal').addEventListener('click', () => {
            shareModal.style.display = 'none';
        });
        
        window.addEventListener('click', (e) => {
            if (e.target === shareModal) {
                shareModal.style.display = 'none';
            }
        });

        // Form validation
        document.getElementById('shareForm').addEventListener('submit', function(e) {
            const username = document.querySelector('input[name="recipient_username"]').value.trim();
            if (!username) {
                e.preventDefault();
                alert('Please enter a username');
            }
        });
    </script>
</body>
</html> 