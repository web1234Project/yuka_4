<?php
// Include the database configuration file to establish connection
include '../common/config.php';

// Check if 'file' parameter exists in the URL
if (isset($_GET['file'])) {
    // Get the filename and sanitize it to prevent directory traversal
    $fileName = basename($_GET['file']);
    // Set the full file path relative to this script
    $filePath = '../uploads/' . $fileName;
    
    // Verify the file exists on the server
    if (file_exists($filePath)) {
        // Determine the file type using MIME detection
        $mimeType = mime_content_type($filePath);
        
        // Check if file is viewable in browser (images or PDF)
        if (strpos($mimeType, 'image/') === 0 || 
            $mimeType == 'application/pdf') {
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Basic meta tags for character set and responsive viewport -->
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Dynamic page title showing filename -->
  <title>Viewing <?php echo htmlspecialchars($fileName); ?> - RecallIt</title>
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    /* CSS Reset */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    /* Main page styling */
    body {
      background-color: #121212; /* Dark background */
      color: #f8f6f6; /* Light text */
      height: 100vh; /* Full viewport height */
      display: flex;
      flex-direction: column; /* Vertical layout */
    }

    /* Header styling */
    .viewer-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px 20px;
      background-color: #1a1a1a; /* Slightly lighter than body */
      box-shadow: 0 2px 10px rgba(0, 247, 255, 0.1); /* Teal glow */
    }

    /* Logo area styling */
    .logo-section {
      display: flex;
      align-items: center;
    }

    .logo {
      width: 30px;
      height: 30px;
      margin-right: 10px;
    }

    .logo-name {
      font-size: 20px;
      color: #00f7ff; /* Teal accent */
      font-weight: bold;
    }

    /* Back button styling */
    .back-btn {
      background-color: #00f7ff; /* Teal */
      color: #121212; /* Dark text */
      padding: 8px 15px;
      border-radius: 5px;
      text-decoration: none;
      font-weight: 500;
      transition: all 0.3s ease; /* Smooth hover effects */
      display: flex;
      align-items: center;
      gap: 5px;
    }

    .back-btn:hover {
      background-color: #02c6d2; /* Darker teal on hover */
      transform: translateY(-2px); /* Lift effect */
    }

    /* Main content container */
    .file-viewer-container {
      flex: 1; /* Take remaining space */
      padding: 20px;
      display: flex;
      flex-direction: column;
    }

    /* File metadata section */
    .file-meta {
      margin-bottom: 15px;
      padding: 10px;
      background-color: #1e1e1e; /* Dark gray */
      border-radius: 5px;
      border-left: 3px solid #00f7ff; /* Teal accent */
    }

    /* File display area */
    .file-display {
      flex: 1; /* Take maximum available space */
      background-color: #1a1a1a;
      border-radius: 8px;
      overflow: hidden; /* Prevent content overflow */
      box-shadow: 0 2px 15px rgba(18, 239, 247, 0.2); /* Teal glow */
      padding: 20px; /* Inner spacing */
    }

    /* PDF viewer iframe */
    iframe {
      width: 100%;
      height: 100%;
      border: none; /* Remove border */
    }

    /* Image styling */
    img {
      max-width: 100%;
      max-height: 80vh; /* Limit height */
      display: block;
      margin: 0 auto; /* Center horizontally */
      object-fit: contain; /* Maintain aspect ratio */
    }

    /* Action buttons container */
    .file-actions {
      margin-top: 15px;
      display: flex;
      gap: 20px; /* Space between buttons */
    }

    /* Base button styling */
    .action-btn {
      background-color: #00d4ff; /* Light teal */
      color: #000;
      border: none;
      padding: 8px 15px;
      border-radius: 5px;
      cursor: pointer;
      font-weight: 500;
      transition: all 0.3s ease;
      text-decoration: none;
      font-size: 14px;
    }

    .action-btn:hover {
      background-color: #02a8c2; /* Darker teal */
    }

    /* Download button specific styling */
    .download-btn {
      background-color: #4CAF50; /* Green */
    }

    .download-btn:hover {
      background-color: #3e8e41; /* Darker green */
    }
  </style>
</head>
<body>
  <!-- Page header with logo and back button -->
  <header class="viewer-header">
    <div class="logo-section">
      <img src="logo.png" alt="RecallIt Logo" class="logo">
      <span class="logo-name">RecallIt</span>
    </div>
    <a href="view-page.php" class="back-btn">
      <i class="fas fa-arrow-left"></i> Back to Materials
    </a>
  </header>

  <!-- Main content area -->
  <main class="file-viewer-container">
    <!-- File metadata section -->
    <div class="file-meta">
      <!-- Display filename (sanitized for security) -->
      <h3><?php echo htmlspecialchars($fileName); ?></h3>
      <!-- Display file extension in uppercase -->
      <p>Type: <?php echo strtoupper(pathinfo($fileName, PATHINFO_EXTENSION)); ?></p>
    </div>

    <!-- File display area -->
    <div class="file-display">
      <?php if (strpos($mimeType, 'image/') === 0): ?>
        <!-- Display image files -->
        <img src="../uploads/<?php echo htmlspecialchars($fileName); ?>" alt="<?php echo htmlspecialchars($fileName); ?>">
      <?php elseif ($mimeType == 'application/pdf'): ?>
        <!-- Display PDF files with native toolbar -->
        <iframe src="../uploads/<?php echo htmlspecialchars($fileName); ?>#toolbar=1"></iframe>
      <?php endif; ?>
    </div>

    <!-- Action buttons -->
    <div class="file-actions">
      <!-- Download button with direct file link -->
      <a href="../uploads/<?php echo htmlspecialchars($fileName); ?>" download class="action-btn download-btn">
        <i class="fas fa-download"></i> Download
      </a>
    </div>
  </main>
</body>
</html>
<?php
            exit; // Stop further execution after displaying the viewer
        } else {
            // Handle non-viewable files (like .doc, .zip) with forced download
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $fileName . '"');
            header('Content-Length: ' . filesize($filePath));
            readfile($filePath); // Output the file contents
            exit;
        }
    }
}

// If file not found, redirect with error message
header("Location: view-page.php?error=File not found");
exit;