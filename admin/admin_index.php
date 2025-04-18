<?php
require_once __DIR__ . '/../common/config.php';

// Check if the 'id' parameter is passed first (before querying all users)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && is_numeric($_POST['id'])) {
    $userId = intval($_POST['id']); // Sanitize the input correctly from POST
    // Prepare the DELETE query
    $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $userId);
        mysqli_stmt_execute($stmt);

        // Check if user was deleted
        if (mysqli_stmt_affected_rows($stmt) > 0) {
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            header("Location: admin_index.php"); // Redirect after delete
            exit();
        } else {
            echo "Error deleting user.";
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "Database error.";
    }
}

// Now fetch all users after any deletion logic has been handled
$sql = "SELECT id, username, email FROM users";
$result = $conn->query($sql); 

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin - RecallIt</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    /* Header Styling */
    .view-header {
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
      font-size: 24px;
      color: #00f7ff;
      text-decoration: none;
      transition: color 0.3s ease;
    }

    .home-link:hover {
      color: #02c6d2;
    }

    /* Back Button */
    .back-link {
      font-size: 14px;
      color: #00f7ff;
      text-decoration: none;
      transition: color 0.3s ease;
    }

    .back-link:hover {
      color: #02c6d2;
    }

    /* Table Styling */
    .table-container {
      width: 90%;
      max-width: 900px;
      background-color: #1a1a1a;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 2px 15px rgba(18, 239, 247, 0.849);
      margin: 30px auto;
      text-align: center;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    th, td {
      padding: 12px;
      text-align: center;
      border-bottom: 1px solid #00f7ff;
    }

    th {
      background-color: #26262c;
      color: #00f7ff;
      font-size: 18px;
      text-transform: uppercase;
    }

    td {
      background-color: #1e1e1e;
      color: #ffffff;
    }

    .empty-message {
      color: #ccc;
      font-size: 18px;
      margin-top: 20px;
    }

    .delete-link {
        color: #00d4ff;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .delete-link:hover {
        color: #02c6d2; /* Optional hover effect */
    }
  </style>
</head>
<body>

  <!-- Header -->
  <div class="view-header">
    <div class="logo-section">
      <img src="logo.png" alt="RecallIt Logo" class="logo">
      <span class="logo-name">Recallit</span>
    </div>
    <div class="home-section">
      <a href="admin_login.php" class="home-link">
        <i class="fas fa-home"></i>
      </a>
    </div>
  </div>

  <!-- Table Container -->
  <div class="table-container">
    <h2>Registered Users Data</h2>
    <table>
      <thead>
        <tr>
          <th>id</th>
          <th>Username</th>
          <th>Email</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if ($result->num_rows > 0) {
            $index = 1;
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['username'] . "</td>";
                echo "<td>" . $row['email'] . "</td>";
                echo "<td>
                <form method='POST' action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "'>
                    <input type='hidden' name='id' value='" . htmlspecialchars($row["id"]) . "'>
                    <button type='submit' class='delete-link' style='background:none;border:none;color:#00d4ff;cursor:pointer;' onclick='return confirmDelete();'>Delete</button>
                </form>

                      </td>";
                echo "</tr>";
            }
        }else{
            echo "<tr><td colspan='4' class='empty-message'>No users registered yet!</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>

  <div class="back-section">
    <a href="admin_login.php" class="back-link">
      Logout
    </a>
    <br><br>
  </div>

  <script>
    function confirmDelete() {
        return confirm("Are you sure you want to delete this user?");
    }
</script>

</body>
</html>