<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/xlsx/dist/xlsx.full.min.js"></script>
    <style>
        /* Custom styles for the sidebar and layout */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #0f172a; /* Dark background for body */
            color: #f8fafc;
        }
        .sidebar {
            background-color: #1e293b;
            color: #f8fafc;
            height: 100vh;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 2rem;
            padding-left: 1rem;
            padding-right: 1rem;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3), 0 2px 4px -1px rgba(0, 0, 0, 0.2);
            z-index: 10;
        }
        .sidebar-logo {
            margin-bottom: 2rem;
            width: 100%;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .sidebar-logo img {
            height: 3rem;
            width: 3rem;
        }
        .sidebar-logo h1 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #00ffff; /* Cyan for logo heading */
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
            width: 100%;
        }
        .sidebar-menu li {
            margin-bottom: 1rem;
        }
        .sidebar-menu li a {
            color: #f8fafc;
            text-decoration: none;
            display: block;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            transition: background-color 0.3s ease;
            width: 100%;
        }
        .sidebar-menu li a:hover {
            background-color: #334155;
        }
        .sidebar-menu li a.active {
            background-color: #00ffff; /* Active link in cyan */
            color: #0f172a;
            font-weight: 600;
        }

        .main-content {
            margin-left: 250px;
            padding: 2rem;
            flex: 1;
        }
        .top-bar {
            background-color: #2d3748;
            color: #f8fafc;
            padding: 1.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.2), 0 1px 2px -1px rgba(0, 0, 0, 0.2);
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .top-bar h2 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #ffffff;
            margin: 0;
        }
        .top-bar-actions {
            display: flex;
            gap: 1rem;
        }

        .report-section {
            background-color: #2d3748;
            color: #f8fafc;
            padding: 1.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.2), 0 1px 2px -1px rgba(0, 0, 0, 0.2);
            margin-bottom: 2rem;
        }

        .report-header{
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .user-list {
            background-color: #2d3748;
            color: #f8fafc;
            padding: 1.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.2), 0 1px 2px -1px rgba(0, 0, 0, 0.2);
            margin-bottom: 2rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
            color: #f8fafc;
        }
        th, td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #4a5568;
        }
        th {
            font-weight: 600;
            color: #cbd5e0;
        }
        tbody tr:hover {
            background-color: #4a5568;
        }
        .actions {
            display: flex;
            gap: 0.5rem;
        }
        .button {
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s ease;
            color: white;
        }
        .edit-button {
            background-color: #6b7280;
        }
        .edit-button:hover {
            background-color: #4b5563;
        }
        .delete-button {
            background-color: #dc2626;
        }
        .delete-button:hover {
            background-color: #b91c1c;
        }
        .chat-button {
            background-color: #00ffff; /* Cyan chat button */
            color: #0f172a;
        }
        .chat-button:hover {
            background-color: #4dd0e1; /* Lighter cyan hover */
            color: #0f172a;
        }

        .message-box {
            background-color: #1e293b;
            border: 1px solid #4a5568;
            color: #f8fafc;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 0.375rem;
        }

         .message-box strong {
            color: #00ffff; /* Cyan for username in message box */
        }

        .text-input{
            color: #f8fafc;
        }

        .report-notification {
            position: relative;
            display: inline-flex; /* Use inline-flex for better alignment */
            align-items: center;
        }
        .report-notification::after {
            content: ""; /* Initially empty */
            position: absolute;
            top: 50%; /* Vertically center the badge */
            right: 0;
            background-color: #00ffff; /* Changed to cyan */
            color: #0f172a;      /* Changed text color to dark gray */
            font-size: 0.75rem;
            padding: 0.25rem;
            border-radius: 50%;
            width: 1.25rem;
            height: 1.25rem;
            text-align: center;
            line-height: 1.25rem;
            transform: translateY(-50%); /* Correct vertical positioning */
            margin-left: 0.75rem; /* Increased horizontal space between text and badge */
            display: none; /* Initially hidden */
        }
        .report-notification a{
             padding-right: 2rem; /* Add padding to the right of the link */
             display: flex; /* Use flexbox to align items horizontally */
             align-items: center;
        }


        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                position: relative;
                height: auto;
                padding-left: 1rem;
                padding-right: 1rem;
            }
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }
        }
    </style>
</head>
<body class="bg-gray-900 flex">
    <aside class="sidebar">
        <div class="sidebar-logo">
            <img src="logo.png" alt="Recallit Logo">
            <h1>Recallit</h1>
        </div>
        <ul class="sidebar-menu">
            <li><a href="admin_index.php" class="active">Dashboard</a></li>
            <li class="report-notification"><a href="reports.php">Reports</a></li>
        </ul>
    </aside>
    <main class="main-content">
        <div class="top-bar">
            <h2>Admin Dashboard</h2>
            <div class="top-bar-actions">
                <button id="export-btn" class="button bg-green-500 hover:bg-green-700">Export Data</button>
            </div>
        </div>
        <div  id="user-list-container">
            <?php include 'users.php'; ?>
        </div>
    </main>
    <script>
        const reportLink = document.querySelector('.report-notification a');
        const reportCountElement = document.querySelector('.report-notification::after');
        const reportNotificationContainer = document.querySelector('.report-notification');
        let reportCount = 0;
        const usersLink = document.getElementById('users-link');
        const userListContainer = document.getElementById('user-list-container');


        // Function to increment the report count
        function incrementReportCount() {
            reportCount++;
            reportCountElement.textContent = reportCount;
            if (reportCount > 0) {
                reportNotificationContainer.classList.remove('hidden');
                reportCountElement.style.display = 'block'; // Make the badge visible
            }
        }

        // Function to decrement the report count
        function decrementReportCount() {
            reportCount--;
            reportCountElement.textContent = reportCount;
             if (reportCount === 0) {
                reportNotificationContainer.classList.add('hidden');
                reportCountElement.style.display = 'none'; // Hide the badge
            }
        }

        reportLink.addEventListener('click', (event) => {
            event.preventDefault();
            window.location.href = "reports.php";
        });

        document.getElementById('export-btn').addEventListener('click', () => {
            const table = document.getElementById('user-table'); // Correct ID.
            const wb = XLSX.utils.table_to_book(table);
            XLSX.writeFile(wb, 'user_data.xlsx');
        });



        usersLink.addEventListener('click', (event) => {
            event.preventDefault();
            userListContainer.style.display = 'block';

        });
    </script>
</body>
</html>
