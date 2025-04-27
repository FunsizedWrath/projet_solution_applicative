<?php
// Start session
session_start();

// Include database connection
include 'database/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['form_type']) && $_POST['form_type'] === 'login') {
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];

    if (!empty($email) && !empty($password)) {
        // Prepare and execute query
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email_user = :email_user");
        $stmt->execute(['email_user' => $email]);
        $result = $stmt->fetch();

        if ($result != null) {
            // Verify password
            if (password_verify($password, $result['password_user'])) {
                $_SESSION['user_id'] = $result['id_user'];
                $_SESSION['role'] = $result['id_role'];
                echo "Login successful!";
                // Redirect to the home page
                header("Location: index.php");
                exit();
            } else {
                error_log("Password verification failed for user: " . $email);
                echo "Invalid password.";
            }
        } else {
            echo "No account found with that email.";
        }
    } else {
        echo "Login : Please fill in all fields.";
    }
}

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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && (!isset($_POST['form_type']) || ($_POST['form_type'] !== 'login' && $_POST['form_type'] !== 'register'))) {
    echo "Invalid request.";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles/style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container">
        <div class="login-container">
            <h2>Login</h2>
            <form action="login.php" method="POST">
                <input type="hidden" name="form_type" value="login">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <button type="submit">Login</button>
                </div>
            </form>
        </div>
        <div class="login-container">
            <h2>Register</h2>
            <form action="login.php" method="POST">
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
        </div>
    </div>
</body>
</html>