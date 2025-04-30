<?php
session_start();

// Include database connection
require_once 'database/db_connection.php';
require_once 'database/document_type_enum.php';
require_once 'display_argument_enum.php';

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

$section = $_GET['section'] ?? 'personal-info';

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

$stmt = $pdo->prepare("SELECT DISTINCT d.*, br.date_borrowed, br.return_date_borrowed, br.id_borrowed, b.author_book, b.nbr_words_book, b.publisher_book, di.artist_disk, di.producer_disk, di.director_disk
FROM document d
LEFT JOIN book b ON d.id_document = b.id_document
LEFT JOIN disk di ON d.id_document = di.id_document
JOIN borrowed br ON br.id_document = d.id_document AND br.id_user = :id_user");
$stmt->execute(['id_user' => $user_id]);
$documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
usort($documents, function ($a, $b) {
    if ($a['return_date_borrowed'] && $b['return_date_borrowed']) {
        return strtotime($a['return_date_borrowed']) - strtotime($b['return_date_borrowed']);
    } elseif ($a['return_date_borrowed']) {
        return 1;
    } elseif ($b['return_date_borrowed']) {
        return -1;
    }
    return strtotime($a['date_borrowed']) - strtotime($b['date_borrowed']);
});

$stmt = $pdo->prepare("SELECT * FROM dispute JOIN dispute_type ON dispute.id_dispute_type = dispute_type.id_dispute_type JOIN document ON dispute.id_document = document.id_document WHERE id_user = :id_user");
$stmt->execute(['id_user' => $user_id]);
$activeDisputes = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
        <a href="?section=personal-info&id_user=<?= $user_id ?>"><button type="button">Données personnelles</button></a>
        <a href="?section=active-documents&id_user=<?= $user_id ?>"><button type="button">Documents empruntés</button></a>
        <a href="?section=active-disputes&id_user=<?= $user_id ?>"><button type="button">Contentieux</button></a>
    </div>
    <div class="content">
        <?php if ($section == 'personal-info'): ?>
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
        <?php elseif ($section == 'active-documents'): ?>
        <div id="active-books" class="section">
            <h2>Livres empruntés</h2>
            <?php $display_argument = display_argument::NoAction; require_once 'display_documents.php'; ?>
        </div>
        <?php elseif ($section == 'active-disputes'): ?>
        <div id="active-disputes" class="section">
        <table class="dispute-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Status</th>
                            <th>Description</th>
                            <th>Type</th>
                            <th>Titre du document</th>
                            <th>Date de création</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($activeDisputes as $dispute): ?>
                            <tr>
                                <td><?= htmlspecialchars($dispute['id_dispute']) ?></td>
                                <td><?= htmlspecialchars($dispute['status_dispute']) ?></td>
                                <td><?= htmlspecialchars($dispute['description_dispute']) ?></td>
                                <td><?= htmlspecialchars($dispute['name_dispute_type']) ?></td>
                                <td><?= htmlspecialchars($dispute['title_document']) ?></td>
                                <td><?= htmlspecialchars($dispute['start_date_dispute']) ?></td>
                                <td>
                                    <?php if ($dispute['end_date_dispute'] == null): ?>
                                        <p>Non résolu</p>
                                    <?php else: ?>
                                        <p>Résolu le : <?= htmlspecialchars($dispute['end_date_dispute']) ?></p>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
        </div>
        <?php endif; ?>
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