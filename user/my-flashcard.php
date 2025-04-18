<?php
// Start the session to manage user login state.
session_start();

// Check if the 'user_id' session variable is not set.
// If not set, the user is not logged in, so redirect to the login page.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit; // Terminate the current script after redirection.
}

// Include the database configuration file.
// This file contains the database connection details.
require_once '../common/config.php';

// Check if the 'subject' GET parameter is set in the URL.
// This parameter is used to filter flashcards by subject.
if (isset($_GET['subject'])) {
    // Sanitize the subject value from the GET request.
    $subject = $_GET['subject'];
    // Retrieve the logged-in user's ID from the session.
    $userId = $_SESSION['user_id'];

    // SQL query to select all flashcards for the specific user and subject,
    // ordered by the creation date in descending order (newest first).
    $sql = "SELECT * FROM flashcards WHERE user_id = ? AND subject = ? ORDER BY created_at DESC";
    // Prepare the SQL statement for execution. This prevents SQL injection.
    $stmt = $conn->prepare($sql);

    // Check if the preparation of the SQL statement failed.
    if (!$stmt) {
        die("Prepare failed: " . $conn->error); // Terminate the script with an error message.
    }

    // Bind the parameters to the prepared statement.
    // 'i' for integer (user_id), 's' for string (subject).
    $stmt->bind_param("is", $userId, $subject);
    // Execute the prepared SQL statement.
    $stmt->execute();
    // Get the result set from the executed statement.
    $result = $stmt->get_result();
    // Fetch all rows from the result set as an associative array.
    $flashcards = $result->fetch_all(MYSQLI_ASSOC);

    // --- DEBUGGING: Check if any flashcards are fetched ---
    // if (empty($flashcards)) {
    //     echo "<p style='color: red;'>No flashcards found for subject: " . htmlspecialchars($subject) . " and user ID: " . $userId . "</p>";
    // } else {
    //     echo "<p style='color: green;'>Found " . count($flashcards) . " flashcards.</p>";
    //     // --- DEBUGGING: Output the structure of the first flashcard ---
    //     echo "<pre style='color: yellow;'>";
    //     print_r($flashcards[0]);
    //     echo "</pre>";
    // }
    // --- END DEBUGGING ---

} else {
    // If the 'subject' GET parameter is not set, redirect the user
    // back to the subjects.php page to choose a subject.
    header("Location: subjects.php");
    exit; // Terminate the script after redirection.
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Flashcards - <?= htmlspecialchars(urldecode($subject)) ?> - RecallIt</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <style>
        /* Basic styling for the body of the page */
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
            justify-content: space-between; /* Distribute space for buttons at the bottom */
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
            margin-top: auto; /* Push to the bottom */
            margin-bottom: 10px;
        }

        .card-face.back .flip-button {
            margin-top: 10px; /* Adjust if needed */
            margin-bottom: 5px;
        }

        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 10px;
            width: 100%;
            align-items: center;
            margin-top: 10px; /* Add some space above the buttons */
            margin-bottom: 10px; /* Add some space below the buttons */
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
            max-width: 80%;
            max-height: 200px;
            margin-top: 10px;
            margin-bottom: 10px;
            border-radius: 8px;
            object-fit: contain;
        }
        /* Share Modal Styles */
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

<h2 class="subject-title"><?= htmlspecialchars(urldecode($subject)) ?> Flashcards</h2>

<div class="container">
    <?php if (count($flashcards) > 0): ?>
        <?php foreach ($flashcards as $card): ?>
            <div class="flashcard-wrapper">
                <div class="flashcard" onclick="this.classList.toggle('flipped');">
                    <div class="card-face front">
                        <h3>Question</h3>
                        <p><?= htmlspecialchars($card['question']) ?></p>
                        <button class="flip-button">Flip</button>
                    </div>
                    <div class="card-face back">
                        <h3>Answer</h3>
                        <p><?= htmlspecialchars($card['answer']) ?></p>

                        <?php
                        // Define base paths
                        $baseUrl = '/RECALLIT1/yuka_4/'; // Adjust this to match your project's base URL

                        // Process image path
                        $imagePath = !empty($card['image_path']) ? $card['image_path'] : '';
                        $imageUrl = $imagePath ? $baseUrl . $imagePath : '';
                        $imageFullPath = $_SERVER['DOCUMENT_ROOT'] . $imageUrl;
                        $imageExists = $imagePath && file_exists($imageFullPath);

                        // Process PDF path
                        $pdfPath = !empty($card['pdf_path']) ? $card['pdf_path'] : '';
                        $pdfUrl = $pdfPath ? $baseUrl . $pdfPath : '';
                        $pdfFullPath = $_SERVER['DOCUMENT_ROOT'] . $pdfUrl;
                        $pdfExists = $pdfPath && file_exists($pdfFullPath);

                        // Debug output (uncomment when troubleshooting)
                        /*
                        echo "<pre>Debug Info for card ID: " . $card['id'] . "\n";
                        echo "DOCUMENT_ROOT: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
                        echo "Base URL: " . $baseUrl . "\n";
                        echo "Image Path (DB): " . $imagePath . "\n";
                        echo "Image URL: " . $imageUrl . "\n";
                        echo "Full Image Path: " . $imageFullPath . "\n";
                        echo "Image Exists: " . ($imageExists ? 'Yes' : 'No') . "\n";
                        echo "PDF Path (DB): " . $pdfPath . "\n";
                        echo "PDF URL: " . $pdfUrl . "\n";
                        echo "Full PDF Path: " . $pdfFullPath . "\n";
                        echo "PDF Exists: " . ($pdfExists ? 'Yes' : 'No') . "\n";
                        echo "</pre>";
                        */

                        // Display image if exists
                        if ($imageExists): ?>
                            <img src="<?= htmlspecialchars($imageUrl) ?>" alt="Flashcard Image" class="card-image">
                        <?php endif; ?>

                        <div class="action-buttons">
                            <?php if ($imageExists || $pdfExists): ?>
                                <?php if ($pdfExists): ?>
                                    <a href="<?= htmlspecialchars($pdfUrl) ?>" class="action-button" target="_blank">
                                        <i class="fas fa-file-pdf"></i> View Files
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

                            <a href="update_flashcard.php?id=<?= $card['id'] ?>" class="action-button">
                                <i class="fas fa-edit"></i> Update
                            </a>
                            <button class="action-button share-button" data-card-id="<?= $card['id'] ?>">
                                <i class="fas fa-share-alt"></i> Share
                                </button>
                        
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
</div>

<a href="subjects.php" class="back-link">
    <i class="fas fa-arrow-left"></i> Back to Subjects
</a>
<div id="shareModal" class="share-modal">
    <div class="share-modal-content">
        <span class="close-share-modal">&times;</span>
        <h3 style="color: #00d4ff; margin-bottom: 20px; text-align: center;">Share Flashcard</h3>
        <form id="shareForm" method="post" action="share_flashcard.php">
            <input type="hidden" name="flashcard_id" id="shareFlashcardId">
            <input type="hidden" name="subject" value="<?= htmlspecialchars($subject) ?>">
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; color: #eee;">Recipient's Email:</label>
                <input type="email" name="recipient_email" required 
                       style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #333; 
                       background: #2c2c3e; color: #fff; box-sizing: border-box;">
            </div>
            <button type="submit" style="background-color: #00f7ff; color: #0e0e10; border: none; 
                    padding: 12px 24px; border-radius: 8px; cursor: pointer; font-size: 16px; width: 100%;">
                Send
            </button>
        </form>
    </div>
</div>

<script>
    const flipButtons = document.querySelectorAll('.flip-button');
    flipButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            const card = this.closest('.flashcard');
            card.classList.toggle('flipped');
            event.stopPropagation();
        });
    });

    const flashcardWrappers = document.querySelectorAll('.flashcard-wrapper');
    flashcardWrappers.forEach(wrapper => {
        const card = wrapper.querySelector('.flashcard');
        wrapper.addEventListener('click', function() {
            card.classList.toggle('flipped');
        });
    });
    // Share functionality
    const shareButtons = document.querySelectorAll('.share-button');
    const shareModal = document.getElementById('shareModal');
    const shareFlashcardId = document.getElementById('shareFlashcardId');
    const closeModal = document.querySelector('.close-share-modal');
    
    shareButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            shareFlashcardId.value = this.getAttribute('data-card-id');
            shareModal.style.display = 'block';
        });
    });
    
    closeModal.addEventListener('click', () => {
        shareModal.style.display = 'none';
    });
    
    window.addEventListener('click', (e) => {
        if (e.target === shareModal) {
            shareModal.style.display = 'none';
        }
    });
</script>

</body>
</html>