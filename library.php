<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>library</title>

</head>
<body>
    <?php include 'navbar.php'; ?>
    <h1>Bibliothèque</h1>
    <div class="search-container">
        <form method="GET" action="library.php">
            <input type="text" name="search" placeholder="Search for a piece of work..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
            <button type="submit">Search</button>
        </form>
    </div>
    <div class="filters">
        <form method="GET" action="library.php">
            <label>