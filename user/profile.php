<?php
session_start();
require_once '../common/config.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    die("User not logged in.");
}

// Fetch user details
$query = "SELECT username, email FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
if (!$user) {
    die("User not found.");
}

$successMessage = $errorMessage = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];

    $update = "UPDATE users SET username = ?, email = ? WHERE id = ?";
    $stmt = $conn->prepare($update);
    $stmt->bind_param("ssi", $username, $email, $user_id);

    if ($stmt->execute()) {
        $successMessage = "Profile updated successfully!";
        $user['username'] = $username;
        $user['email'] = $email;
    } else {
        $errorMessage = "Error updating profile.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile - RecallIt</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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
            width: 100%;
            background: #1a1a1a;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 247, 255, 0.1);
            z-index: 999;
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
        .home-link {
            font-size: 26px;
            color: #00f7ff;
            text-decoration: none;
        }
        .home-link:hover {
            color: #02c6d2;
        }
        .profile-container {
            width: 350px;
            margin: 120px auto;
            background: #1e1e1e;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px #00f7ff;
        }
        .profile-container h2 {
            margin-bottom: 20px;
            text-align: center;
        }
        label {
            display: block;
            margin: 12px 0 6px;
        }
        input {
            width: 100%;
            padding: 10px;
            background: #2c2c2c;
            color: white;
            border: 1px solid #00f7ff;
            border-radius: 5px;
        }
        .update-btn {
            margin-top: 20px;
            width: 100%;
            padding: 12px;
            background: #00f7ff;
            color: #000;
            font-weight: bold;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        .update-btn:hover {
            background: #02c6d2;
        }
        .success {
            color: #0f0;
            margin-top: 10px;
            text-align: center;
        }
        .error {
            color: #f00;
            margin-top: 10px;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="quiz-header">
    <div class="logo-section">
        <img src="logo.png" alt="Logo" class="logo">
        <span class="logo-name">RecallIt</span>
    </div>
    <a href="user-dashboard.php" class="home-link"><i class="fas fa-home"></i></a>
</div>

<div class="profile-container">
    <h2>User Profile</h2>
    <form method="post">
        <label>Username:</label>
        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>

        <label>Email:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

        <button type="submit" class="update-btn">Update</button>
    </form>
    <div style="text-align: center; margin-top: 20px;">
    <a href="user-dashboard.php" style="color: #00f7ff; text-decoration: none; font-weight: bold;">
        <i class="fas fa-arrow-left"></i> Back to Home
    </a>
</div>
    <?php if (!empty($successMessage)): ?>
        <p class="success"><?= $successMessage ?></p>
    <?php elseif (!empty($errorMessage)): ?>
        <p class="error"><?= $errorMessage ?></p>
    <?php endif; ?>
</div>

</body>
</html>
