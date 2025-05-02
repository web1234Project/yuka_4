<?php
// Include your database connection file
require_once __DIR__ . '/../common/config.php';

// 1.  Get the plain-text password
$plainTextPassword = "admin01";  // Replace with the actual password

// 2.  Hash the password
$hashedPassword = password_hash($plainTextPassword, PASSWORD_DEFAULT);

// 3.  Update the 'admin' table using PDO
try {
    // Create a PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$database", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $email = 'admin01@gmail.com'; // Replace with the actual admin email

    $stmt = $pdo->prepare("UPDATE admin SET password = ? WHERE email = ?");
    $stmt->execute([$hashedPassword, $email]);

    $affectedRows = $stmt->rowCount(); // Use PDO's rowCount()

    if ($affectedRows > 0) {
        echo "Admin password updated successfully!";
    } else {
        echo "Admin password NOT updated. Email may not exist, or the password was already the hash.";
    }

} catch (PDOException $e) {
    die("Error updating password: " . $e->getMessage());
}

?>
