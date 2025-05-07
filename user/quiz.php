<?php
require_once '../common/config.php';

// Assuming you have a session where user_id is stored after the user logs in
session_start();

// Check if the user is logged in (make sure user_id is set in the session)
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header('Location: login.php');
    exit();
}

// Get the logged-in user's ID from the session
$user_id = $_SESSION['user_id']; 

// Fetch all subjects created by the user
$subjects_sql = "
  SELECT DISTINCT s.*
  FROM subjects s
  INNER JOIN flashcards f ON s.id = f.subject_id
  WHERE s.user_id = $user_id AND f.user_id = $user_id

  UNION

  SELECT DISTINCT s.*
  FROM subjects s
  INNER JOIN shared_flashcards sf ON s.id = sf.subjectid
  WHERE sf.recipient_id = $user_id AND sf.status = 'Accepted'
";
$subjects_result = $conn->query($subjects_sql);

$subjects = [];
if ($subjects_result->num_rows > 0) {
    while ($row = $subjects_result->fetch_assoc()) {
        $subjects[] = $row;
    }
}

// If a subject is selected, fetch the corresponding flashcards
$selected_subject = isset($_GET['subject']) ? intval($_GET['subject']) : null;
$selected_subject_name = '';
if ($selected_subject) {
    foreach ($subjects as $subj) {
        if ($subj['id'] == $selected_subject) {
            $selected_subject_name = $subj['subject_name'];
            break;
        }
    }
}
$flashcards = [];
if ($selected_subject) {
  $flashcards_sql = "SELECT * FROM flashcards WHERE user_id = $user_id AND subject_id = $selected_subject";
  $flashcards_result = $conn->query($flashcards_sql);
  
  $temp_flashcards = [];
  if ($flashcards_result->num_rows > 0) {
      while ($row = $flashcards_result->fetch_assoc()) {
          $temp_flashcards[] = $row;
      }
      // Shuffle and take only up to 10
      shuffle($temp_flashcards);
      $flashcards = array_slice($temp_flashcards, 0, min(10, count($temp_flashcards)));
  }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Take a Quiz - RecallIt</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  <style>
     
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: #0e0e10;
      color: #fff;
      min-height: 100vh;
    }

    .quiz-header {
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
      margin-right: 60px;
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
      background-color: #1a1a1a;
      border-radius: 12px;
      padding: 30px;
      max-width: 700px;
      width: 100%;
      box-shadow: 0 0 15px rgba(0, 212, 255, 0.2);
      margin: 100px auto 20px;
    }

    .card {
      width: 100%;
      height: 250px;
      perspective: 1000px;
      margin-top: 20px;
    }

    .card-inner {
      width: 100%;
      height: 100%;
      position: relative;
      transition: transform 0.8s;
      transform-style: preserve-3d;
    }

    .card.flipped .card-inner {
      transform: rotateY(180deg);
    }

    .card-content {
      position: absolute;
      width: 100%;
      height: 100%;
      backface-visibility: hidden;
      background: #2c2c3e;
      border-radius: 12px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      padding: 20px;
      box-sizing: border-box;
    }

    .card-content.back {
      transform: rotateY(180deg);
    }

    input[type="text"] {
      padding: 10px;
      font-size: 16px;
      margin-top: 10px;
      width: 80%;
      border-radius: 8px;
      border: none;
    }

    button {
      background: linear-gradient(135deg, #0072ff, #00d4ff);
      color: white;
      border: none;
      padding: 12px 24px;
      border-radius: 10px;
      font-size: 16px;
      cursor: pointer;
      margin-top: 20px;
    }

    .rating-btns button {
      margin: 5px;
      background: #00a859;
    }

    .hidden {
      display: none;
    }

    a.create-link {
      color: #00d4ff;
      text-decoration: underline;
    }
    .quit-button {
  background-color: #ff4c4c;
  color: white;
  padding: 10px 15px;
  border: none;
  border-radius: 5px;
  font-weight: bold;
  cursor: pointer;
  margin-left: 10px;
    }

.quit-button:hover {
  box-shadow: 0 0 8px rgba(43, 245, 234, 0.8);
  background:rgb(17, 173, 197);
  transform: translateY(-2px);
}

.button-container {

  display: flex;
  justify-content: center;
  margin-top: 20px;
}

  </style>
</head>
<body>

  <!-- Header -->
  <div class="quiz-header">
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

  <div class="container">
    <!-- Subject Selection -->
    <?php if (!$selected_subject): ?>
      <h2>Select a Subject</h2>
      <p>Choose a subject to begin your quiz.</p>
      
      <?php if (count($subjects) > 0): ?>
        <?php foreach ($subjects as $subject): ?>
          <button onclick="window.location.href='quiz.php?subject=<?= $subject['id'] ?>'">
            <?= htmlspecialchars($subject['subject_name']) ?>
          </button>
        <?php endforeach; ?>
      <?php else: ?>
        <p>You don't have any subjects yet. Please <a href='subjects.php'>create a subject</a> first.</p>
      <?php endif; ?>
    <?php else: ?>
      <!-- Check if flashcards are available -->
      <?php if (count($flashcards) > 0): ?>
        <!-- Quiz Time! -->
        <h2>Quiz Time: <?= htmlspecialchars($selected_subject_name) ?></h2>
        <p>Click below to test yourself using your flashcards. Rate yourself honestly!</p>
        <button id="startBtn">Get Started</button>

        <!-- Quiz area -->
        <div id="quizArea" class="hidden">
          <div class="card" id="card">
            <div class="card-inner">
              <div class="card-content front">
                <div id="question"></div>
              </div>
              <div class="card-content back">
                <input type="text" id="userAnswer" placeholder="Type your answer here" />
                <button id="submitBtn">Submit</button>
                <div id="answer" class="hidden"></div>
              </div>
            </div>
          </div>

          <button id="flipBtn">Flip</button>

          <div class="rating-btns hidden" id="ratingArea">
            <p>How well did you answer? Rate yourself:</p>
            <button data-rating="1">1</button>
            <button data-rating="2">2</button>
            <button data-rating="3">3</button>
            <button data-rating="4">4</button>
            <button data-rating="5">5</button>
          </div>

          <button id="nextBtn" class="hidden">Next</button>
        </div>
      <?php else: ?>
        <p>No flashcards found for the selected subject. Please <a href='flash_creation.php'>create flashcards</a> for this subject first.</p>
      <?php endif; ?>
    <?php endif; ?>
  </div>
  <div class="button-container">
  <form method="post">
    <button type="submit" name="quit_quiz" class="quit-button">Quit Quiz</button>
  </form>
</div>

  <script>
    const flashcards = <?php echo json_encode($flashcards); ?>;
    let current = 0;
    let totalScore = 0;

    const startBtn = document.getElementById("startBtn");
    const quizArea = document.getElementById("quizArea");
    const questionDiv = document.getElementById("question");
    const answerDiv = document.getElementById("answer");
    const userAnswerInput = document.getElementById("userAnswer");
    const flipBtn = document.getElementById("flipBtn");
    const submitBtn = document.getElementById("submitBtn");
    const ratingArea = document.getElementById("ratingArea");
    const nextBtn = document.getElementById("nextBtn");
    const card = document.getElementById("card");

    startBtn.onclick = () => {
      if (flashcards.length === 0) {
        quizArea.innerHTML = `<p>You don't have any flashcards yet. Please <a href='flash_creation.php' class='create-link'>create some</a> to start a quiz.</p>`;
        quizArea.classList.remove('hidden');
        startBtn.style.display = 'none';
        return;
      }
      startBtn.style.display = 'none';
      quizArea.classList.remove('hidden');
      showCard();
    };

    function showCard() {
      const cardData = flashcards[current];
      questionDiv.innerHTML = `<strong>Q:</strong> ${cardData.question}`;
      answerDiv.innerHTML = `<strong>Correct A:</strong> ${cardData.answer}`;
      answerDiv.classList.add("hidden");
      userAnswerInput.value = "";
      card.classList.remove("flipped");
      flipBtn.style.display = "inline-block";
      ratingArea.classList.add("hidden");
      submitBtn.style.display = "inline-block";
      nextBtn.classList.add("hidden");
    }

    flipBtn.onclick = () => {
      card.classList.add("flipped");
      flipBtn.style.display = "none";
    };

    submitBtn.onclick = () => {
      const userAns = userAnswerInput.value.trim();
      if (userAns === "") {
        alert("Please enter your answer.");
        return;
      }
      answerDiv.classList.remove("hidden");
      submitBtn.style.display = "none";
      ratingArea.classList.remove("hidden");
    };

    document.querySelectorAll('[data-rating]').forEach(btn => {
      btn.onclick = () => {
        const score = parseInt(btn.getAttribute('data-rating'));
        totalScore += score;
        ratingArea.classList.add("hidden");
        nextBtn.classList.remove("hidden");
      };
    });

    nextBtn.onclick = () => {
      current++;
      if (current < flashcards.length) {
        showCard();
      } else {
        showScore();
      }
    };

    function showScore() {
      const avgScore = flashcards.length ? Math.round(totalScore / flashcards.length * 10) : 0;
      quizArea.innerHTML = `<h3>Quiz Completed!</h3><p>Your Score: ${avgScore}/50</p>`;
      fetch('save_score.php', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    score: avgScore,
    user_id: <?= $user_id ?>,
    subject_id: <?= $selected_subject ?>
  })
});
    }
  </script>

</body>
</html>
