<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recallit</title>
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
        }

        header {
            width: 100%;
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

        nav a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
            font-weight: 600;
        }

        .container {
            max-width: 500px;
            background: #121212;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(3, 222, 246, 0.8);
            margin-top: 80px;
        }

        h2 {
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        p {
            color: #aaa;
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
            transform: scale(1.05);
        }
    </style>
</head>

<body>
    <header>
        <div class="logo">
            <img src="logo.png" alt="Recallit">
            <h1>Recallit</h1>
        </div>
        <nav>
            <a href="login.php">Login</a>
            <a href="register.php" style="margin-right: 45px;">Register</a>
        </nav>
    </header>

    <div class="container">
        <h2>Remembering is easier with Recallit</h2>
        <p>Create, share, and test yourself with intelligent flashcards.</p>
        <a href="register.php" class="cta-button">Get Started</a>
    </div>
</body>

</html>
