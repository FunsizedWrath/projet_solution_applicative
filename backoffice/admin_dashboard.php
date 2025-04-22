<?php
// admin_dashboard.php

session_start();

// // Check if the user is logged in
// if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
//     header("Location: ../login.php");
//     exit;
// }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../styles/style.css">
</head>
<body>
    <?php include '../navbar.php'; ?>
    <header>
        <h1>Admin Dashboard</h1>
        <nav>
            <ul>
                <li><a href="manage_document.php">Manage Documents</a></li>
                <li><a href="manage_client.php">Manage Clients</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h2>Welcome to the Admin Dashboard</h2>
        <p>Select an option from the menu to get started.</p>
    </main>
    <footer>
        <p>&copy; <?php echo date("Y"); ?> Your Company. All rights reserved.</p>
    </footer>
</body>
</html>