<?php
  include '../common/config.php';

  $upload_message = '';

  if ($_SERVER["REQUEST_METHOD"] === "POST") {
      try {
          $subject_name = trim($_POST['subject_name'] ?? '');
          $file = $_FILES['file'] ?? null;

          if (empty($subject_name)) {
              throw new Exception("Subject name is required");
          }

          if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
              throw new Exception("Please select a file to upload");
          }

          $targetDir = "uploads/";
          if (!is_dir($targetDir)) {
              mkdir($targetDir, 0777, true);
          }

          $fileName = basename($file['name']);
          $targetFile = $targetDir . $fileName;

          if (!move_uploaded_file($file['tmp_name'], $targetFile)) {
              throw new Exception("Failed to upload file");
          }

          $sql = "INSERT INTO subjects (subject_name, file_name) VALUES (?, ?)";
          $stmt = $conn->prepare($sql);

          if (!$stmt) {
              throw new Exception("Prepare failed: " . $conn->error);
          }

          $stmt->bind_param("ss", $subject_name, $fileName);

          if (!$stmt->execute()) {
              throw new Exception("Execute failed: " . $stmt->error);
          }

          header("Location: view-page.php?success=1");
          exit();
      } catch (Exception $e) {
          $upload_message = $e->getMessage(); // just the message
      }
  }
  ?>

  <!DOCTYPE html>
  <html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Upload Subject - RecallIt</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"/>
    <style>
      * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
        font-family: 'Poppins', sans-serif;
      }

      body {
        background: #0e0e10;
        color: #fff;
        min-height: 100vh;
      }

      .header {
        padding: 20px;
        background-color: #1a1a1a;
        box-shadow: 0 2px 10px rgba(0, 247, 255, 0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
      }

      .logo {
        display: flex;
        align-items: center;
      }

      .logo img {
        width: 40px;
        height: 40px;
        margin-right: 10px;
      }

      .logo-name {
        font-size: 24px;
        color: #00f7ff;
        font-weight: bold;
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
        max-width: 600px;
        margin: 40px auto;
        padding: 30px;
        background: #1a1a1a;
        border-radius: 15px;
      }

      h2 {
        color: #00f7ff;
        margin-bottom: 20px;
        text-align: center;
      }

      .form-group {
        margin-bottom: 20px;
      }

      label {
        display: block;
        margin-bottom: 8px;
        color: #ccc;
      }

      input[type="text"] {
        width: 100%;
        padding: 12px;
        background: #2c2c3e;
        border: none;
        border-radius: 8px;
        color: white;
      }

      .file-upload {
        padding: 20px;
        border: 2px dashed #00f7ff;
        border-radius: 8px;
        text-align: center;
        background: #2c2c3e;
        cursor: pointer;
      }

      .file-upload i {
        color: #00f7ff;
        font-size: 24px;
        margin-bottom: 10px;
      }

      .submit-btn {
        width: 100%;
        padding: 12px;
        background: #00f7ff;
        color: #0e0e10;
        border: none;
        border-radius: 8px;
        font-weight: bold;
        cursor: pointer;
        transition: background 0.3s ease;
      }

      .submit-btn:hover {
        background: #02c6d2;
      }

      .back-link {
        display: block;
        text-align: center;
        margin-top: 20px;
        color: #00f7ff;
        text-decoration: none;
      }

      #selected-file-name {
        margin-top: 8px;
        font-style: italic;
      }

      input[type="file"] {
        display: none;
      }

      .file-error {
        color:rgb(92, 201, 255);
        font-size: 14px;
        margin-top: 8px;
      }
      .clear-btn {
          background: transparent;
          border: none;
          color:rgb(92, 201, 255);
          cursor: pointer;
          font-size: 18px;
          padding: 5px;
          transition: color 0.3s ease;
      }

.clear-btn:hover {
          color:rgb(46, 248, 255);
      }

      .clear-btn:focus {
          outline: none;
      }
    </style>
  </head>
  <body>
    <div class="header">
      <div class="logo">
        <img src="logo.png" alt="RecallIt Logo" />
        <span class="logo-name">RecallIt</span>
      </div>
      <a href="user-dashboard.html" class="home-link">
        <i class="fas fa-home"></i>
      </a>
    </div>

    <div class="container">
      <h2>Upload New Subject</h2>

      <form id="uploadForm" action="" method="POST" enctype="multipart/form-data">
        <div class="form-group">
          <label for="subject_name">Subject Name</label>
          <input type="text" id="subject_name" name="subject_name" placeholder="Enter subject name">
          <div id="subject-error" class="file-error"></div> <!-- Added error div -->
        </div>

        <div class="form-group">
          <label for="file">Upload File</label>
          <div class="file-upload" id="fileLabel">
            <i class="fas fa-cloud-upload-alt"></i><br>
            <span id="file-label">Click to select file</span>
          </div>
          <input type="file" id="file" name="file">
          <div id="selected-file-name"></div>
          <button type="button" id="clear-file" class="clear-btn" title="Clear selection">
            <i class="fas fa-refresh"></i>
          </button>

          <!-- PHP error message here -->
          <div class="file-error" id="file-error"><?php echo !empty($upload_message) ? htmlspecialchars($upload_message) : ''; ?></div>
        </div>

        <button type="submit" class="submit-btn">Upload</button>
      </form>

      <a href="study-guide.php" class="back-link">Back to Study Guide</a>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('file');
    const fileLabel = document.getElementById('fileLabel');
    const fileNameDisplay = document.getElementById('selected-file-name');
    const fileError = document.getElementById('file-error');
    const subjectError = document.getElementById('subject-error');
    const uploadForm = document.getElementById('uploadForm');
    const subjectInput = document.getElementById('subject_name');
    const clearFileBtn = document.getElementById('clear-file'); // New clear button reference
    const fileLabelText = document.getElementById('file-label'); // Reference to the label text

    // Function to clear file selection
    function clearFileSelection() {
        fileInput.value = ''; // Clear the file input
        fileNameDisplay.textContent = ''; // Clear the displayed file name
        fileLabelText.textContent = 'Click to select file'; // Reset label text
        fileError.textContent = ''; // Clear any file errors
        fileLabel.style.borderColor = '#00f7ff'; // Reset border color
    }

    // Show PHP errors on page load
    if (fileError.textContent.trim() !== '') {
        fileLabel.style.borderColor = '#ff5c5c';
    }

    fileLabel.addEventListener('click', () => fileInput.click());

    // Clear button event listener
    clearFileBtn.addEventListener('click', clearFileSelection);

    fileInput.addEventListener('change', function () {
        if (this.files.length > 0) {
            fileNameDisplay.textContent = "Selected file: " + this.files[0].name;
            fileLabelText.textContent = this.files[0].name; // Update label with filename
            fileError.textContent = "";
            fileLabel.style.borderColor = '#00f7ff';
        }
    });

    uploadForm.addEventListener('submit', function (e) {
        let isValid = true;
        
        // Clear previous errors
        fileError.textContent = "";
        subjectError.textContent = "";
        fileLabel.style.borderColor = '#00f7ff';
        subjectInput.style.border = 'none';

        // Validate subject name
        if (subjectInput.value.trim() === '') {
            isValid = false;
            subjectInput.style.border = '1px solid rgb(94, 225, 164,1.85)';
            subjectError.textContent = "Subject name is required";
        }

        // Validate file
        if (fileInput.files.length === 0) {
            isValid = false;
            fileError.textContent = "Please select a file to upload";
            fileLabel.style.borderColor = 'rgb(94, 225, 164,1.85)';
        }

        if (!isValid) {
            e.preventDefault();
            // Scroll to the first error
            const firstError = subjectError.textContent ? subjectError : 
                             (fileError.textContent ? fileError : null);
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    });

    // Clear error when user starts typing
    subjectInput.addEventListener('input', function() {
        if (this.value.trim() !== '') {
            subjectError.textContent = "";
            this.style.border = 'none';
        }
    });
});
  </script>
  </body>
  </html>