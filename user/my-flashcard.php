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
$owned_query = "SELECT f.*, 'owner' as permission_type, 
                NULL as share_id, NULL as owner_id, NULL as share_status
                FROM flashcards f 
                WHERE f.user_id = ? AND f.subject = ?";
$owned_stmt = $conn->prepare($owned_query);
$owned_stmt->bind_param("is", $userId, $subject);
$owned_stmt->execute();
$owned_flashcards = $owned_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get shared flashcards with proper handling of view/edit permissions
$shared_query = "SELECT 
                    sf.permissions as permission_type,
                    sf.share_id,
                    sf.owner_id,
                    sf.status as share_status,
                    f.id,
                    f.user_id,
                    f.question,
                    f.answer,
                    f.subject,
                    f.subject_id,
                    f.image_path,
                    f.pdf_path,
                    f.created_at
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
$flashcards = array_merge($owned_flashcards, $shared_flashcards);
?>

<!DOCTYPE html>

<html lang="en">
<head>
        <meta charset="UTF-8">
        <title>Flashcards - <?= htmlspecialchars($subject) ?> - RecallIt</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
        <style>
            body {
                margin: 0;
                padding: 0;
                font-family: 'Poppins', sans-serif;
                background: #0e0e10;
                color: #fff;
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                align-items: center;
            }

.flashcard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background-color: #1a1a1a;
            box-shadow: 0 2px 10px rgba(0, 247, 255, 0.1);
            width: 100%;
        }

        .logo-section {
            display: flex;
            align-items: center;
        }

        .logo {
            width: 40px;
            height: 40px;
            margin-right: 10px;
        }

        .logo-name {
            font-size: 24px;
            font-weight: bold;
            color: #00f7ff;
        }

        .home-section {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 30px;
        }

        .home-link {
            font-size: 30px;
            color: #00f7ff;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .home-link:hover {
            color: #02c6d2;
        }

        .subject-title {
            margin-top: 60px;
            font-size: 28px;
            color: #00d4ff;
            text-align: center;
            width: 95%;
            max-width: 1200px;
        }

        .container {
            margin: 30px auto 40px auto;
            width: 95%;
            max-width: 1200px;
            background: #1a1a1a;
            padding: 60px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 212, 255, 0.2);
            min-height: auto;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 60px;
        }

        .flashcard-wrapper {
            perspective: 1000px;
            flex-basis: calc(33% - 60px);
            max-width: 350px;
            height: 400px;
            margin-bottom: 50px;
        }

        @media (max-width: 900px) {
            .flashcard-wrapper {
                flex-basis: calc(50% - 60px);
            }
        }

        @media (max-width: 600px) {
            .flashcard-wrapper {
                flex-basis: 90%;
            }
        }

        .flashcard {
            width: 100%;
            height: 100%;
            position: relative;
            transition: transform 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            transform-style: preserve-3d;
            cursor: pointer;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 212, 255, 0.2);
        }

        .flashcard.flipped {
            transform: rotateY(180deg);
        }

        .card-face {
            position: absolute;
            width: 100%;
            height: 100%;
            backface-visibility: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            background-color: #2c2c3e;
            border-radius: 12px;
            color: #fff;
            padding: 20px;
            text-align: center;
        }

        .card-face h3 {
            color: #00d4ff;
            margin-bottom: 5px;
        }

        .card-face p {
            flex-grow: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        .card-face.back {
            background-color: #3a3a4d;
            transform: rotateY(180deg);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .flip-button {
            background-color: #00f7ff;
            color: #0e0e10;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
            margin-top: auto;
            margin-bottom: 10px;
        }

        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 10px;
            width: 100%;
            align-items: center;
            margin-top: 10px;
            margin-bottom: 10px;
        }

        .action-button {
            background-color: #00f7ff;
            color: #0e0e10;
            border: none;
            padding: 10px 15px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            transition: background-color 0.3s ease;
            width: 80%;
            text-align: center;
        }

        .action-button:hover {
            background-color: #00e5ff;
        }

        .no-files {
            color: #888;
            font-size: 16px;
            margin-top: 10px;
            margin-bottom: 10px;
        }

        .back-link {
            margin-top: 40px;
            color: #00d4ff;
            text-decoration: none;
            transition: color 0.3s;
        }

        .back-link:hover {
            color: #00f7ff;
        }

        .back-link i {
            margin-right: 8px;
        }

        .card-image {
            max-width: 50%;
            max-height: 120px;
            margin: 10px 0;
            border-radius: 8px;
            object-fit: contain;
        }

        .share-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.7);
        }

        .share-modal-content {
            background-color: #1a1a1a;
            margin: 15% auto;
            padding: 30px;
            border-radius: 12px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 4px 15px rgba(0, 212, 255, 0.3);
        }

        .close-share-modal {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close-share-modal:hover {
            color: #fff;
        }

        .answer-heading {
            margin-top: 1.5px;
            margin-bottom: 8px;
        }

        .message-alert {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #00f7ff;
            color: #0e0e10;
            padding: 15px 25px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            z-index: 1001;
            animation: fadeIn 0.3s, fadeOut 0.3s 2.5s forwards;
        }
        .permission-badge {
            background: rgba(0, 247, 255, 0.2);
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            margin: 10px 0;
            display: inline-block;
            color: #00f7ff;
        }

        .permission-badge i {
            margin-right: 5px;
        }
        @keyframes fadeIn {
            from { opacity: 0; top: 0; }
            to { opacity: 1; top: 20px; }
        }
        
        @keyframes fadeOut {
            from { opacity: 1; top: 20px; }
            to { opacity: 0; top: 0; }
        }
    </style>
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
            $is_owner = ($card['permission_type'] === 'owner');
            $share_id = $card['share_id']; // Now available for shared cards
            $share_status = $card['share_status']; // Now available
            $can_edit = $is_owner || $card['permission_type'] === 'edit';
            
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
                            <?php elseif ($card['permission_type'] === 'edit'): ?>
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
                        <option value="edit">Can Edit</option>
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