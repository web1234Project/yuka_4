<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Create Flashcard - RecallIt</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  <style>
    /* [Your Original CSS – No changes made] */
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
      width: 450%;
      max-width: 1700px;
      background: #1a1a1a;
      padding: 70px 30px;
      border-radius: 15px;
      box-shadow: 0 0 20px rgba(0, 212, 255, 0.2);
      margin: 40px auto;
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
      min-height: 80px;
    }
    input[type="file"] {
      display: none;
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
      gap: 2px;
      justify-content: center;
      margin-top: 10px;
    }
    .difficulty-btn {
      padding: 10px 20px;
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
      padding: 12px 24px;
      background: linear-gradient(135deg, #0072ff, #00d4ff);
      border: none;
      border-radius: 12px;
      color: #fff;
      font-weight: bold;
      font-size: 16px;
      cursor: pointer;
      transition: 0.3s ease;
      display: block;
      margin: 20px auto 0;
    }
    button:hover {
      opacity: 0.9;
    }
  </style>
</head>
<body>

<?php
// PHP starts here
$host = "localhost";
$user = "root";
$password = "";
$dbname = "recallit";

$conn = new mysqli("localhost", "root", "", "recallit_db");
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $subject = $_POST["subject"] ?? '';
  $notes = $_POST["notes"] ?? '';
  $question = $_POST["question"] ?? '';
  $answer = $_POST["answer"] ?? '';
  $difficulty = $_POST["difficulty"] ?? '';
  
  $imagePath = "";
  $pdfPath = "";

  if (isset($_FILES['notesImageUpload']) && $_FILES['notesImageUpload']['error'] === UPLOAD_ERR_OK) {
    $imagePath = "uploads/" . basename($_FILES['notesImageUpload']['name']);
    move_uploaded_file($_FILES['notesImageUpload']['tmp_name'], $imagePath);
  }

  if (isset($_FILES['notesPdfUpload']) && $_FILES['notesPdfUpload']['error'] === UPLOAD_ERR_OK) {
    $pdfPath = "uploads/" . basename($_FILES['notesPdfUpload']['name']);
    move_uploaded_file($_FILES['notesPdfUpload']['tmp_name'], $pdfPath);
  }

  $stmt = $conn->prepare("INSERT INTO flashcards (subject, notes, difficulty, question, answer, image_path, pdf_path) VALUES (?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("sssssss", $subject, $notes, $difficulty, $question, $answer, $imagePath, $pdfPath);

  if ($stmt->execute()) {
    echo "<script>alert('Flashcard saved successfully!');</script>";
  } else {
    echo "<script>alert('Error saving flashcard');</script>";
  }

  $stmt->close();
}
?>

  <!-- Header -->
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

  <!-- Flashcard Form -->
  <div class="container">
    <form method="post" enctype="multipart/form-data">
      <div class="form-group">
        <label for="subject">Subject</label>
        <input type="text" id="subject" name="subject" placeholder="Enter subject..." />
      </div>

      <div class="form-group">
        <label for="notes">Notes</label>
        <textarea id="notes" name="notes" placeholder="Any extra notes..."></textarea>
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
        <input type="hidden" name="difficulty" id="difficultyInput">
      </div>

      <div class="form-group">
        <label for="question">Question</label>
        <input type="text" id="question" name="question" placeholder="Enter question..." />
      </div>

      <div class="form-group">
        <label for="answer">Answer</label>
        <textarea id="answer" name="answer" placeholder="Enter answer..."></textarea>
      </div>

      <div class="form-group">
        <label for="notesImageUpload">Upload Notes Image</label>
        <input type="file" id="notesImageUpload" name="notesImageUpload" accept="image/*" />
        <label for="notesImageUpload" class="import-btn"><i class="fas fa-plus"></i> Import</label>
      </div>

      <div class="form-group">
        <label for="notesPdfUpload">Upload Notes PDF</label>
        <input type="file" id="notesPdfUpload" name="notesPdfUpload" accept="application/pdf" />
        <label for="notesPdfUpload" class="import-btn"><i class="fas fa-plus"></i> Import</label>
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
  </script>
</body>
</html>
