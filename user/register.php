<?php
require_once __DIR__ . '/../common/config.php'; // Include database configuration

$errors = [
    'username' => '',
    'email' => '',
    'password' => '',
];
$success = false;
$username = $email = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Input sanitization
    $username = htmlspecialchars(trim($_POST['username'] ?? ''));
    $email = htmlspecialchars(trim($_POST['email'] ?? ''));
    $password = trim($_POST['password'] ?? '');

    // Validation
    // Username validation
    if (!$username) {
        $errors['username'] = "Username is required.";
    } elseif (preg_match('/\s/', $username)) {
        // Check for spaces in the username using regular expressions
        $errors['username'] = "Username must not contain spaces.";
    }

    // Email validation
    if (!$email) {
        $errors['email'] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Please provide a valid email address.";
    }

    // Password validation
    if (!$password) {
        $errors['password'] = "Password is required.";
    } elseif (strlen($password) < 6) {
        $errors['password'] = "Password must be at least 6 characters long.";
    }

    // Process if there are no validation errors
    if (empty(array_filter($errors))) {
        $stmt = mysqli_prepare($conn, "SELECT username, email FROM users WHERE email = ? OR username = ?");
        mysqli_stmt_bind_param($stmt, "ss", $email, $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $existingUsername, $existingEmail);

        while (mysqli_stmt_fetch($stmt)) {
            if ($existingUsername === $username) {
                $errors['username'] = "This username is already taken.";
            }
            if ($existingEmail === $email) {
                $errors['email'] = "This email is already taken.";
            }
        }

        if (empty($errors['username']) && empty($errors['email'])) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $insertStmt = mysqli_prepare($conn, "INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($insertStmt, "sss", $username, $email, $hashedPassword);

            if (mysqli_stmt_execute($insertStmt)) {
                header("Location: confirmation.php");
                exit();
            } else {
                $errors['email'] = "Database error: " . mysqli_error($conn);
            }

            mysqli_stmt_close($insertStmt);
        }

        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Register - Recallit</title>
    <link rel="stylesheet" href="styles.css" />
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

        .logo {
            display: flex;
            align-items: center;
        }

        .logo img {
            height: 50px;
            margin-right: 10px;
        }

        .logo h1 {
            font-size: 2rem;
            font-weight: 700;
            background: linear-gradient(135deg, #00d4ff, #0072ff);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .container {
            background: #121212;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 212, 255, 0.5);
            width: 450px;
            margin-top: 100px;
        }

        h2 {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        input {
            width: 90%;
            padding: 1rem;
            margin-bottom: 1.5rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            background: #1e1e24;
            color: white;
            outline: none;
        }

        .error-message {
            color: #00d4ff;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            text-align: left;
            width: 90%;
        }

        .cta-button {
            width: 100%;
            padding: 1rem;
            font-size: 1rem;
            color: white;
            background: linear-gradient(135deg, #00d4ff, #0072ff);
            border: none;
            border-radius: 50px;
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-top: 10px;
        }

        .cta-button:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 20px rgba(0, 212, 255, 0.5);
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <img src="logo.png" alt="Recallit Logo">
            <h1>Recallit</h1>
        </div>
    </header>

    <div class="container">
        <h2>Create an Account</h2>

        <form id="registerForm" method="POST" action="">
            <input type="text" name="username" id="username" placeholder="Username" required value="<?= htmlspecialchars($username) ?>" />
            <?php if (!empty($errors['username'])): ?>
                <div class="error-message"><?= htmlspecialchars($errors['username']) ?></div>
            <?php endif; ?>

            <input type="email" name="email" id="email" placeholder="Email" required value="<?= htmlspecialchars($email) ?>" />
            <?php if (!empty($errors['email'])): ?>
                <div class="error-message"><?= htmlspecialchars($errors['email']) ?></div>
            <?php endif; ?>

            <input type="password" name="password" id="password" placeholder="Password" required />
            <?php if (!empty($errors['password'])): ?>
                <div class="error-message"><?= htmlspecialchars($errors['password']) ?></div>
            <?php endif; ?>

            <button type="submit" class="cta-button">Register</button>
        </form>
    </div>
</body>
</html>