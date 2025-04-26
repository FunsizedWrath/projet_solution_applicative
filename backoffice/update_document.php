<?php
// Include database connection
require_once '../database/db_connection.php';
include '../database/document_type_enum.php';

// Check if the document ID is provided
if (!isset($_GET['id_document']) || empty($_GET['id_document'])) {
    die('Document ID is required.');
}

$documentId = intval($_GET['id_document']);
$message = '';

// Fetch document data
$query = $pdo->prepare("SELECT * FROM document WHERE id_document = :id_document");
$query->execute([':id_document' => $id]);
$result = $query->fetch();

if ($result->num_rows === 0) {
    die('Document not found.');
}
$document['type_document'] = array_key_exists('author_book', $document) && $document['author_book'] != null
    ? type_document::Book
    : type_document::Disk;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';

    if (!empty($title) && !empty($content)) {
        $updateQuery = $conn->prepare("UPDATE documents SET title = ?, content = ? WHERE id = ?");
        $updateQuery->bind_param("ssi", $title, $content, $documentId);

        if ($updateQuery->execute()) {
            $message = 'Document updated successfully.';
            // Refresh document data
            $document['title'] = $title;
            $document['content'] = $content;
        } else {
            $message = 'Failed to update document.';
        }
    } else {
        $message = 'All fields are required.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Document</title>
</head>
<body>
    <h1>Edit Document</h1>
    <?php if (!empty($message)): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
    <form method="POST">
        <label for="title">Title:</label><br>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($document['title']); ?>" required><br><br>

        <label for="content">Content:</label><br>
        <textarea id="content" name="content" rows="10" cols="50" required><?php echo htmlspecialchars($document['content']); ?></textarea><br><br>

        <button type="submit">Update Document</button>
    </form>
</body>
</html>