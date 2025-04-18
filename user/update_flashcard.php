<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once '../common/config.php';

// Check if an ID is provided in the URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $flashcardId = $_GET['id'];
    $userId = $_SESSION['user_id'];

    // Fetch the flashcard data
    $sql = "SELECT * FROM flashcards WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ii", $flashcardId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $flashcard = $result->fetch_assoc();

    if (!$flashcard) {
        header("Location: my-flashcard.php");
        exit;
    }
} else {
    header("Location: subjects.php");
    exit;
}

// Handle form submission
if (isset($_POST['update_flashcard'])) {
    $question = $_POST['question'];
    $answer = $_POST['answer'];
    $uploadError = null;
    
    // Initialize with existing paths
    $imagePath = $flashcard['image_path'];
    $pdfPath = $flashcard['pdf_path'];

    // Handle image upload
    if (!empty($_FILES['image']['name'])) {
        $uploadResult = handleFileUpload($_FILES['image'], 'images');
        if ($uploadResult === false) {
            $uploadError = "Image upload failed. Please use JPG, PNG or GIF under 5MB.";
        } else {
            // Delete old image if exists
            if (!empty($imagePath)) {
                $oldImagePath = $_SERVER['DOCUMENT_ROOT'] . '/RECALLIT1/yuka_4/' . $imagePath;
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            $imagePath = $uploadResult;
        }
    }

    // Handle PDF upload
    if (!empty($_FILES['pdf']['name'])) {
        $uploadResult = handleFileUpload($_FILES['pdf'], 'files');
        if ($uploadResult === false) {
            $uploadError = "File upload failed. Please use PDF, DOC or PPT under 5MB.";
        } else {
            // Delete old PDF if exists
            if (!empty($pdfPath)) {
                $oldPdfPath = $_SERVER['DOCUMENT_ROOT'] . '/RECALLIT1/yuka_4/' . $pdfPath;
                if (file_exists($oldPdfPath)) {
                    unlink($oldPdfPath);
                }
            }
            $pdfPath = $uploadResult;
        }
    }

    if (!$uploadError) {
        // Update database
        $sqlUpdate = "UPDATE flashcards SET question = ?, answer = ?, image_path = ?, pdf_path = ? WHERE id = ? AND user_id = ?";
        $stmtUpdate = $conn->prepare($sqlUpdate);
        
        if ($stmtUpdate) {
            $stmtUpdate->bind_param("ssssii", $question, $answer, $imagePath, $pdfPath, $flashcardId, $userId);
            
            if ($stmtUpdate->execute()) {
                $_SESSION['success'] = "";
                header("Location: my-flashcard.php?subject=" . urlencode($flashcard['subject']));
                exit;
            } else {
                $updateError = "Error saving changes: " . $stmtUpdate->error;
            }
            $stmtUpdate->close();
        } else {
            $updateError = "Database error: " . $conn->error;
        }
    }
}

function handleFileUpload($file, $type) {
    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/RECALLIT1/yuka_4/uploads/' . $type . '/';
    
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $uniqueFilename = uniqid() . '.' . $fileExtension;
    $targetPath = $uploadDir . $uniqueFilename;

    $allowedTypes = ($type === 'images') ? 
        ['jpg', 'jpeg', 'png', 'gif'] : 
        ['pdf', 'doc', 'docx', 'ppt', 'pptx'];

    if ($file['error'] !== UPLOAD_ERR_OK) return false;
    if (!in_array($fileExtension, $allowedTypes)) return false;
    if ($file['size'] > (5 * 1024 * 1024)) return false;

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return 'uploads/' . $type . '/' . $uniqueFilename;
    }
    
    return false;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Flashcard - RecallIt</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background: #0e0e10;
            color: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 60px 20px;
            box-sizing: border-box;
            min-height: 100vh;
        }

        .update-container {
            background: #1a1a1a;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 212, 255, 0.3);
            width: 95%;
            max-width: 600px;
        }

        h2 {
            color: #00d4ff;
            text-align: center;
            margin-bottom: 30px;
            font-size: 2rem;
            font-weight: bold;
        }

        .form-group {
            margin-bottom: 25px;
        }

        label {
            display: block;
            color: #eee;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input[type="text"],
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #333;
            border-radius: 8px;
            background: #2c2c3e;
            color: #fff;
            box-sizing: border-box;
            font-size: 1rem;
        }

        textarea {
            resize: vertical;
            min-height: 120px;
        }

        input[type="file"] {
            color: #ddd;
            padding: 8px 0;
            font-size: 1rem;
        }

        input[type="file"]::file-selector-button {
            background-color: #333;
            color: #eee;
            border: none;
            padding: 10px 15px;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-right: 10px;
        }

        input[type="file"]::file-selector-button:hover {
            background-color: #444;
        }

        .current-file {
            color: #aaa;
            font-size: 0.9rem;
            margin-top: 5px;
            font-style: italic;
        }

        .button-group {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 35px;
        }

        .update-button,
        .cancel-button {
            background-color: #00f7ff;
            color: #0e0e10;
            border: none;
            padding: 14px 30px;
            border-radius: 10px;
            cursor: pointer;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }

        .cancel-button {
            background-color: #444;
            color: #eee;
        }

        .update-button:hover {
            background-color: #00e5ff;
            transform: translateY(-2px);
        }

        .cancel-button:hover {
            background-color: #555;
            transform: translateY(-2px);
        }

    

        .error-message {
            color: #ff6b6b;
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 6px;
            background-color: rgba(255, 107, 107, 0.1);
        }
    </style>
</head>
<body>
    <div class="update-container">
        <h2>Update Flashcard</h2>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="success-message">
                <i class="fas fa-check-circle"></i> <?= htmlspecialchars($_SESSION['success']) ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <?php if (isset($uploadError)): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($uploadError) ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($updateError)): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($updateError) ?>
            </div>
        <?php endif; ?>

        <form action="" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="question">Question:</label>
                <textarea id="question" name="question" required><?= htmlspecialchars($flashcard['question']) ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="answer">Answer:</label>
                <textarea id="answer" name="answer" required><?= htmlspecialchars($flashcard['answer']) ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="image">Update Image (JPG, PNG, GIF):</label>
                <input type="file" id="image" name="image" accept="image/*">
                <?php if (!empty($flashcard['image_path'])): ?>
                    <p class="current-file">
                        <i class="fas fa-image"></i> Current: <?= htmlspecialchars(basename($flashcard['image_path'])) ?>
                    </p>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="pdf">Update File (PDF, DOC, PPT):</label>
                <input type="file" id="pdf" name="pdf" accept=".pdf,.doc,.docx,.ppt,.pptx">
                <?php if (!empty($flashcard['pdf_path'])): ?>
                    <p class="current-file">
                        <i class="fas fa-file"></i> Current: <?= htmlspecialchars(basename($flashcard['pdf_path'])) ?>
                    </p>
                <?php endif; ?>
            </div>
            
            <div class="button-group">
                <button type="submit" name="update_flashcard" class="update-button">
                    <i class="fas fa-save"></i> Update Flashcard
                </button>
                <a href="my-flashcard.php?subject=<?= urlencode($flashcard['subject']) ?>" class="cancel-button">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>