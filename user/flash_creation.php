<?php
session_start();
require_once __DIR__ . '/../common/config.php';

// Verify database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['save'])) {
    $user_id = $_SESSION['user_id'];
    $subject = trim($_POST['subject'] ?? '');
    $notes = trim($_POST['notes'] ?? '');
    $difficulty = trim($_POST['difficulty'] ?? '');
    $question = trim($_POST['question'] ?? '');
    $answer = trim($_POST['answer'] ?? '');
    $imagePath = '';
    $pdfPath = '';

    // Upload directories
    $uploadFileDir = __DIR__ . '/../uploads/files/';
    $uploadImageDir = __DIR__ . '/../uploads/images/';

    // Create directories if they don't exist
    if (!is_dir($uploadFileDir)) mkdir($uploadFileDir, 0777, true);
    if (!is_dir($uploadImageDir)) mkdir($uploadImageDir, 0777, true);

    // Handle Document Upload (PDF/DOC/PPT)
    if (isset($_FILES['notesPdfUpload']) && $_FILES['notesPdfUpload']['error'] === UPLOAD_ERR_OK) {
        $allowedDocTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation'
        ];

        $docType = $_FILES['notesPdfUpload']['type'];
        $docExt = pathinfo($_FILES['notesPdfUpload']['name'], PATHINFO_EXTENSION);

        if (in_array($docType, $allowedDocTypes)) {
            $docName = time() . '-' . uniqid() . '.' . $docExt;
            $docTarget = $uploadFileDir . $docName;

            if (move_uploaded_file($_FILES['notesPdfUpload']['tmp_name'], $docTarget)) {
                $pdfPath = 'uploads/files/' . $docName;
            } else {
                $_SESSION['error'] = "Error uploading document.";
            }
        } else {
            $_SESSION['error'] = "Invalid document type.";
        }
    }

    // Handle Image Upload
    if (isset($_FILES['notesImageUpload']) && $_FILES['notesImageUpload']['error'] === UPLOAD_ERR_OK) {
        $allowedImageTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
        $imageType = $_FILES['notesImageUpload']['type'];
        $imgExt = pathinfo($_FILES['notesImageUpload']['name'], PATHINFO_EXTENSION);

        if (in_array($imageType, $allowedImageTypes)) {
            $imgName = time() . '-' . uniqid() . '.' . $imgExt;
            $imgTarget = $uploadImageDir . $imgName;

            if (move_uploaded_file($_FILES['notesImageUpload']['tmp_name'], $imgTarget)) {
                $imagePath = 'uploads/images/' . $imgName;
            } else {
                $_SESSION['error'] = "Error uploading image.";
            }
        } else {
            $_SESSION['error'] = "Invalid image type.";
        }
    }

    // Validate required fields
    if (empty($question) || empty($answer) || empty($difficulty)) {
        $_SESSION['error'] = "Question, Answer, and Difficulty are required fields.";
    } else { // Ensure user_id exists in the users table (use 'id' from the 'users' table)
        $stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows === 0) {
            $_SESSION['error'] = "User not found.";
            header("Location: login.php");
            exit;
        }
        $stmt->close();
        
        // Check if the subject exists in the subjects table
        $stmt = $conn->prepare("SELECT id FROM subjects WHERE subject_name = ? AND user_id = ?");
        $stmt->bind_param("si", $subject, $user_id);
        $stmt->execute();
        $stmt->store_result();
        
        // If the subject doesn't exist, insert it
        if ($stmt->num_rows === 0) {
            $stmt = $conn->prepare("INSERT INTO subjects (subject_name, user_id) VALUES (?, ?)");
            $stmt->bind_param("si", $subject, $user_id);  // Ensure user_id is passed correctly
            $stmt->execute();
            $subject_id = $stmt->insert_id;  // Get the newly inserted subject ID
        } else {
            $stmt->bind_result($subject_id);
            $stmt->fetch();  // Get the existing subject ID
        }
        $stmt->close();
        
        // Now insert the flashcard with the correct subject_id and user_id
        $stmt = $conn->prepare("INSERT INTO flashcards (user_id, subject_id, subject, notes, difficulty, question, answer, image_path, pdf_path, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("iisssssss", $user_id, $subject_id, $subject, $notes, $difficulty, $question, $answer, $imagePath, $pdfPath);
            if ($stmt->execute()) {
                $stmt->close();
                $_SESSION['success'] = "";
                header("Location: subjects.php");
                exit;
            } else {
                $_SESSION['error'] = "Error saving flashcard: " . $stmt->error;
            }
        }
    }

?>



    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Create Flashcard - RecallIt</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
        <style>
            * {
                box-sizing: border-box;
            }
            body {
                margin: 0;
                font-family: 'Poppins', sans-serif;
                background: #0e0e10;
                color: #fff;
                min-height: 100vh;
            }
            .flashcard-header {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 20px;
                background-color: #1a1a1a;
                box-shadow: 0 2px 10px rgba(0, 247, 255, 0.1);
                z-index: 1000;
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
            .container {
                width: 90%;
                max-width: 1200px;
                background: #1a1a1a;
                padding: 40px;
                border-radius: 15px;
                box-shadow: 0 0 20px rgba(0, 212, 255, 0.2);
                margin: 80px auto;
            }
            h2 {
                margin-bottom: 30px;
                text-align: center;
            }
            .form-group {
                margin-bottom: 25px;
            }
            label {
                display: block;
                margin-bottom: 10px;
                font-weight: 500;
                color: #ccc;
            }
            input[type="text"], textarea {
                width: 100%;
                padding: 12px;
                border: none;
                border-radius: 12px;
                background: #2c2c3e;
                color: #fff;
                font-size: 15px;
            }
            textarea {
                resize: none;
                min-height: 100px;
            }
            .selected-file {
                margin-top: 10px;
                color: #ccc;
                font-size: 14px;
            }
            input[type="file"] {
                display:none;
            }
            .import-btn {
                display: inline-block;
                padding: 10px 20px;
                border: 1px solid #999;
                border-radius: 12px;
                background: #1a1a2e;
                color: #fff;
                cursor: pointer;
                font-size: 14px;
                transition: 0.3s ease;
                margin-top: 10px;
            }
            .import-btn:hover {
                background-color: #2c2c3e;
                border-color: #00d4ff;
                color: #00d4ff;
            }
            .difficulty-btn-container {
                display: flex;
                gap: 10px;
                justify-content: center;
                margin-top: 15px;
            }
            .difficulty-btn {
                padding: 12px 24px;
                border-radius: 12px;
                font-size: 16px;
                font-weight: bold;
                cursor: pointer;
                transition: 0.3s ease;
                border: none;
                color: #fff;
                position: relative;
                padding-left: 40px;
            }
            .btn-easy { background-color: #00a859; }
            .btn-medium { background-color: #ffaa00; }
            .btn-difficult { background-color: #e94b3c; }
            .difficulty-btn:hover { opacity: 0.8; }
            .difficulty-btn.selected {
                box-shadow: 0 0 0 2px #fff, 0 0 0 4px #00f7ff;
            }
            .difficulty-btn .check-icon {
                position: absolute;
                left: 15px;
                top: 50%;
                transform: translateY(-50%);
                opacity: 0;
                transition: opacity 0.2s ease;
            }
            .difficulty-btn.selected .check-icon {
                opacity: 1;
            }
            button {
                padding: 14px 28px;
                background: linear-gradient(135deg, #0072ff, #00d4ff);
                border: none;
                border-radius: 12px;
                color: #fff;
                font-weight: bold;
                font-size: 18px;
                cursor: pointer;
                transition: 0.3s ease;
                display: block;
                margin: 30px auto 0;
            }
            button:hover {
                opacity: 0.9;
            }
            .alert {
                padding: 15px;
                margin-bottom: 20px;
                border-radius: 12px;
                text-align: center;
            }
            .alert-error {
                background-color: #300;
                color: #ff6b6b;
                border: 1px solid #ff0000;
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
                    <i class="fas fa-home"></i>
                </a>
            </div>
        </div>

        <div class="container">
            <h2>Create Flashcard</h2>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <?= htmlspecialchars($_SESSION['error']) ?>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($_SESSION['success']) ?>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <form method="post" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" id="subject" name="subject" placeholder="Enter subject..." required value="<?= htmlspecialchars($_POST['subject'] ?? '') ?>" />
                </div>

                <div class="form-group">
                    <label for="notes">Notes</label>
                    <textarea id="notes" name="notes" placeholder="Any extra notes..."><?= htmlspecialchars($_POST['notes'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label>Difficulty Level</label>
                    <div class="difficulty-btn-container">
                        <button type="button" class="difficulty-btn btn-easy">
                            <i class="fas fa-check check-icon"></i>
                            Easy
                        </button>
                        <button type="button" class="difficulty-btn btn-medium">
                            <i class="fas fa-check check-icon"></i>
                            Medium
                        </button>
                        <button type="button" class="difficulty-btn btn-difficult">
                            <i class="fas fa-check check-icon"></i>
                            Difficult
                        </button>
                    </div>
                    <input type="hidden" name="difficulty" id="difficultyInput" value="<?= htmlspecialchars($_POST['difficulty'] ?? 'Medium') ?>">
                </div>

                <div class="form-group">
                    <label for="question">Question</label>
                    <input type="text" id="question" name="question" placeholder="Enter question..." required value="<?= htmlspecialchars($_POST['question'] ?? '') ?>" />
                </div>

                <div class="form-group">
                    <label for="answer">Answer</label>
                    <textarea id="answer" name="answer" placeholder="Enter answer..." required><?= htmlspecialchars($_POST['answer'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label for="notesImageUpload">Upload Notes Image</label>
                    <input type="file" id="notesImageUpload" name="notesImageUpload" accept="image/*" onchange="updateFileName('notesImageUpload', 'imageFileName')"/>
                    <label for="notesImageUpload" class="import-btn"><i class="fas fa-plus"></i> Import</label>
                    <div id="imageFileName" class="selected-file"></div>
                </div>

                <div class="form-group">
                    <label for="notesPdfUpload">Upload Notes PDF</label>
                    <input type="file" id="notesPdfUpload" name="notesPdfUpload" accept="application/pdf, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/vnd.ms-powerpoint, application/vnd.openxmlformats-officedocument.presentationml.presentation" onchange="updateFileName('notesPdfUpload', 'pdfFileName')" />
                    <label for="notesPdfUpload" class="import-btn"><i class="fas fa-plus"></i> Import</label>
                    <div id="pdfFileName" class="selected-file"></div>
                </div>

                <button type="submit" name="save">Save Flashcard</button>
            </form>
        </div>

        <script>
            // Difficulty button selection functionality
            const difficultyButtons = document.querySelectorAll('.difficulty-btn');
            const hiddenInput = document.getElementById('difficultyInput');

            difficultyButtons.forEach(button => {
                button.addEventListener('click', function () {
                    difficultyButtons.forEach(btn => btn.classList.remove('selected'));
                    this.classList.add('selected');
                    hiddenInput.value = this.textContent.trim();
                });
            });

            // Set initial difficulty selection
            window.onload = function() {
                const hiddenInput = document.getElementById("difficultyInput");
                const currentDifficulty = hiddenInput.value || 'Medium';
                const buttons = {
                    'Easy': document.querySelector('.btn-easy'),
                    'Medium': document.querySelector('.btn-medium'),
                    'Difficult': document.querySelector('.btn-difficult')
                };
                
                if (buttons[currentDifficulty]) {
                    buttons[currentDifficulty].click();
                }
            };

            // File name display
            function updateFileName(inputId, fileNameId) {
                var input = document.getElementById(inputId);
                var fileName = input.files[0] ? input.files[0].name : "No file selected";
                document.getElementById(fileNameId).textContent = fileName;
            };

            // Form validation
            document.querySelector('form').addEventListener('submit', function(e) {
                const difficulty = document.getElementById('difficultyInput').value;
                const question = document.getElementById('question').value;
                const answer = document.getElementById('answer').value;
                
                if (!difficulty) {
                    e.preventDefault();
                    alert('Please select a difficulty level');
                    return;
                }
                
                if (!question || !answer) {
                    e.preventDefault();
                    alert('Question and Answer are required fields');
                }
            });
        </script>
    </body>
    </html>