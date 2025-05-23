<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Successful</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: #0e0e10;
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
        }

        .container {
            background: #121212;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 212, 255, 0.5);
        }

        h2 {
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        .cta-button {
            display: inline-block;
            margin-top: 1rem;
            padding: 0.8rem 1.5rem;
            font-size: 1rem;
            color: white;
            background: linear-gradient(135deg, #00d4ff, #0072ff);
            border: none;
            border-radius: 50px;
            text-decoration: none;
            transition: 0.3s ease;
        }

        .cta-button:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Registration Successful!</h2>
        <p>Your account has been created successfully.</p>
        <a href="login.php" class="cta-button">Go to Login</a>
    </div>
</body>
</html>