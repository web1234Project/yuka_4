<?php
include '../common/config.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
$user_id = $_SESSION['user_id'];

// Get the subject ID from the query parameter
$subject_id = isset($_GET['subject_id']) ? intval($_GET['subject_id']) : 0;
if ($subject_id <= 0) {
    die("Invalid subject.");
}


// Fetch quiz progress for the selected subject
$quizQuery = "SELECT score, created_at FROM quizzes WHERE user_id = ? AND subject_id = ? ORDER BY created_at ASC";
$stmt = $conn->prepare($quizQuery);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("ii", $user_id, $subject_id);
$stmt->execute();
if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}

$quizResult = $stmt->get_result();

// Prepare data for Chart.js
$quizScores = [];
$quizDates = [];
while ($row = $quizResult->fetch_assoc()) {
    $quizScores[] = $row['score'];
    $quizDates[] = date("M d, Y", strtotime($row['created_at'])); // Format date
}

// Convert arrays to JSON format for Chart.js
$scoresJson = json_encode($quizScores);
$datesJson = json_encode($quizDates);

// Get subject name for the page title
$subjectQuery = "SELECT subject_name FROM subjects WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($subjectQuery);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("ii", $subject_id, $user_id);
$stmt->execute();
if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}
$subjectResult = $stmt->get_result();
$subject = $subjectResult->fetch_assoc();
if (!$subject) {
    die("Subject not found or not accessible by user.");
}
$subject_name = $subject['subject_name'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Quiz Progress - <?php echo htmlspecialchars($subject_name); ?> - RecallIt</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

    .progress-container {
      background-color: #1a1a1a;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 2px 15px rgba(18, 239, 247, 0.849);
      width: 80%;
    }

    h1 {
      text-align: center;
      color: #00f7ff;
      margin-bottom: 20px;
    }

    canvas {
      width: 100%;
      max-width: 900px;
      margin: 0 auto;
    }

    .back-link {
      display: block;
      text-align: center;
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

  <div class="progress-container">
    <h1>Quiz Progress for <?php echo htmlspecialchars($subject_name); ?></h1>

    <!-- Chart.js Graph -->
    <canvas id="quizProgressChart"></canvas>

    <!-- Back Link -->
    <a href="subpro.php" class="back-link">
      <i class="fas fa-arrow-left"></i> Back to Subjects
    </a>
  </div>

  <script>
    const quizScores = <?php echo $scoresJson; ?>;
    const quizDates = <?php echo $datesJson; ?>;

    // Create the chart
    const ctx = document.getElementById('quizProgressChart').getContext('2d');
    const quizProgressChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: quizDates,
        datasets: [{
          label: 'Quiz Scores',
          data: quizScores,
          borderColor: '#00f7ff',
          backgroundColor: 'rgba(0, 247, 255, 0.2)',
          fill: true,
          tension: 0.4,
          pointBackgroundColor: '#00f7ff',
          pointRadius: 5,
          pointHoverRadius: 7
        }]
      },
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true,
            max: 100,
            ticks: {
              stepSize: 10
            }
          }
        },
        plugins: {
          tooltip: {
            callbacks: {
              title: function(tooltipItem) {
                return 'Quiz on ' + tooltipItem[0].label;
              },
              label: function(tooltipItem) {
                return 'Score: ' + tooltipItem.raw + '%';
              }
            }
          }
        }
      }
    });
  </script>

</body>
</html>
