<?php
// Database connection
$db = new PDO('sqlite:../path_to_your_database.db'); // Update the path to your SQLite database

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action === 'create') {
            $name = $_POST['name'];
            $price = $_POST['price'];
            $stmt = $db->prepare("INSERT INTO subscription_type (name, price) VALUES (:name, :price)");
            $stmt->execute([':name' => $name, ':price' => $price]);
        } elseif ($action === 'update') {
            $id = $_POST['id'];
            $name = $_POST['name'];
            $price = $_POST['price'];
            $stmt = $db->prepare("UPDATE subscription_type SET name = :name, price = :price WHERE id = :id");
            $stmt->execute([':id' => $id, ':name' => $name, ':price' => $price]);
        } elseif ($action === 'delete') {
            $id = $_POST['id'];
            $stmt = $db->prepare("DELETE FROM subscription_type WHERE id = :id");
            $stmt->execute([':id' => $id]);
        }
    }
}

// Fetch all subscription types
$subscriptionTypes = $db->query("SELECT * FROM subscription_type")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Subscription Types</title>
</head>
<body>
    <h1>Manage Subscription Types</h1>

    <h2>Create Subscription Type</h2>
    <form method="POST">
        <input type="hidden" name="action" value="create">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>
        <label for="price">Price:</label>
        <input type="number" id="price" name="price" step="0.01" required>
        <button type="submit">Create</button>
    </form>

    <h2>Existing Subscription Types</h2>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Price</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($subscriptionTypes as $type): ?>
                <tr>
                    <td><?= htmlspecialchars($type['id']) ?></td>
                    <td><?= htmlspecialchars($type['name']) ?></td>
                    <td><?= htmlspecialchars($type['price']) ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($type['id']) ?>">
                            <input type="text" name="name" value="<?= htmlspecialchars($type['name']) ?>" required>
                            <input type="number" name="price" value="<?= htmlspecialchars($type['price']) ?>" step="0.01" required>
                            <button type="submit">Update</button>
                        </form>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($type['id']) ?>">
                            <button type="submit" onclick="return confirm('Are you sure you want to delete this subscription type?');">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>