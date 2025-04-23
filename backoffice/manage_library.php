<?php
require_once '../database/db_connection.php';

// Handle form submission for creating, updating, or deleting documents
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action === 'create') {
        $title = $_POST['title'];
        $type = $_POST['type'];
        $author = $_POST['author'];

        $stmt = $pdo->prepare("INSERT INTO documents (title, type, author) VALUES (?, ?, ?)");
        $stmt->execute([$title, $type, $author]);
    } elseif ($action === 'update') {
        $id = $_POST['id'];
        $title = $_POST['title'];
        $type = $_POST['type'];
        $author = $_POST['author'];

        $stmt = $pdo->prepare("UPDATE documents SET title = ?, type = ?, author = ? WHERE id = ?");
        $stmt->execute([$title, $type, $author, $id]);
    } elseif ($action === 'delete') {
        $id = $_POST['id'];

        $stmt = $pdo->prepare("DELETE FROM documents WHERE id = ?");
        $stmt->execute([$id]);
    }
}

// Fetch all documents
$stmt = $pdo->query("SELECT * FROM document");
$documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Documents</title>
    <link rel="stylesheet" href="../styles/style.css">
</head>
<body>
    <?php include '../navbar.php'; ?>
    <h1>Manage Documents</h1>

    <h2>Create Document</h2>
    <form method="POST">
        <input type="hidden" name="action" value="create">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required>
        <label for="type">Type:</label>
        <select id="type" name="type" required>
            <option value="book">Book</option>
            <option value="disk">Disk</option>
        </select>
        <label for="author">Author:</label>
        <input type="text" id="author" name="author" required>
        <button type="submit">Create</button>
    </form>

    <h2>Existing Documents</h2>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Type</th>
                <th>Author</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($documents as $document): ?>
                <tr>
                    <td><?= htmlspecialchars($document['id']) ?></td>
                    <td><?= htmlspecialchars($document['title']) ?></td>
                    <td><?= htmlspecialchars($document['type']) ?></td>
                    <td><?= htmlspecialchars($document['author']) ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($document['id']) ?>">
                            <button type="submit">Delete</button>
                        </form>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($document['id']) ?>">
                            <input type="text" name="title" value="<?= htmlspecialchars($document['title']) ?>" required>
                            <select name="type" required>
                                <option value="book" <?= $document['type'] === 'book' ? 'selected' : '' ?>>Book</option>
                                <option value="disk" <?= $document['type'] === 'disk' ? 'selected' : '' ?>>Disk</option>
                            </select>
                            <input type="text" name="author" value="<?= htmlspecialchars($document['author']) ?>" required>
                            <button type="submit">Update</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>