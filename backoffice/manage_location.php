<?php
require_once '../database/db_connection.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action === 'create') {
            $name = $_POST['name_location'];
            $description = $_POST['description_location'];
            $stmt = $pdo->prepare("INSERT INTO Location (name_location, description_location) VALUES (:name, :description)");
            $stmt->execute(['name' => $name, 'description' => $description]);
        } elseif ($action === 'update') {
            $id = $_POST['id_location'];
            $name = $_POST['name_location'];
            $description = $_POST['description_location'];
            $stmt = $pdo->prepare("UPDATE Location SET name_location = :name, description_location = :description WHERE id_location = :id");
            $stmt->execute(['id' => $id, 'name' => $name, 'description' => $description]);
        } elseif ($action === 'delete') {
            $id = $_POST['id_location'];
            $stmt = $pdo->prepare("DELETE FROM Location WHERE id_location = :id");
            $stmt->execute(['id' => $id]);
        }
    }
}

// Fetch all locations
$stmt = $pdo->query("SELECT * FROM Location");
$locations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Locations</title>
    <link rel="stylesheet" href="../styles/style.css">
</head>
<body>
    <?php include '../navbar.php'; ?>
    <h1>Manage Locations</h1>

    <h2>Create Location</h2>
    <form method="POST">
        <input type="hidden" name="action" value="create">
        <label for="name_location">Name:</label>
        <input type="text" id="name_location" name="name_location" required>
        <label for="description_location">Description:</label>
        <input type="text" id="description_location" name="description_location">
        <button type="submit">Create</button>
    </form>

    <h2>Existing Locations</h2>
    <table border="1">
        <thead>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($locations as $location): ?>
                <tr>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id_location" value="<?= htmlspecialchars($location['id_location']) ?>">
                            <td>
                                <input type="text" name="name_location" value="<?= htmlspecialchars($location['name_location']) ?>" required>
                            </td>
                            <td>
                                <input type="text" name="description_location" value="<?= htmlspecialchars($location['description_location']) ?>">
                            </td>
                            <td>

                                <button type="submit">Update</button>
                            </form>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id_location" value="<?= htmlspecialchars($location['id_location']) ?>">
                                <button type="submit">Delete</button>
                            </form>
                        </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>