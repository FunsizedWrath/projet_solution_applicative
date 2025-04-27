<?php
// Include database connection
require_once '../database/db_connection.php';
require_once '../database/document_type_enum.php';

session_start();

$role = $_SESSION['role'] ?? null;
if ($role === null) {
    header("Location: login.php");
    exit();
} elseif ($role < 1 || $role >= 4) {
    echo "Access denied.";
    exit();
}

// Check if the document ID is provided
if (!isset($_GET['id_document']) || empty($_GET['id_document'])) {
    die('Document ID is required.');
}

if (!isset($_GET['type']) || empty($_GET['type'])) {
    die('Document type is required.');
}

$document_id = intval($_GET['id_document']);
$document_type = type_document::fromName($_GET['type']);
$document_type_str = $document_type == type_document::Book ? 'book' : 'disk';
$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $publishing_date = $_POST['publishing_date'];
    $acquisition_date = $_POST['acquisition_date'];
    $location = $_POST['location'];

    if (!empty($title) && !empty($publishing_date) && !empty($acquisition_date) && !empty($location)) {
        // Update common fields in the document table
        $stmt = $pdo->prepare("
            UPDATE document
            SET
                title_document = :title_document,
                description_document = :description_document,
                publishing_date_document = :publishing_date_document,
                acquisition_date_document = :acquisition_date_document,
                id_location = (SELECT id_location FROM location WHERE name_location = :location_document)
            WHERE id_document = :id_document
        ");
        $params = [
            'id_document' => $document_id,
            'title_document' => $title,
            'description_document' => $description,
            'publishing_date_document' => $publishing_date,
            'acquisition_date_document' => $acquisition_date,
            'location_document' => $location
        ];
        $stmt->execute($params);

        // Handle specific fields based on document type
        if ($document_type == type_document::Book) {
            $author_book = $_POST['author_book'];
            $nbr_words_book = $_POST['nbr_words_book'];
            $publisher_book = $_POST['publisher_book'];

            $stmt = $pdo->prepare("
                UPDATE book
                SET
                    author_book = :author_book,
                    nbr_words_book = :nbr_words_book,
                    publisher_book = :publisher_book
                WHERE id_document = :id_document
            ");
            $params = [
                'id_document' => $document_id,
                'author_book' => $author_book,
                'nbr_words_book' => $nbr_words_book,
                'publisher_book' => $publisher_book
            ];
            $stmt->execute($params);
        } elseif ($document_type == type_document::Disk) {
            $artist_disk = $_POST['artist_disk'];
            $producer_disk = $_POST['producer_disk'];
            $director_disk = $_POST['director_disk'];

            $stmt = $pdo->prepare("
                UPDATE disk
                SET
                    artist_disk = :artist_disk,
                    producer_disk = :producer_disk,
                    director_disk = :director_disk
                WHERE id_document = :id_document
            ");
            $params = [
                'id_document' => $document_id,
                'artist_disk' => $artist_disk,
                'producer_disk' => $producer_disk,
                'director_disk' => $director_disk
            ];
            $stmt->execute($params);
        }

        if ($stmt->rowCount() > 0) {
            $message = 'Document updated successfully.';
        } else {
            $message = 'Failed to update document.';
        }
    } else {
        $message = 'Missing required fields.';
    }
}

$stmt = $pdo->prepare("
    SELECT document.*, $document_type_str.*, location.name_location AS location_document
    FROM document
    LEFT JOIN $document_type_str ON document.id_document = $document_type_str.id_document
    LEFT JOIN location ON document.id_location = location.id_location
    WHERE document.id_document = :id_document
");
$stmt->execute(['id_document' => $document_id]);
$document = $stmt->fetch(PDO::FETCH_ASSOC);

if ($document->num_rows === 0) {
    die('Document not found.');
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

    <form method="POST" action="">
        <input type="hidden" name="action" value="update">
        <label for="type">Type : <?= $document_type == type_document::Book ? 'Livre' : 'Disque' ?></label>

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
                <input type="text" id="artist_disk" name="artist_disk" value="<?php echo htmlspecialchars($document['artist_disk']); ?>" required>

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