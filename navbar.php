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
        session_start();
        if (isset($_SESSION['user_id'])) {
            echo '<a href="account.php">Compte</a>';
        } else {
            echo '<a href="login.php">Se connecter/Créer un compte</a>';
        }
        if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['employee', 'admin', 'superadmin'])) {
            echo '<a href="manage_library.php">Gérer la bibliothèque</a>';
            echo '<a href="manage_clients.php">Gérer les clients</a>';
        }
    ?>
    </div>
</body>
</html>