<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../common/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$flashcard_id = $_GET['id'];

// Initialize flashcard with default values
$flashcard = [
    'question' => '',
    'answer' => '',
    'image_path' => '',
    'pdf_path' => '',
    'subject' => ''
];

// Check if this is a shared flashcard with edit permissions
$is_shared = false;
$original_flashcard_id = null;
$owner_id = null;
$has_edit_permission = false;

// First check if user owns the flashcard directly
$stmt = $conn->prepare("SELECT * FROM flashcards WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $flashcard_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$flashcard = $result->fetch_assoc();
$stmt->close();

if ($flashcard) {
    // User is the owner - has full permissions
    $has_edit_permission = true;
} else {
    // Check if it's a shared flashcard
    $shared_stmt = $conn->prepare("
        SELECT sf.*, f.user_id as owner_id 
        FROM shared_flashcards sf
        JOIN flashcards f ON sf.flashcard_id = f.id
        WHERE sf.recipient_flashcard_id = ?
        AND sf.recipient_id = ?
        AND sf.status = 'Accepted'
    ");
    $shared_stmt->bind_param("ii", $flashcard_id, $user_id);
    $shared_stmt->execute();
    $shared_info = $shared_stmt->get_result()->fetch_assoc();
    $shared_stmt->close();

    if ($shared_info) {
        $is_shared = true;
        $original_flashcard_id = $shared_info['flashcard_id'];
        $owner_id = $shared_info['owner_id'];
        $has_edit_permission = ($shared_info['permissions'] == 'edit');
        
        // Get the recipient's copy of the flashcard
        $stmt = $conn->prepare("SELECT * FROM flashcards WHERE id = ?");
        $stmt->bind_param("i", $flashcard_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $flashcard = $result->fetch_assoc();
        $stmt->close();
    }
}

// If no flashcard found or no edit permission, redirect
if (!$flashcard || (!$has_edit_permission && $is_shared)) {
    $_SESSION['error'] = "You don't have permission to edit this flashcard";
    header("Location: my-flashcard.php");
    exit;
}

// Handle form submission
if (isset($_POST['update_flashcard'])) {
    $question = $_POST['question'];
    $answer = $_POST['answer'];
    $uploadError = null;
    
    // Initialize with existing paths (user's own files)
    $imagePath = $flashcard['image_path'];
    $pdfPath = $flashcard['pdf_path'];

    // Handle image upload - only updates user's copy
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

    // Handle PDF upload - only updates user's copy
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
        // Start transaction if this is a shared flashcard
        if ($is_shared) {
            $conn->begin_transaction();
        }

        try {
            $changes_made = false;
            
            // Check if content has changed
            $content_changed = ($question != $flashcard['question'] || $answer != $flashcard['answer']);
            $image_changed = (!empty($_FILES['image']['name']));
            $pdf_changed = (!empty($_FILES['pdf']['name']));
            
            if ($content_changed || $image_changed || $pdf_changed) {
                // Update the current flashcard (user's copy)
                $sqlUpdate = "UPDATE flashcards SET question = ?, answer = ?, image_path = ?, pdf_path = ? WHERE id = ?";
                $stmtUpdate = $conn->prepare($sqlUpdate);
                $stmtUpdate->bind_param("ssssi", $question, $answer, $imagePath, $pdfPath, $flashcard_id);
                $stmtUpdate->execute();
                
                if ($stmtUpdate->affected_rows > 0) {
                    $changes_made = true;
                }
                
                if ($is_shared && $has_edit_permission && $content_changed) {
                    // For shared flashcards with edit permission, update the original
                    $stmtUpdateOriginal = $conn->prepare("UPDATE flashcards SET question = ?, answer = ? WHERE id = ?");
                    $stmtUpdateOriginal->bind_param("ssi", $question, $answer, $original_flashcard_id);
                    $stmtUpdateOriginal->execute();
                    
                    if ($stmtUpdateOriginal->affected_rows > 0) {
                        // Notify the owner of the change
                        $message = "Your shared flashcard was updated by " . $_SESSION['username'];
                        $stmtNotify = $conn->prepare("INSERT INTO notifications (user_id, message, flashcard_id, status) VALUES (?, ?, ?, 'Unread')");
                        $stmtNotify->bind_param("isi", $owner_id, $message, $original_flashcard_id);
                        $stmtNotify->execute();
                    }
                    
                    $conn->commit();
                }
            }

            if ($changes_made) {
                $_SESSION['success'] = "Flashcard updated successfully";
            } else {
                $_SESSION['info'] = "No changes were made to the flashcard";
            }
            header("Location: my-flashcard.php?subject=" . urlencode($flashcard['subject']));
            exit;
        } catch (Exception $e) {
            if ($is_shared) {
                $conn->rollback();
            }
            $_SESSION['error'] = "Error saving changes: " . $e->getMessage();
            header("Location: update_flashcard.php?id=$flashcard_id");
            exit;
        }
    }
}

function handleFileUpload($file, $type) {
    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/RECALLIT1/yuka_4/uploads/' . $type . '/';
    
    if (!file_exists($uploadDir) && !mkdir($uploadDir, 0777, true)) {
        error_log("Failed to create directory: $uploadDir");
        return false;
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

        .success-message {
            color: #00ff00;
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 6px;
            background-color: rgba(0, 255, 0, 0.1);
        }

        .error-message {
            color: #ff6b6b;
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 6px;
            background-color: rgba(255, 107, 107, 0.1);
        }

        .info-message {
            color: #00d4ff;
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 6px;
            background-color: rgba(0, 212, 255, 0.1);
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
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($_SESSION['error']) ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['info'])): ?>
            <div class="info-message">
                <i class="fas fa-info-circle"></i> <?= htmlspecialchars($_SESSION['info']) ?>
            </div>
            <?php unset($_SESSION['info']); ?>
        <?php endif; ?>
        
        <?php if (isset($uploadError)): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($uploadError) ?>
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
if (isset($conn) && $conn) {
    $conn->close();
}