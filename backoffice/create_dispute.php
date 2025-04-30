<?php
// Include the database connection file
require_once '../database/db_connection.php';

// Initialize variables for error/success messages
$message = '';

if (!isset($_GET['id_document']) || !isset($_GET['id_user'])) {
    $message = 'Error: id_document and id_user must be provided.';
}

// Retrieve id_user and id_document from GET parameters
$id_document = $_GET['id_document'];

try {
    // Use the $pdo variable from db_connection.php
    global $pdo;

    // Fetch the document details from the database
    $stmt = $pdo->prepare('SELECT * FROM Document WHERE id_document = :id_document');
    $stmt->execute(['id_document' => $id_document]);
    $document = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$document) {
        $message = 'Error: Document not found.';
    }
} catch (PDOException $e) {
    $message = 'Database error: ' . $e->getMessage();
}

$id_user = $_GET['id_user'];

try {
    // Fetch the user details from the database
    $stmt = $pdo->prepare('SELECT * FROM Users WHERE id_user = :id_user');
    $stmt->execute(['id_user' => $id_user]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $message = 'Error: User not found.';
    }
} catch (PDOException $e) {
    $message = 'Database error: ' . $e->getMessage();
}

// Get all the dispute types from the database
$dispute_types = [];
try {
    // Fetch the user details from the database
    $stmt = $pdo->prepare('SELECT * FROM Dispute_type');
    $stmt->execute();
    $dispute_types = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$dispute_types || count($dispute_types) == 0) {
        $message = 'Error: No Dispute Types.';
    }
} catch (PDOException $e) {
    $message = 'Database error: ' . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if all required fields are provided in the POST request
    if (!isset($_POST['description_dispute'], $_POST['dispute_type'])) {
        $message = 'Error: All fields are required.';
    } else {
        $description_dispute = trim($_POST['description_dispute']);
        $status_dispute = trim($_POST['status_dispute']);
        $dispute_type = trim($_POST['dispute_type']);

        try {
                // Insert the new dispute into the disputes table
                $stmt = $pdo->prepare('INSERT INTO Dispute (id_document, id_user, description_dispute, status_dispute, id_dispute_type, start_date_dispute) VALUES (:id_document, :id_user, :description_dispute, :status_dispute, :id_dispute_type, :start_date_dispute)');
                $stmt->execute([
                    ':id_document' => $id_document,
                    ':id_user' => $id_user,
                    ':description_dispute' => $description_dispute,
                    ':status_dispute' => $status_dispute,
                    ':id_dispute_type' => $dispute_type,
                    ':start_date_dispute' => date('Y-m-d')
                ]);

                $message = 'Dispute created successfully.';
                header("Location: update_client.php?section=active-disputes&id_user=$id_user");
        } catch (PDOException $e) {
            $message = 'Database error: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Dispute</title>
    <link rel="stylesheet" href="../styles/style.css">
</head>
<body>
    <?php include '../navbar.php'; ?>
    <h1>Create Dispute</h1>
    <?php if (!empty($message)): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
    <?php if ($id_document !== null && $id_user !== null): ?>
        <form method="POST" action="">
            <p>Document ID: <?php echo htmlspecialchars($document['title_document']); ?></p>
            <p>User ID: <?php echo htmlspecialchars($user['email_user']); ?></p>
            <input type="hidden" name="id_document" value="<?php echo htmlspecialchars($id_document); ?>">
            <input type="hidden" name="id_user" value="<?php echo htmlspecialchars($id_user); ?>">
            <input type="text" name="status_dispute" value="Actif">
            <label for="description_dispute">Dispute Description:</label>
            <textarea id="description_dispute" name="description_dispute" required></textarea>
            <br>
            <label for="dispute_type">Dispute Type:</label>
            <select id="dispute_type" name="dispute_type" required>
                <?php foreach ($dispute_types as $dispute_type): ?>
                    <option value="<?php echo htmlspecialchars($dispute_type['id_dispute_type']); ?>">
                        <?php echo htmlspecialchars($dispute_type['name_dispute_type']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <br>
            <button type="submit">Create Dispute</button>
        </form>
    <?php else: ?>
        <p>Error: id_document and id_user must be provided as GET parameters.</p>
    <?php endif; ?>
</body>
</html>