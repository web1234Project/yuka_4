<?php
   
    // Include the database connection file
    include '../common/config.php';

    // Check if the connection was successful
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    ?>
    <div class="user-list-container">
        <h2>User List</h2>
        <table id="user-table">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php


                // SQL query to select username and email from the users table
                $sql = "SELECT id, username, email FROM users";
                $result = mysqli_query($conn, $sql);

                // Check if there are any users in the database
                if (mysqli_num_rows($result) > 0) {
                    // Loop through each row in the result set
                    while ($row = mysqli_fetch_assoc($result)) {
                        // Output the user data in a table row
                        echo "<tr>";
                        echo "<td>" . $row["username"] . "</td>";
                        echo "<td>" . $row["email"] . "</td>";
                        echo "<td><button class='delete-button button' data-user-id='" . $row["id"] . "'>Delete</button></td>";
                        echo "</tr>";
                    }
                } else {
                    // If there are no users in the database, display a message
                    echo "<tr><td colspan='3'>No users found</td></tr>";
                }

                // Close the database connection
                mysqli_close($conn);
                ?>
            </tbody>
        </table>
    </div>
    <script>
        // User deletion functionality
        document.querySelectorAll('.delete-button').forEach(button => {
            button.addEventListener('click', function() {
                const userId = this.dataset.userId;
                // In a real application, you would send an AJAX request to your server to delete the user
                console.log(`Deleting user with ID: ${userId}`);
                // Remove the user's row from the table
                this.closest('tr').remove();

                // Send an AJAX request to delete the user from the database
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'delete_user.php'); // Create a new file named delete_user.php
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        // Handle success (optional: show a message to the user)
                        console.log('User deleted successfully');
                    } else {
                        // Handle error (optional: show an error message)
                        console.error('Error deleting user');
                    }
                };
                xhr.send('id=' + userId); // Send the user ID to delete_user.php
            });
        });
    </script>
