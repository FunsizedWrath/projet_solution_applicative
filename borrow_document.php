<?php
// borrow_document.php

require_once 'database/db_connection.php';

session_start();

$role = $_SESSION['role'] ?? null;
if ($role === null) {
    header("Location: login.php");
    exit();
} elseif ($role < 1 || $role >= 4) {
    echo "Access denied.";
    exit();
}

// Check if the document ID is provided in the GET parameter
if (!isset($_GET['id_document']) || empty($_GET['id_document'])) {
    die("Document ID is required.");
}

$search = null;
if (isset($_GET["search"]) && !empty($_GET["search"])) {
    $search = $_GET["search"];
}

$id_document = $_GET['id_document'];

// Process the post request to create an entry in the borrowed table
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? null;
    // Sanitize and validate the email address format
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format.";
        exit;
    }

    $id_document = $_POST["id_document"] ?? null;

    if ($email) {
        // Step 1: Retrieve the user ID based on the email
        $stmt = $pdo->prepare("SELECT id_user FROM users WHERE email_user = :email_user");
        $stmt->execute(["email_user" => $email]);
        $user = $stmt->fetch();

        if ($user) {
            $id_user = $user['id_user'];

            // Step 2: Insert the borrowed document record
            $stmt = $pdo->prepare("INSERT INTO borrowed (id_document, id_user, date_borrowed) VALUES (:id_document, :id_user, :date_borrowed)");
            $stmt->execute([
                "id_document" => $id_document,
                "id_user" => $id_user,
                "date_borrowed" => date("Y-m-d") // Use the correct date format for your database
            ]);

            $stmt = $pdo->prepare('UPDATE document SET available_document = 0 WHERE id_document = :id_document');
            $stmt->execute(['id_document' => $id_document]);
            $return_to_search = $search ? "&search=" . htmlspecialchars($search) : "";
            header("Location: library.php?message=Borrowed successfully!$return_to_search");
            exit;
        } else {
            echo "No user found with the provided email.";
        }
    } else {
        echo "Email is required.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrow Document</title>
    <link rel="stylesheet" href="styles/style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <h1>Borrow Document</h1>
    <form action="" method="post">
        <input type="hidden" name="id_document" value="<?php echo htmlspecialchars($id_document); ?>">
        <label for="email">Your Email Address:</label>
        <input type="email" id="email" name="email" required>
        <button type="submit">Borrow</button>
    </form>
</body>
</html>