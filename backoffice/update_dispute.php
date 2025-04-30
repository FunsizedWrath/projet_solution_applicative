<?php
// Include the database connection file
require_once '../database/db_connection.php';

// Initialize variables for error/success messages
$message = '';

if (!isset($_GET['id_dispute'])) {
    $message = 'Error: id_dispute must be provided.';
}

$id_dispute = $_GET['id_dispute'];

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
    // Check if the action is to delete the dispute
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        try {
            // Delete the dispute from the database
            $stmt = $pdo->prepare('DELETE FROM Dispute WHERE id_dispute = :id_dispute');
            $stmt->execute(['id_dispute' => $_POST['id_dispute']]);
            echo 'Dispute deleted successfully.';
            header("Location: update_client.php?section=active-disputes&id_user=" . $_POST['id_user']);
            exit;
        } catch (PDOException $e) {
            $message = 'Database error: ' . $e->getMessage();
        }
    }

    // Check if all required fields are provided in the POST request
    if (!isset($_POST['description_dispute'], $_POST['dispute_type'])) {
        $message = 'Error: All fields are required.';
    } else {
        $description_dispute = trim($_POST['description_dispute']);
        $status_dispute = trim($_POST['status_dispute']);
        $dispute_type = trim($_POST['dispute_type']);

        try {
            $optional_parameters = "";
            if (isset($_POST['end_date_dispute']) && !empty($_POST['end_date_dispute'])) {
                $optional_parameters = ", end_date_dispute = :end_date_dispute";
            }
            $params = [
                'id_dispute' => $id_dispute,
                'description_dispute' => $description_dispute,
                'status_dispute' => $status_dispute,
                'id_dispute_type' => $dispute_type,
                'start_date_dispute' => $_POST['start_date_dispute'],
            ];
            if (isset($_POST['end_date_dispute']) && !empty($_POST['end_date_dispute'])) {
                $params['end_date_dispute'] = $_POST['end_date_dispute'];
            }

            // Insert the new dispute into the disputes table
            $stmt = $pdo->prepare("UPDATE Dispute SET description_dispute = :description_dispute, status_dispute = :status_dispute, id_dispute_type = :id_dispute_type, start_date_dispute = :start_date_dispute$optional_parameters WHERE id_dispute = :id_dispute");
            $stmt->execute($params);

            echo 'Dispute updated successfully.';
            $id_user = $_POST['id_user'];
            header("Location: update_client.php?section=active-disputes&id_user=$id_user");
        } catch (PDOException $e) {
            $message = 'Database error: ' . $e->getMessage();
        }
    }
}

$dispute = null;

try {

    $stmt = $pdo->prepare("SELECT * FROM dispute JOIN dispute_type ON dispute.id_dispute_type = dispute_type.id_dispute_type JOIN users ON dispute.id_user = users.id_user JOIN document ON dispute.id_document = document.id_document WHERE id_dispute = :id_dispute");
    $stmt->execute(['id_dispute' => $id_dispute]);
    $dispute = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$dispute) {
        $message = 'Error: No Dispute Types.';
    }
} catch(PDOException $e) {
    $message = 'Database error: ' . $e->getMessage();
}

if ($message) {
    echo $message;
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Contentieux</title>
    <link rel="stylesheet" href="../styles/style.css">
</head>
<body>
    <?php include '../navbar.php'; ?>
    <h1>Modifier Contentieux</h1>
    <?php if (!empty($message)): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
    <?php if ($id_dispute): ?>
        <form method="POST" action="">
            <p>Document : <?php echo htmlspecialchars($dispute['title_document']); ?></p>
            <p>Utilisateur : <?php echo htmlspecialchars($dispute['email_user']); ?></p>
            <input type="hidden" name="id_dispute" value="<?php echo htmlspecialchars($id_dispute); ?>">
            <input type="hidden" name="id_user" value="<?php echo htmlspecialchars($dispute['id_user']); ?>">
            <input type="text" name="status_dispute" value="<?= htmlspecialchars($dispute['status_dispute']); ?>">
            <label for="description_dispute">Description:</label>
            <textarea id="description_dispute" name="description_dispute" required><?php echo htmlspecialchars($dispute['description_dispute']); ?></textarea>
            <br>
            <label for="dispute_type">Type de contentieux:</label>
            <select id="dispute_type" name="dispute_type" required>
                <?php foreach ($dispute_types as $dispute_type): ?>
                    <option value="<?php echo htmlspecialchars($dispute_type['id_dispute_type']); ?>">
                        <?php echo htmlspecialchars($dispute_type['name_dispute_type']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <br>
            <label for="start_date_dispute">Date de création du contentieux :</label>
            <input type="date" id="start_date_dispute" name="start_date_dispute" value="<?php echo htmlspecialchars($dispute['start_date_dispute']); ?>" required>
            <br>
            <label for="end_date_dispute">Date de clôture du contentieux :</label>
            <input type="date" id="end_date_dispute" name="end_date_dispute" value="<?php echo htmlspecialchars($dispute['end_date_dispute']); ?>">
            <br>
            <button type="submit">Sauvegarder les modifications</button>
        </form>
        <form method="POST" action="">
            <input type="hidden" name="id_dispute" value="<?php echo htmlspecialchars($id_dispute); ?>">
            <input type="hidden" name="id_user" value="<?php echo htmlspecialchars($dispute['id_user']); ?>">
            <input type="hidden" name="action" value="delete">
            <button type="submit">Supprimer le contentieux</button>
        </form>
    <?php else: ?>
        <p>Error: id_dispute must be provided as GET parameter.</p>
    <?php endif; ?>
</body>
</html>