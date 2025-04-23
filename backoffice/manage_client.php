<?php
require_once '../database/db_connection.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['form_type']) && $_POST['form_type'] === 'register') {
    // Retrieve form data
    $lastname = $_POST['lastname'] ? htmlspecialchars($_POST['lastname']) : null;
    $name = $_POST['name'] ? htmlspecialchars($_POST['name']) : null;
    $email = $_POST['email'] ? htmlspecialchars($_POST['email']) : null;
    $phone = $_POST['phone'] ? htmlspecialchars($_POST['phone']) : null;
    $address = $_POST['address'] ? htmlspecialchars($_POST['address']) : null;
    $postcode = $_POST['postcode'] ? htmlspecialchars($_POST['postcode']) : null;
    $city = $_POST['city'] ? htmlspecialchars($_POST['city']) : null;
    $password = $_POST['password'] ? htmlspecialchars($_POST['password']) : null;

    echo $lastname, "<br/>", $name, "<br/>", $email, "<br/>", $phone, "<br/>", $address, "<br/>", $postcode, "<br/>", $city, "<br/>", hash("sha256", $password);

    // Validate form data (basic example)
    if (!empty($name) && !empty($lastname) && !empty($password) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Save the data to the database
        $stmt = $pdo->prepare("INSERT INTO users (lastname_user, name_user, email_user, phone_user, address_user, postcode_user, city_user, password_user) VALUES (:lastname_user, :name_user, :email_user, :phone_user, :address_user, :postcode_user, :city_user, :password_user)");
        if ($stmt->execute(["lastname_user" => $lastname, "name_user" => $name, "email_user" => $email, "phone_user" => $phone, "address_user" => $address, "postcode_user" => $postcode, "city_user" => $city, "password_user" => password_hash($password, PASSWORD_DEFAULT)])) {
            echo "Account successfully created!";
        } else {
            echo "Error: " . $stmt->errorInfo()[2];
        }
    } else {
        echo "Register : Please fill in all fields correctly.";
    }
}
// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update'])) {
        $id = $_POST['id'];
        $lastname = $_POST['lastname'];
        $name = $_POST['name'];
        $email = $_POST['email'];

        $stmt = $pdo->prepare("UPDATE users SET lastname_user = :lastname_user, name_user = :name_user, email_user = :email_user WHERE id_user = :id_user");
        $params = [
            'lastname_user' => $lastname,
            'name_user' => $name,
            'email_user' => $email,
            // 'phone_user' => $phone,
            // 'address_user' => $address,
            // 'postcode_user' => $postcode,
            // 'city_user' => $city,
            'id_user' => $id
        ];
        $stmt->execute($params);
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];

        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
    } elseif (isset($_POST['dispute'])) {
        $userId = $_POST['user_id'];
        $reason = $_POST['reason'];

        $stmt = $pdo->prepare("INSERT INTO disputes (user_id, reason) VALUES (?, ?)");
        $stmt->execute([$userId, $reason]);
    }
}

// Fetch users
$users = $pdo->query("SELECT * FROM users")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Clients</title>
    <link rel="stylesheet" href="../styles/style.css">
</head>
<body>
    <?php include '../navbar.php'; ?>
    <h1>Manage Clients</h1>

    <h2>Create User</h2>
    <form action="manage_client.php" method="POST">
        <input type="hidden" name="form_type" value="register">
        <div class="form-group">
            <label for="lastname">Nom de famille :</label><br>
            <input type="text" id="lastname" name="lastname" required><br><br>

            <label for="name">Prénom :</label><br>
            <input type="text" id="name" name="name" required><br><br>

            <label for="email">E-mail :</label><br>
            <input type="email" id="email" name="email" required><br><br>

            <label for="phone">Téléphone :</label><br>
            <input type="text" id="phone" name="phone"><br><br>

            <label for="address">Adresse :</label><br>
            <textarea id="address" name="address"></textarea><br><br>

            <label for="postcode">Code postal :</label><br>
            <input type="text" id="postcode" name="postcode"><br><br>

            <label for="city">Ville :</label><br>
            <input type="text" id="city" name="city"><br><br>

            <label for="reg-password">Mot de passe :</label>
            <input type="password" id="reg-password" name="password" required>
        </div>
        <div class="form-group">
            <button type="submit">Register</button>
        </div>
    </form>

    <h2>Users</h2>
    <table border="1">
        <thead>
            <tr>
                <th>Nom de famille</th>
                <th>Prénom</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr><?php echo var_dump($user) ?>
                    <td><?= htmlspecialchars($user['lastname_user']) ?></td>
                    <td><?= htmlspecialchars($user['name_user']) ?></td>
                    <td><?= htmlspecialchars($user['email_user']) ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $user['id_user'] ?>">
                            <input type="text" name="lastname" value="<?= $user['lastname_user'] ?>">
                            <input type="text" name="name" value="<?= htmlspecialchars($user['name_user']) ?>" required>
                            <input type="email" name="email" value="<?= htmlspecialchars($user['email_user']) ?>" required>
                            <button type="submit" name="update">Update</button>
                        </form>

                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $user['id_user'] ?>">
                            <button type="submit" name="delete">Delete</button>
                        </form>

                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="user_id" value="<?= $user['id_user'] ?>">
                            <input type="text" name="reason" placeholder="Dispute Reason" required>
                            <button type="submit" name="dispute">Open Dispute</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html></form></td></tbody></form>