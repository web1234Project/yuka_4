<?php
include '../common/config.php';
session_start();
$user_id = $_SESSION['user_id'];

// Fetch the subjects for the user
$subjectsQuery = "
  SELECT s.id, s.subject_name 
  FROM subjects s
  WHERE s.user_id = ?
    AND EXISTS (
      SELECT 1 FROM quizzes sc WHERE sc.subject_id = s.id AND sc.user_id = ?
    )
  ORDER BY s.subject_name ASC
";
$stmt = $conn->prepare($subjectsQuery);
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$subjectsResult = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Your Subjects - RecallIt</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #121212;
      color: #f8f6f6;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .subjects-container {
      background-color: #1a1a1a;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 2px 15px rgba(18, 239, 247, 0.849);
      width: 80%;
      text-align: center;
    }

    h1 {
      margin-bottom: 20px;
      color: #00f7ff;
    }

    .subject-link {
      display: block;
      padding: 12px;
      background-color: #00d4ff;
      color: #000;
      margin: 10px 0;
      border-radius: 5px;
      text-decoration: none;
      font-weight: 500;
      transition: all 0.3s ease;
    }

    .subject-link:hover {
      background-color: #02a8c2;
      transform: translateY(-2px);
    }

    .back-link {
      display: block;
      margin-top: 20px;
      color: #00f7ff;
      text-decoration: none;
      font-size: 16px;
      transition: color 0.3s ease;
    }

    .back-link:hover {
      color: #02c6d2;
      text-decoration: underline;
    }
  </style>
</head>
<body>

  <div class="subjects-container">
    <h1>Your Subjects</h1>

    <?php if ($subjectsResult->num_rows > 0): ?>
      <?php while ($row = $subjectsResult->fetch_assoc()): ?>
        <a href="progress.php?subject_id=<?php echo $row['id']; ?>" class="subject-link">
          <?php echo htmlspecialchars($row['subject_name']); ?>
        </a>
      <?php endwhile; ?>
    <?php else: ?>
      <p>No subjects found. Please add a subject first.</p>
    <?php endif; ?>

    <a href="user-dashboard.php" class="back-link">Back to Dashboard</a>
  </div>

</body>
</html>
