<?php
require_once '../database/db_connection.php';

session_start();

$role = $_SESSION['role'] ?? null;
if ($role === null) {
    header("Location: ../login.php");
    exit();
} elseif ($role < 1 || $role >= 4) {
    echo "Access denied.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['form_type']) && $_POST['form_type'] === 'register') {
    // Retrieve form data
    $lastname = htmlspecialchars($_POST['lastname']);
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $phone = $_POST['phone'] ? htmlspecialchars($_POST['phone']) : null;
    $address = $_POST['address'] ? htmlspecialchars($_POST['address']) : null;
    $postcode = $_POST['postcode'] ? htmlspecialchars($_POST['postcode']) : null;
    $city = $_POST['city'] ? htmlspecialchars($_POST['city']) : null;
    $id_role = htmlspecialchars($_POST['id_role']);
    $password = $_POST['password'] ? htmlspecialchars($_POST['password']) : null;

    // Validate form data (basic example)
    if (!empty($name) && !empty($lastname) && !empty($password) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Save the data to the database
        $stmt = $pdo->prepare("INSERT INTO users (lastname_user, name_user, email_user, phone_user, address_user, postcode_user, city_user, password_user, id_role) VALUES (:lastname_user, :name_user, :email_user, :phone_user, :address_user, :postcode_user, :city_user, :password_user, :id_role)");
        if ($stmt->execute(["lastname_user" => $lastname, "name_user" => $name, "email_user" => $email, "phone_user" => $phone, "address_user" => $address, "postcode_user" => $postcode, "city_user" => $city, "password_user" => password_hash($password, PASSWORD_DEFAULT), "id_role" => $id_role])) {
            echo "Account successfully created!";
        } else {
            echo "Error: " . $stmt->errorInfo()[2];
        }
    } else {
        echo "Register : Please fill in all fields correctly.";
    }
} elseif (isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = $_POST['id'];

    $stmt = $pdo->prepare("DELETE FROM users WHERE id_user = :id_user");
    $stmt->execute([':id_user' => $id]);
}
// Handle form submissions
// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     if (isset($_POST['update'])) {
//         $id = $_POST['id'];
//         $lastname = $_POST['lastname'];
//         $name = $_POST['name'];
//         $email = $_POST['email'];

//         $stmt = $pdo->prepare("UPDATE users SET lastname_user = :lastname_user, name_user = :name_user, email_user = :email_user WHERE id_user = :id_user");
//         $params = [
//             'lastname_user' => $lastname,
//             'name_user' => $name,
//             'email_user' => $email,
//             // 'phone_user' => $phone,
//             // 'address_user' => $address,
//             // 'postcode_user' => $postcode,
//             // 'city_user' => $city,
//             'id_user' => $id
//         ];
//         $stmt->execute($params);

// }

$roles = $pdo->query("SELECT * FROM role")->fetchAll(PDO::FETCH_ASSOC);

$roles = array_filter($roles, function ($r) use ($role) {
    return $r['id_role'] >= $role;
});

// Fetch users
$users = $pdo->query("SELECT * FROM users")->fetchAll(PDO::FETCH_ASSOC);

$search_term = "";
$search_term = $_GET['search'] ?? null;
if ($search_term != null) {
    $search_term = htmlspecialchars($search_term);
    $stmt = $pdo->prepare("SELECT u.* FROM users u WHERE u.email_user LIKE :search_term OR u.lastname_user LIKE :search_term OR u.name_user LIKE :search_term");
    $stmt->execute(['search_term' => '%' . $search_term . '%']);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

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

            <label for="id_role">Rôle :</label><br>
            <select id="id_role" name="id_role" required>
                <?php foreach ($roles as $role): ?>
                    <option value="<?= $role['id_role'] ?>">
                        <?= htmlspecialchars($role['name_role']) ?>
                    </option>
                <?php endforeach; ?>
            </select><br><br>

            <label for="reg-password">Mot de passe :</label>
            <input type="password" id="reg-password" name="password" required>
        </div>
        <div class="form-group">
            <button type="submit">Register</button>
        </div>
    </form>

    <h2>Users</h2>
    <div class="search-container">
        <form method="GET" action="">
            <input type="text" name="search" placeholder="Chercher un utilisateur..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
            <button type="submit">Rechercher</button>
        </form>
    </div>
    <?php if (count($users) < 1): ?>
        Aucun utilisateur trouvé.
    <?php else: ?>
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
                <tr>
                    <td><?= htmlspecialchars($user['lastname_user']) ?></td>
                    <td><?= htmlspecialchars($user['name_user']) ?></td>
                    <td><?= htmlspecialchars($user['email_user']) ?></td>
                    <td>
                        <a href="update_client.php?id_user=<?= $user['id_user'] ?>" style="display: inline-block;">
                            <button type="button">Update</button>
                        </a>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $user['id_user'] ?>">
                            <button type="submit">Delete</button>
                        </form>

                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</body>
</html></form></td></tbody></form>