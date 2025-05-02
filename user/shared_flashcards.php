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

// Fetch shared flashcards
$query = "
    SELECT 
        f.id, f.user_id, f.question, f.answer, f.subject, f.subject_id,
        f.image_path, f.pdf_path, f.created_at,
        sf.permissions, sf.share_id, sf.status
    FROM flashcards f
    LEFT JOIN shared_flashcards sf 
        ON (sf.flashcard_id = f.id AND sf.recipient_id = ?)
    WHERE f.subject = ? AND (sf.status = 'Accepted' OR sf.share_id IS NULL)
";

$stmt = $conn->prepare($query);
$stmt->bind_param("is", $userId, $subject);
$stmt->execute();
$sharedFlashcards = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shared Flashcards - <?= htmlspecialchars($subject) ?> - RecallIt</title>
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

        .header {
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

        @keyframes fadeIn {
            from { opacity: 0; top: 0; }
            to { opacity: 1; top: 20px; }
        }
        
        @keyframes fadeOut {
            from { opacity: 1; top: 20px; }
            to { opacity: 0; top: 0; }
        }

        .empty-notifications {
            text-align: center;
            color: #aaa;
            padding: 40px 0;
        }
    </style>
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

    <h2 class="subject-title">Shared Flashcards - <?= htmlspecialchars($subject) ?></h2>
        
    <div class="container">
        <?php if (count($sharedFlashcards) > 0): ?>
            <?php foreach ($sharedFlashcards as $card): ?>
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
                                <?php if ($card['permissions'] === 'edit'): ?>
                                    <i class="fas fa-edit"></i> Can Edit
                                <?php else: ?>
                                    <i class="fas fa-eye"></i> View Only
                                <?php endif; ?>
                            </div>
                            <button class="flip-button">Flip</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-notifications">
                <i class="fas fa-bell-slash"></i>
                <p>No shared flashcards found</p>
            </div>
        <?php endif; ?>
    </div>

    <a href="subjects.php" class="back-link">
        <i class="fas fa-arrow-left"></i> Back to Subjects
    </a>

    <script>
        // Flip functionality
        document.querySelectorAll('.flip-button').forEach(button => {
            button.addEventListener('click', function(e) {
                e.stopPropagation();
                this.closest('.flashcard').classList.toggle('flipped');
            });
        });
    </script>
</body>
</html>