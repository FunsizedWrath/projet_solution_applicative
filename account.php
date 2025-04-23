<?php
session_start();

// Include database connection
include 'database/db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch user info from the database
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT lastname_user, name_user, email_user, phone_user, address_user, postcode_user, city_user FROM users WHERE id_user = :id_user");
$stmt->execute(["id_user" => $user_id]);
$result = $stmt->fetch();

if ($result) {
    $user = $result;
} else {
    echo "Error: User not found.";
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lastname = $_POST['lastname'] ? htmlspecialchars($_POST['lastname']) : null;
    $name = $_POST['name'] ? htmlspecialchars($_POST['name']) : null;
    $email = $_POST['email'] ? htmlspecialchars($_POST['email']) : null;
    $phone = $_POST['phone'] ? htmlspecialchars($_POST['phone']) : null;
    $address = $_POST['address'] ? htmlspecialchars($_POST['address']) : null;
    $postcode = $_POST['postcode'] ? htmlspecialchars($_POST['postcode']) : null;
    $city = $_POST['city'] ? htmlspecialchars($_POST['city']) : null;
    $password = $_POST['password'] ? htmlspecialchars($_POST['password']) : null;

    // Update user data in the database
    $query = "UPDATE users SET lastname_user = :lastname_user, name_user = :name_user, email_user = :email_user, phone_user = :phone_user, address_user = :address_user, postcode_user = :postcode_user, city_user = :city_user";
    $params = [
        'lastname_user' => $lastname,
        'name_user' => $name,
        'email_user' => $email,
        'phone_user' => $phone,
        'address_user' => $address,
        'postcode_user' => $postcode,
        'city_user' => $city,
        'id_user' => $user_id
    ];

    if (!empty($password)) {
        $query .= ", password_user = :password_user";
        $params['password_user'] = password_hash($password, PASSWORD_DEFAULT);
    }

    $query .= " WHERE id_user = :id_user";
    $stmt = $pdo->prepare($query);

    try {
        if ($stmt->execute($params)) {
            $message = "Your information has been updated successfully.";
            // Refresh user data
            $user['lastname_user'] = $lastname;
            $user['name_user'] = $name;
            $user['email_user'] = $email;
            $user['phone_user'] = $phone;
            $user['address_user'] = $address;
            $user['postcode_user'] = $postcode;
            $user['city_user'] = $city;
        } else {
            $message = "Error updating your information.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Page</title>
    <link rel="stylesheet" href="styles/style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="sidebar">
        <button onclick="showSection('personal-info')">Données personnelles</button>
        <button onclick="showSection('active-books')">Documents empruntés</button>
        <button onclick="showSection('active-disputes')">Contentieux</button>
    </div>
    <div class="content">
        <div id="personal-info" class="section active">
            <?php if (!empty($message)): ?>
                <p class="message"><?= $message ?></p>
            <?php endif; ?>
            <form method="POST" action="">
                <label for="lastname">Nom :</label>
                <input type="text" id="lastname" name="lastname" value="<?= htmlspecialchars($user['lastname_user']) ?>" required>

                <label for="name">Prénom :</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name_user']) ?>" required>

                <label for="email">E-mail :</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email_user']) ?>" required>

                <label for="phone">Téléphone :</label>
                <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($user['phone_user']) ?>">

                <label for="address">Adresse :</label>
                <textarea id="address" name="address"><?= htmlspecialchars($user['address_user']) ?></textarea>

                <label for="postcode">Code postal :</label>
                <input type="text" id="postcode" name="postcode" value="<?= htmlspecialchars($user['postcode_user']) ?>">

                <label for="city">Ville :</label>
                <input type="text" id="city" name="city" value="<?= htmlspecialchars($user['city_user']) ?>">

                <label for="password">Mot de passe :</label>
                <input type="password" id="password" name="password" placeholder="Laissez vide si vous ne souhaitez pas le changer" class="input">

                <button type="submit" class="button">Mettre à jour</button>
            </form>
        </div>
<!-- paste snipet of code here -->
    <script>
        function showSection(sectionId) {
            const sections = document.querySelectorAll('.section');
            sections.forEach(section => section.classList.remove('active'));
            document.getElementById(sectionId).classList.add('active');

            const buttons = document.querySelectorAll('.sidebar button');
            buttons.forEach(button => button.classList.remove('active'));
            document.querySelector(`.sidebar button[onclick="showSection('${sectionId}')"]`).classList.add('active');
        }
    </script>
</body>
</html>