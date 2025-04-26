<?php
// Include database connection
require_once '../database/db_connection.php';
include '../database/document_type_enum.php';

// Check if the document ID is provided
if (!isset($_GET['id_document']) || empty($_GET['id_document'])) {
    die('Document ID is required.');
}

if (!isset($_GET['type']) || empty($_GET['type'])) {
    die('Document type is required.');
}

$document_id = intval($_GET['id_document']);
$document_type = type_document::fromName($_GET['type']);
$message = '';

// Fetch document data
$stmt = $pdo->prepare("
    SELECT document.*, book.*, location.name_location AS location_document
    FROM document
    LEFT JOIN book ON document.id_document = book.id_document
    LEFT JOIN location ON document.id_location = location.id_location
    WHERE document.id_document = :id_document
");
$stmt->execute(['id_document' => $document_id]);
$document = $stmt->fetch(PDO::FETCH_ASSOC);

if ($document->num_rows === 0) {
    die('Document not found.');
}
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
    <link rel="stylesheet" href="../styles/style.css">
    <title>Edit Document</title>
</head>

<body>
<?php include '../navbar.php'; ?>
    <h1>Edit Document</h1>
    <?php if (!empty($message)): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
    <form method="POST">
        <label for="title">Title:</label><br>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($document['title_document']); ?>" required><br><br>
        <button type="submit">Update Document</button>
    </form>

    <method="POST">
        <input type="hidden" name="action" value="create">
        <label for="type">Type : <?=$document_type == type_document::Book ? 'Livre' : 'Disque'?></label>
        <label for="title">Titre :</label>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($document['title_document']); ?>" required>
        <label for="description">Description :</label>
        <textarea id="description" name="description"><?php echo htmlspecialchars($document['description_document']); ?></textarea>
        <label for="publishing_date">Date de publication :</label>
        <input type="date" id="publishing_date" name="publishing_date" value="<?php echo htmlspecialchars($document['publishing_date_document']); ?>" required>
        <label for="acquisition_date">Date d'acquisition :</label>
        <input type="date" id="acquisition_date" name="acquisition_date" value="<?php echo htmlspecialchars($document['acquisition_date_document']); ?>" required>
        <label for="location">Emplacement :</label>
        <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($document['location_document']); ?>" required>

        <?php if ($document_type == type_document::Book): ?>

            <div id="book-fields">
            <label for="author_book">Auteur :</label>
            <input type="text" id="author_book" name="author_book" value="<?php echo htmlspecialchars($document['author_book']); ?>" required>
            <label for="nbr_words_book">Nombre de mots :</label>
            <input type="number" id="nbr_words_book" name="nbr_words_book" value="<?php echo htmlspecialchars($document['nbr_words_book']); ?>">
            <label for="publisher_book">Editeur :</label>
            <input type="text" id="publisher_book" name="publisher_book" value="<?php echo htmlspecialchars($document['publisher_book']); ?>">
            </div>

        <?php elseif ($document_type == type_document::Disk): ?>
            <div id="disk-fields">
            <label for="artist_disk">Artiste :</label>
            <input type="text" id="artist_disk" name="artist_disk" value="<?php echo htmlspecialchars($document['artist_disk']); ?>">
            <label for="producer_disk">Producteur :</label>
            <input type="text" id="producer_disk" name="producer_disk" value="<?php echo htmlspecialchars($document['producer_disk']); ?>">
            <label for="director_disk">Directeur :</label>
            <input type="text" id="director_disk" name="director_disk" value="<?php echo htmlspecialchars($document['director_disk']); ?>">
            </div>
        <?php endif; ?>

        <button type="submit">Update</button>
        </form>
</body>

</html>