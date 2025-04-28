<?php

$httpProtocol = !isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on' ? 'http' : 'https';

$base_url = $httpProtocol.'://'.$_SERVER['HTTP_HOST'];

?>

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
        <!-- <a href="index.php">Accueil</a>
        <a href="library.php">Bibliothèque</a> -->
        <?php
        // Start the session at the very beginning of the script
        session_start();
        echo "<a href=\"$base_url/index.php\">Accueil</a>
        <a href=\"$base_url/library.php\">Bibliothèque</a>";

        // Debugging: Ensure session variables are set correctly
        if (!isset($_SESSION['role'])) {
            error_log('$_SESSION["role"] is not set.');
        } else {
            error_log('$_SESSION["role"]: ' . $_SESSION['role']);
        }
        if (isset($_SESSION['role']) && in_array($_SESSION['role'], [1, 2, 3])) {
            echo "<a href=\"$base_url/backoffice/manage_library.php\">Gérer la bibliothèque</a>";
            echo "<a href=\"$base_url/backoffice/manage_client.php\">Gérer les clients</a>";
            echo "<a href=\"$base_url/backoffice/manage_location.php\">Gérer les emplacements</a>";
            echo "<a href=\"$base_url/backoffice/manage_subscription.php\">Gérer les abonnements</a>";
            echo "<a href=\"$base_url/backoffice/manage_tag.php\">Gérer les étiquettes</a>";
        }
        echo '<div class="navbar-right">';
        if (isset($_SESSION['user_id'])) {
            echo "<a href=\"$base_url/account.php\">Compte</a>";
            echo "<a href=\"$base_url/logout.php\">Déconnexion</a>";
        } else {
            echo "<a href=\"$base_url/login.php\">Se connecter / Créer un compte</a>";
        }
        echo '</div>';
    ?>
    </div>
</body>
</html>