<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Study Guide - RecallIt</title>
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
      font-size: 30px;
      display: flex;
      flex-direction: column;
      align-items: center; /* center horizontally */
    }

    /* Header Styling */
    .study-header {
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
      font-size: 30px;
      color: #00f7ff;
      text-decoration: none;
      transition: color 0.3s ease;
    }

    .home-link:hover {
      color: #02c6d2;
    }

    /* Container (Now Centered) */
    .study-container {
      background-color: #1a1a1a;
      padding: 50px;
      border-radius: 15px;
      box-shadow: 0 8px 20px rgba(0, 212, 255, 0.5);
      text-align: center;
      width: 100%;
      max-width: 950px;
      position: absolute; /* Enables absolute positioning */
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%); /* Perfect centering */
    }

    .study-title {
      font-size: 24px;
      font-weight: bold;
      color: #2edbe1;
      text-transform: uppercase;
      text-shadow: 0 0 15px #074042, 0 0 30px #00f7ff50; /* Glowing effect */
      margin-bottom: 28px;
    }

    .study-description {
      font-size: 16px;
      color: #ccc;
      max-width: 90%;
      margin: 0 auto 30px;
      line-height: 1.6;
    }

    /* Buttons */
    .btn-container {
      display: flex;
      justify-content: center;
      gap: 15px;
    }

    .btn {
      padding: 12px 25px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-size: 16px;
      font-weight: bold;
      transition: 0.3s ease;
    }

    .btn-add {
      background-color: #26262c;
      color: #fcf8f8;
      border: 1px solid #00f7ff;
    }

    .btn-add:hover {
      background-color:#36abb5;
    }

    .btn-view {
      background-color: #26262c;
      color: white;
      border: 1px solid #00f7ff;
    }

    .btn-view:hover {
      background-color: #36abb5;
    }
  </style>
</head>
<body>

  <!-- Header -->
  <div class="study-header">
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
  
  <!-- Study Guide Section -->
  <div class="study-container">
    <div class="study-title">Welcome to Your Study Guide Section!</div>
    
    <p class="study-description">
      All your PDFs, PPTs & notes—organized by subject for faster, smarter access.
    </p>

    <div class="btn-container">
      <button class="btn btn-add" onclick="window.location.href='add-page.php'">Add</button>
      <button class="btn btn-view" onclick="window.location.href='view-page.php'">View</button>
    </div>
  </div>


  <script>
    function showAddSubject() {
      alert("Add Subject form will appear here (functionality pending).");
    }

    function viewSubjects() {
      alert("Subject list will be displayed here.");
    }
  </script>

</body>
</html>