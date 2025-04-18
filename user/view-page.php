<?php
  include '../common/config.php';

  $upload_message = '';

// Handle file deletion
if (isset($_GET['delete'])) {
  $id = $_GET['delete'];

  // Get file name to delete it from folder
  $getFileQuery = "SELECT file_name FROM subjects WHERE id = $id";
  $result = $conn->query($getFileQuery);
  if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $filePath = 'uploads/' . $row['file_name'];
    if (file_exists($filePath)) {
      unlink($filePath); // delete file from folder
    }

    // delete row from database
    $deleteQuery = "DELETE FROM subjects WHERE id = $id";
    $conn->query($deleteQuery);
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
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      width: 100vw;
      height: 100vh;
      background-color: #121212;
      color: #f8f6f6;
      font-family: 'Poppins', sans-serif;
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

    .home-section .home-link {
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
      text-align: center;
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
    }

    .back-link {
      font-size: 14px;
      color: #00f7ff;
      text-decoration: none;
      transition: color 0.3s ease;
    }

    .back-link:hover {
      color: #02c6d2;
    }

    .action-btn {
      background-color: #00d4ff;
      color: #000;
      border: none;
      padding: 6px 12px;
      margin: 2px;
      border-radius: 5px;
      cursor: pointer;
      font-weight: 500;
      transition: background-color 0.3s ease;
    }

    .action-btn:hover {
      background-color: #02a8c2;
    }

    .back-section {
      margin-bottom: 30px;
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

  <!-- Table -->
  <div class="table-container">
    <h2>Study Materials</h2>
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Subject</th>
          <th>File Name</th>
          <th>Type</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php
          if ($result->num_rows > 0) {
            $index = 1;
            while($row = $result->fetch_assoc()) {
              echo "<tr>";
              echo "<td>" . $index++ . "</td>";
              echo "<td>" . htmlspecialchars($row['subject_name']) . "</td>";
              echo "<td>" . htmlspecialchars($row['file_name']) . "</td>";
              echo "<td>" . pathinfo($row['file_name'], PATHINFO_EXTENSION) . "</td>";
              echo "<td>
                      <form method='get' action='uploads/" . urlencode($row['file_name']) . "' target='_blank' style='display:inline-block;'>
                        <button type='submit' class='action-btn'>View</button>
                      </form>
                      <form method='get' onsubmit='return confirm(\"Are you sure you want to delete this file?\")' style='display:inline-block;'>
                        <input type='hidden' name='delete' value='" . (int)$row['id'] . "' />
                        <button type='submit' class='action-btn'>Delete</button>
                      </form>
                    </td>";
              echo "</tr>";
            }
          } else {
            echo "<tr><td colspan='5' class='empty-message'>No files added yet!</td></tr>";
          }
        ?>
      </tbody>
    </table>
  </div>

  <div class="back-section">
    <a href="study-guide.php" class="back-link">Back to Study Guide</a>
  </div>

</body>
</html>
