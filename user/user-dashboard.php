<?php
// Start session if needed (e.g., for user login handling in future)
session_start();
require_once '../common/config.php'; // Make sure this path is correct

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$username = $user['username'] ?? 'User';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recallit Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        /* Paste all your CSS from the previous HTML here (unchanged) */
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: #0e0e10;
            color: #fff;
            display: flex;
            height: 100vh;
            overflow: hidden;
        }
        header {
            width: 100%;
            background: #121212;
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            height:75px;
        }
        .logo-container {
            display: flex;
            align-items: center;
        }
        .logo-container img {
            height: 60px;
            margin-right: 10px;
        }
        .sidebar {
            width: 230px;
            background: #181818;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 150px;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            overflow: hidden;
        }
        .sidebar a {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            width: 100%;
            padding: 15px 20px;
            color: white;
            text-decoration: none;
            font-size: 16px;
            font-weight: bold;
            transition: 0.3s;
        }
        .sidebar a:hover {
            background: #0072ff;
        }
        .sidebar a i {
            margin-right: 10px;
        }
        .sidebar a:first-child {
            margin-top: 0;
            padding-top: 10px;
        }
        .sidebar-divider {
            width: 80%;
            height: 1px;
            background: #444;
            border: none;
            margin: 10px auto;
        }
        .main-content {
            flex-grow: 1;
            margin-left: 230px;
            margin-top: 90px;
            padding: 2rem;
            width: calc(100% - 230px);
            text-align: left;
        }
        .card-container {
            display: flex;
            flex-wrap: wrap;
            gap: 40px;
            margin-top: 20px;
            padding: 20px;
            background-color: #1e1e1e;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 212, 255, 0.8);
            width: 100%;
            max-width: 1190px;
            margin-left: 0;
            margin-right: 0px;
            height: 480px;
            transition: transform 0.3s ease;
        }
        .card-container:hover {
            transform: scale(1.02);
        }
        .btn {
            background-color: #0072ff;
            color: white;
            padding: 10px 18px;
            border: none;
            border-radius: 5px;
            margin-top: 20px;
            cursor: pointer;
            margin-left: 25px;
            font-size: medium;
        }
        .btn:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 20px rgba(0, 212, 255, 0.3);
        }
        @media (max-width: 768px) {
            .sidebar {
                width: 60px;
                align-items: center;
            }
            .sidebar a {
                justify-content: center;
                padding: 15px;
            }
            .sidebar a span {
                display: none;
            }
            .main-content {
                margin-left: 60px;
                width: calc(100% - 60px);
            }
            header {
                width: calc(100% - 60px);
                left: 60px;
            }
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <a href="user-dashboard.php" class="dashboard-link"><i class="fas fa-home"></i><span> Dashboard</span></a>
        <a href="flash_creation.php"><i class="fas fa-plus-circle"></i><span> Create Flashcards</span></a>
        <a href="my-flashcard.php"><i class="fas fa-folder-open"></i><span> My Flashcards</span></a>
        <a href="quiz.php"><i class="fas fa-question-circle"></i><span> Take Quiz</span></a>
        <a href="progress.php"><i class="fas fa-user"></i><span> Progress</span></a>
        <a href="study-guide.php"><i class="fas fa-book"></i><span> Study Guide</span></a>
        <hr class="sidebar-divider">
        <a href="notification.php"><i class="fas fa-bell"></i><span> Notification</span></a>
        <a href="profile.php"><i class="fas fa-user"></i><span> Profile</span></a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i><span> Logout</span></a>
    </div>

    <!-- Header -->
    <header>
        <div class="logo-container">
            <img src="logo.png" alt="Recallit Logo">
            <h1>Recallit</h1>
        </div>
    </header>

    <!-- Main Content -->
    <div class="main-content">
            <h2 style="color:rgb(74, 198, 247);margin-top: 5px;margin-bottom: 10px;">
            Welcome, <?php echo htmlspecialchars($username); ?>!
        </h2>
        <div class="card-container">
            <div class="card">
                <h1 style="margin-top: 150px; color:rgb(74, 198, 247);">🧠 Create Flashcards, Conquer Scores!</h1>
                <p style="font-size:large;margin-left: 25px;">Quickly access and manage your saved cards.</p>
                <button class="btn"><a href="flash_creation.php" style="text-decoration:none;color:white;">Get Started</a></button>
            </div>
            <div class="card">
                <img style="width:320px; height:400px;margin-top: 50px;margin-left: 150px;box-shadow: 0 8px 20px rgba(0, 212, 255, 0.8);border-radius: 5px;" src="flashcard-graphic.png" alt="Flashcard Graphic">
            </div>
        </div>
    </div>

</body>
</html>
