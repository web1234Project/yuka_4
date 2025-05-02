<?php
require_once __DIR__ . '/../common/config.php';

// Initialize errors
$errors = [
    'email' => '',
    'password' => '',
];
$email = '';

// Start the session at the very beginning of the script
session_start();

// Check if the user is already logged in
// if (isset($_SESSION['admin_id'])) {  <-- REMOVE THIS LINE
//     header("Location: admin_dashboard.php");
//     exit();
// }


// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Input sanitization
    $email = htmlspecialchars(trim($_POST['email'] ?? ''));
    $password = trim($_POST['password'] ?? '');

    // Validation
    if (!$email) {
        $errors['email'] = "Email is required.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format.";
    }
    if (!$password) {
        $errors['password'] = "Password is required.";
    }

    // Database authentication
    if (empty(array_filter($errors))) { // Proceed only if there are no errors
        try {
            $stmt = mysqli_prepare($conn, "SELECT id, password, email FROM admin WHERE email = ?");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "s", $email);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_bind_result($stmt, $id, $dbPassword, $dbEmail);
                mysqli_stmt_fetch($stmt);

                if ($dbPassword) {
                    if (password_verify($password, $dbPassword)) {
                        // Successful login
                        $_SESSION['admin_id'] = $id;
                        $_SESSION['admin_email'] = $dbEmail;
                        mysqli_stmt_close($stmt);
                        mysqli_close($conn);
                        header("Location: admin_index.php");
                        exit();
                    } else {
                        $errors['password'] = "Invalid email or password.";
                    }
                } else {
                    $errors['password'] = "Invalid email or password.";
                }
                mysqli_stmt_close($stmt);
            } else {
                // Log the error
                error_log("Failed to prepare statement: " . mysqli_error($conn));
                $errors['email'] = "Database error. Please try again.";
            }
        } catch (Exception $e) {
            //catch any other exceptions
            error_log("Exception: " . $e->getMessage());
            $errors['email'] = "Database error. Please try again.";
        } finally {
            mysqli_close($conn);
        }
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
        /* Original CSS from your code */
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

        .image {
            margin-left: 85px;
            margin-right: 15px;
            margin-top: 30px;
        }

        .heading {
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
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        input {
            width: 90%;
            padding: 15px;
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

        .error-message {
            color: #00d4ff;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            text-align: left;
            width: 90%;
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
            <input type="email" name="email" id="email" placeholder="Email"
                   value="<?= htmlspecialchars($email) ?>" required>
            <?php if (!empty($errors['email'])): ?>
                <div class="error-message"><?= htmlspecialchars($errors['email']) ?></div>
            <?php endif; ?>

            <input type="password" name="password" id="password" placeholder="Password" required>
            <?php if (!empty($errors['password'])): ?>
                <div class="error-message"><?= htmlspecialchars($errors['password']) ?></div>
            <?php endif; ?>

            <br>

            <button type="submit" class="cta-button">Login</button>
        </form>
    </div>
</body>
</html>
