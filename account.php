<?php
session_start();


// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lastname = htmlspecialchars($_POST['lastname_user']);
    $name = htmlspecialchars($_POST['name_user']);
    $email = htmlspecialchars($_POST['email_user']);
    $phone = htmlspecialchars($_POST['phone_user']);
    $address = htmlspecialchars($_POST['address_user']);
    $postcode = htmlspecialchars($_POST['postcode_user']);
    $city = htmlspecialchars($_POST['city_user']);

    // Update user data logic here (e.g., save to database)
    $user['lastname'] = $lastname;
    $user['name'] = $name;
    $user['email'] = $email;
    $user['phone'] = $phone;
    $user['address'] = $address;
    $user['postcode'] = $postcode;
    $user['city'] = $city;

    $message = "Your information has been updated successfully.";
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
                <label for="lastname_user">Nom :</label>
                <input type="text" id="lastname_user" name="lastname_user" value="<?= htmlspecialchars($user['lastname']) ?>" required>

                <label for="name_user">Prénom :</label>
                <input type="text" id="name_user" name="name_user" value="<?= htmlspecialchars($user['name']) ?>" required>

                <label for="email">E-mail :</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

                <label for="phone">Téléphone :</label>
                <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($user['phone']) ?>">

                <label for="address_user">Adresse :</label>
                <textarea id="address_user" name="address_user"><?= htmlspecialchars($user['address']) ?></textarea>

                <label for="postcode_user">Code postal :</label>
                <input type="text" id="postcode_user" name="postcode_user" value="<?= htmlspecialchars($user['postcode']) ?>">

                <label for="city_user">Ville :</label>
                <input type="text" id="city_user" name="city_user" value="<?= htmlspecialchars($user['city']) ?>">

                <button type="submit">Mettre à jour</button>
            </form>
        </div>
        <div id="active-books" class="section">
            <ul>
                <?php foreach ($activeBooks as $book): ?>
                    <li><?= htmlspecialchars($book['title']) ?> - Due: <?= htmlspecialchars($book['due_date']) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div id="active-disputes" class="section">
            <ul>
                <?php foreach ($activeDisputes as $dispute): ?>
                    <li>#<?= htmlspecialchars($dispute['id']) ?> - <?= htmlspecialchars($dispute['description']) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
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