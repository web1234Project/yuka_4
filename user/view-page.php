<?php
include '../common/config.php';

$upload_message = '';

// Handle file deletion
if (isset($_GET['delete'])) {
  $id = intval($_GET['delete']); // Sanitize input

  // Get file name to delete it from folder
  $getFileQuery = "SELECT file_name FROM subjects WHERE id = ?";
  $stmt = $conn->prepare($getFileQuery);
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result && $result->num_rows > 0) {
      $row = $result->fetch_assoc();
      $filePath = '../uploads/' . $row['file_name'];
      
      // Check if it's a file and delete it
      if (file_exists($filePath) && is_file($filePath)) {
          unlink($filePath); // Delete the file
      }

      // Delete record from database
      $deleteQuery = "DELETE FROM subjects WHERE id = ?";
      $stmt = $conn->prepare($deleteQuery);
      $stmt->bind_param("i", $id);
      $stmt->execute();
      
      $upload_message = "File deleted successfully!";
  }
}

// Fetch all files
$sql = "SELECT * FROM subjects ORDER BY uploaded_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>View Study Materials - RecallIt</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      width: 100vw;
      min-height: 100vh;
      background-color: #121212;
      color: #f8f6f6;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .view-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 20px;
      background-color: #1a1a1a;
      box-shadow: 0 2px 10px rgba(0, 247, 255, 0.1);
      width: 100%;
      position: sticky;
      top: 0;
      z-index: 100;
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
      color: #00f7ff;
      font-weight: bold;
    }

    .home-link {
      font-size: 24px;
      color: #00f7ff;
      text-decoration: none;
      transition: color 0.3s ease;
    }

    .home-link:hover {
      color: #02c6d2;
    }

    .table-container {
      width: 90%;
      max-width: 1000px;
      background-color: #1a1a1a;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 2px 15px rgba(18, 239, 247, 0.849);
      margin: 30px auto;
    }

    .message {
      color: #00f7ff;
      text-align: center;
      margin-bottom: 20px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    th, td {
      padding: 12px;
      text-align: center;
      border-bottom: 1px solid #00f7ff;
    }

    th {
      background-color: #26262c;
      color: #00f7ff;
      font-size: 18px;
      text-transform: uppercase;
    }

    td {
      background-color: #1e1e1e;
      color: #ffffff;
    }

    .empty-message {
      color: #ccc;
      font-size: 18px;
      margin-top: 20px;
      text-align: center;
    }

    .action-btn {
      background-color: #00d4ff;
      color: #000;
      border: none;
      padding: 8px 15px;
      margin: 0 5px;
      border-radius: 5px;
      cursor: pointer;
      font-weight: 500;
      transition: all 0.3s ease;
      text-decoration: none;
      display: inline-block;
    }

    .action-btn:hover {
      background-color: #02a8c2;
      transform: translateY(-2px);
    }

    .delete-btn {
      background-color:  #00d4ff;
    }

    .delete-btn:hover {
      background-color: #02a8c2;
    }

    .back-section {
      margin: 20px 0;
      text-align: center;
    }

    .back-link {
      color: #00f7ff;
      text-decoration: none;
      font-size: 16px;
      transition: color 0.3s ease;
    }

    .back-link:hover {
      color: #02c6d2;
      text-decoration: underline;
    }

    @media (max-width: 768px) {
      .table-container {
        width: 95%;
        padding: 15px;
      }
      
      th, td {
        padding: 8px;
        font-size: 14px;
      }
      
      .action-btn {
        padding: 5px 10px;
        margin: 2px;
      }
    }
  </style>
</head>
<body>

  <!-- Header -->
  <div class="view-header">
    <div class="logo-section">
      <img src="logo.png" alt="RecallIt Logo" class="logo">
      <span class="logo-name">RecallIt</span>
    </div>
    <div class="home-section">
      <a href="user-dashboard.php" class="home-link">
        <i class="fas fa-home"></i>
      </a>
    </div>
  </div>

  <!-- Table Container -->
  <div class="table-container">
    <h2 style="text-align: center; color: #00f7ff; margin-bottom: 20px;">Study Materials</h2>
    
    <?php if (!empty($upload_message)): ?>
      <div class="message"><?php echo $upload_message; ?></div>
    <?php endif; ?>

    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Subject</th>
          <th>File Name</th>
          <th>Type</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result->num_rows > 0): ?>
          <?php $index = 1; ?>
          <?php while($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?php echo $index++; ?></td>
              <td><?php echo htmlspecialchars($row['subject_name']); ?></td>
              <td><?php echo htmlspecialchars($row['file_name']); ?></td>
              <td><?php echo strtoupper(pathinfo($row['file_name'], PATHINFO_EXTENSION)); ?></td>
              <td>
              <a href="view_file.php?file=<?php echo urlencode($row['file_name']); ?>" class="action-btn">View</a>
              <form method="get" style="display: inline;">
                <input type="hidden" name="delete" value="<?php echo $row['id']; ?>" />
                <button type="submit" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this file?')">Delete</button>
              </form>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="5" class="empty-message">No study materials found. Upload some files to get started!</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <div class="back-section">
    <a href="study-guide.php" class="back-link">
      <i class="fas fa-arrow-left"></i> Back to Study Guide
    </a>
  </div>

  
</body>
</html>