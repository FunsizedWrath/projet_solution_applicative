<?php
require_once '../database/db_connection.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        $name = $_POST['name_subscription_type'] ?? '';
        $duration = $_POST['duration_subscription_type'] ?? '';
        $price = $_POST['price_subscription_type'] ?? 0;
        $id = $_POST['id_subscription_type'];

        if ($action === 'create' && !empty($name) && !empty($duration)) {
            $stmt = $pdo->prepare("INSERT INTO Subscription_type (name_subscription_type, duration_subscription_type, price_subscription_type) VALUES (:name, :duration, :price)");
            $stmt->execute(['name' => $name, 'duration' => $duration]);
        } elseif ($action === 'update' && !empty($id) && !empty($name) && !empty($duration)) {
            $stmt = $pdo->prepare("UPDATE Subscription_type SET name_subscription_type = :name, duration_subscription_type = :duration, price_subscription_type = :price WHERE id_subscription_type = :id");
            $stmt->execute(['id' => $id, 'name' => $name, 'duration' => $duration, 'price'=> $price]);
        } elseif ($action === 'delete' && !empty($id)) {
            $stmt = $pdo->prepare("DELETE FROM Subscription_type WHERE id_subscription_type = :id");
            $stmt->execute(['id' => $id]);
        }
    }
}

// Fetch all subscription types
$stmt = $pdo->query("SELECT * FROM Subscription_type");
$subscription_types = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Subscription Types</title>
    <link rel="stylesheet" href="../styles/style.css">
</head>
<body>
    <?php include '../navbar.php'; ?>
    <h1>Manage Subscription Types</h1>
    <form method="POST">
        <input type="hidden" name="action" value="create">
        <input type="text" name="name_subscription_type" placeholder="Nom" required>
        <input type="text" name="duration_subscription_type" placeholder="Durée" required>
        <input type="text" name="price_subscription_type" placeholder="Prix">
        <button type="submit">Create</button>
    </form>

    <table border="1">
        <thead>
            <tr>
                <th>Name</th>
                <th>Durée</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($subscription_types as $type): ?>
                <tr>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id_subscription_type" value="<?= htmlspecialchars($type['id_subscription_type']) ?>">
                        <td>
                            <input type="text" name="name_subscription_type" value="<?= htmlspecialchars($type['name_subscription_type']) ?>" required>
                        </td>
                        <td>
                            <input type="text" name="duration_subscription_type" value="<?= htmlspecialchars($type['duration_subscription_type']) ?>"required>
                        </td>
                        <td>
                            <input type="text" name="price_subscription_type" value="<?= htmlspecialchars($type['price_subscription_type']) ?>">
                        <td>
                            <button type="submit">Update</button>
                        </form>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id_subscription_type" value="<?= htmlspecialchars($type['id_subscription_type']) ?>">
                            <button type="submit" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>