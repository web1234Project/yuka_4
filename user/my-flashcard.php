<?php
// Start session
session_start();

// Database connection
$host = 'localhost';
$db = 'recallit_db';
$user = 'root'; // your DB username
$pass = '';     // your DB password

$conn = new mysqli("localhost", "root", "", "recallit_db");

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Fetch flashcards for logged-in user
$userId = $_SESSION['user_id'] ?? 1; // fallback to user 1 for now
$sql = "SELECT * FROM flashcards WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$flashcards = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>My Flashcards - RecallIt</title>
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


    .container {
      margin: 120px auto 40px auto;
      width: 90%;
      max-width: 1100px;
      background: #1a1a1a;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0, 212, 255, 0.2);
      text-align: center;
      min-height: calc(100vh - 180px);
    }

    h2 {
      margin-bottom: 20px;
      font-size: 24px;
      color: #00d4ff;
    }

    .flashcard-container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      padding-bottom: 20px;
    }

    .flashcard {
      width: 100%;
      max-width: 1700px;
      background: #1f1f2e;
      border-radius: 12px;
      padding: 40px 30px;
      position: relative;
      box-shadow: 0 4px 10px rgba(0, 212, 255, 0.2);
    }

    .flashcard h3 {
      margin: 0 0 10px;
      font-size: 18px;
      color: #00d4ff;
    }

    .flashcard p {
      font-size: 14px;
      margin-bottom: 8px;
      color: #ccc;
    }

    .flashcard .actions {
      position: absolute;
      top: 15px;
      right: 15px;
      display: flex;
      gap: 10px;
    }

    .actions i {
      cursor: pointer;
      color: #00d4ff;
      transition: 0.3s;
    }

    .actions i:hover {
      color: red;
    }

    .no-flashcards {
      text-align: center;
      font-size: 18px;
      color: #888;
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

<div class="container">
  <h2>My Flashcards</h2>

  <?php if (count($flashcards) > 0): ?>
    <div class="flashcard-container">
      <?php foreach ($flashcards as $card): ?>
        <div class="flashcard">
          <div class="actions">
            <i class="fas fa-edit" title="Edit"></i>
            <i class="fas fa-trash-alt" title="Delete"></i>
          </div>
          <h3><?= htmlspecialchars($card['subject']) ?></h3>
          <p><strong>Created:</strong> <?= htmlspecialchars(date("d M Y, h:i A", strtotime($card['created_at']))) ?></p>
          <p><strong>Notes:</strong> <?= htmlspecialchars($card['notes'] ?: 'No notes added') ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <p class="no-flashcards">No flashcards created yet.</p>
  <?php endif; ?>
</div>

</body>
</html>
