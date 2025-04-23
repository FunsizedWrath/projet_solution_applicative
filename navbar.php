<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar</title>
    <link rel="stylesheet" href="styles/style.css">
</head>
<body>
    <div class="navbar">
        <a href="index.php">Accueil</a>
        <a href="library.php">Bibliothèque</a>
        <?php
        // Start the session at the very beginning of the script
        session_start();

        // Debugging: Ensure session variables are set correctly
        if (!isset($_SESSION['role'])) {
            error_log('$_SESSION["role"] is not set.');
        } else {
            error_log('$_SESSION["role"]: ' . $_SESSION['role']);
        }
        if (isset($_SESSION['role']) && in_array($_SESSION['role'], [1, 2, 3])) {
            echo '<a href="manage_library.php">Gérer la bibliothèque</a>';
            echo '<a href="manage_clients.php">Gérer les clients</a>';
        }
        echo '<div class="navbar-right">';
        if (isset($_SESSION['user_id'])) {
            echo '<a href="account.php">Compte</a>';
            echo '<a href="logout.php">Déconnexion</a>';
        } else {
            echo '<a href="login.php">Se connecter/Créer un compte</a>';
        }
        echo '</div>';
    ?>
    </div>
</body>
</html>