<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $name = htmlspecialchars($_POST['name']);
    $lastname = htmlspecialchars($_POST['lastname']);
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);
    $address = htmlspecialchars($_POST['address']);
    $postcode = htmlspecialchars($_POST['postcode']);
    $city = htmlspecialchars($_POST['city']);

    // Validate form data (basic example)
    if (!empty($name) && !empty($lastname) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Here you can add code to save the data to a database
        echo "Account successfully created!";
    } else {
        echo "Please fill in all fields correctly.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="styles/style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <h1>Register</h1>
    <form method="POST" action="">
        <label for="name">Name:</label><br>
        <input type="text" id="name" name="name" required><br><br>

        <label for="lastname">Last Name:</label><br>
        <input type="text" id="lastname" name="lastname" required><br><br>

        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br><br>

        <label for="phone">Phone Number:</label><br>
        <input type="text" id="phone" name="phone"><br><br>

        <label for="address">Address:</label><br>
        <textarea id="address" name="address"></textarea><br><br>

        <label for="postcode">Postal Code:</label><br>
        <input type="text" id="postcode" name="postcode"><br><br>

        <label for="city">City:</label><br>
        <input type="text" id="city" name="city"><br><br>

        <button type="submit">Register</button>
    </form>
</body>
</html>