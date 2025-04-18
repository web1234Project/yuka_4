<?php
session_start(); // ✅ Start the session

require_once __DIR__ . '/../common/config.php';

$errors = [
    'username' => '',
    'password' => '',
];
$success = false;
$username = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Input sanitization
    $username = mysqli_real_escape_string($conn, trim($_POST['username'] ?? ''));
    $password = trim($_POST['password'] ?? '');

    // Validation
    if (!$username) {
        $errors['username'] = "Username is required.";
    } elseif (!$password) {
        $errors['password'] = "Password is required.";
    }

    // Database authentication
    if (empty(array_filter($errors))) {
        // ✅ Also select the user id
        $stmt = mysqli_prepare($conn, "SELECT id, password FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $userId, $hashedPassword);

        if (mysqli_stmt_fetch($stmt)) {
            if (password_verify($password, $hashedPassword)) {
                // ✅ Set user id in session
                $_SESSION['user_id'] = $userId;

                // ✅ Redirect to dashboard
                header("Location: user-dashboard.php");
                exit();
            } else {
                $errors['password'] = "Invalid username or password.";
            }
        } else {
            $errors['username'] = "Invalid username or password.";
        }

        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Recallit</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: #0e0e10;
            color: #fff;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            padding: 20px;
            overflow: hidden;
        }
        header {
            width: 100%;
            height: 75px;
            background: #121212;
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
        }

        .container {
            height: 450px;
            width: 350px;
            background: #121212;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 212, 255, 0.5);
            
        }

        .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 50px;
    
        }
        .logos {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 50px;
            margin-top: 10px;
            width: 50px;
            height: 50px;
            margin-right: 15px;
        }

        .image{
            
            margin-left: 85px; 
            margin-right: 15px; 
            margin-top: 30px;
        }
        .heading{
            background: linear-gradient(135deg, #00d4ff, #0072ff);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent; 
            margin-top: 55px;
        }

        .logo img {
            width: 50px;
            height: 50px;
            margin-right: 10px;
        }

        .logo h1 {
            font-size: 1.8rem;
            background: linear-gradient(135deg, #00d4ff, #0072ff);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        
        input {
            width: 90%;
            padding:15px;
            margin-bottom: 1rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            background: #1e1e24;
            color: white;
            outline: none;
        }

        .error {
            color: red;
            font-size: 0.9rem;
            display: none;
            margin-top: -10px;
        }

        .cta-button {
            padding: 0.8rem 1.5rem;
            font-size: 1rem;
            color: white;
            background: linear-gradient(135deg, #00d4ff, #0072ff);
            border: none;
            border-radius: 50px;
            cursor: pointer;
            transition: 0.3s ease;
            margin-top: 10px;
        }

        .cta-button:hover {
            transform: scale(1.05);
        }

            

        .link {
            margin-top: 1rem;
            color: #aaa;
        }

        .link a {
            color: #00d4ff;
            text-decoration: none;
        }

        .error-message {
            color: #00d4ff;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            text-align: left;
            width: 90%;
        }
       
    </style>
</head>

<body>
    <header>
        <div class="logos">
            <img src="logo.png" alt="Recallit" class="image" width="50px" height="50px">
            <h1 class="heading">Recallit</h1>
        </div>
       
    </header>

    <div class="container">
        <div class="logo">
            <img src="logo.png" alt="Recallit Logo">
            <h1>Recallit</h1>
        </div>
        <form id="loginForm" method="POST" action="">
            <input type="text" name="username" id="username" placeholder="Username" required value="<?= htmlspecialchars($username) ?>" />
            <?php if (!empty($errors['username'])): ?>
                <div class="error-message"><?= htmlspecialchars($errors['username']) ?></div>
            <?php endif; ?>

            <input type="password" name="password" id="password" placeholder="Password" required />
            <?php if (!empty($errors['password'])): ?>
                <div class="error-message"><?= htmlspecialchars($errors['password']) ?></div>
            <?php endif; ?>

            <button type="submit" class="cta-button">Login</button>
        </form>
        <p class="link">Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</body>

</html>