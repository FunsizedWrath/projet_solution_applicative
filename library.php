<?php
// Start session
session_start();

// Include database connection
require_once 'database/db_connection.php';
require_once 'database/document_type_enum.php';
require_once 'display_argument_enum.php';

// Fetch all books and disks for each document
$stmt = $pdo->query("SELECT d.*, b.author_book, b.nbr_words_book, b.publisher_book, di.artist_disk, di.producer_disk, di.director_disk, l.* FROM document d LEFT JOIN book b ON d.id_document = b.id_document LEFT JOIN disk di ON d.id_document = di.id_document LEFT JOIN location l ON d.id_location = l.id_location");
$documents = $stmt->fetchAll(PDO::FETCH_ASSOC);

$search_term = "";
$search_term = $_GET['search'] ?? null;
if ($search_term != null) {
    $search_term = htmlspecialchars($search_term);
    $stmt = $pdo->prepare("SELECT d.*, b.author_book, b.nbr_words_book, b.publisher_book, di.artist_disk, di.producer_disk, di.director_disk, l.* FROM document d LEFT JOIN book b ON d.id_document = b.id_document LEFT JOIN disk di ON d.id_document = di.id_document LEFT JOIN location l ON d.id_location = l.id_location WHERE d.title_document LIKE :search_term OR d.description_document LIKE :search_term");
    $stmt->execute(['search_term' => '%' . $search_term . '%']);
    $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$locations = $pdo->query("SELECT * FROM location")->fetchAll(PDO::FETCH_ASSOC);

// // Fetch all books and disks for each document
// $stmt = $pdo->query("SELECT d.*, b.author_book, b.nbr_words_book, b.publisher_book, di.artist_disk, di.producer_disk, di.director_disk, l.* FROM document d LEFT JOIN book b ON d.id_document = b.id_document LEFT JOIN disk di ON d.id_document = di.id_document LEFT JOIN location l ON d.id_location = l.id_location WHERE d.title_document LIKE :search_term OR d.description_document LIKE :search_term");
// $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>library</title>

</head>

<body>
    <?php include 'navbar.php'; ?>
    <h1>Bibliothèque</h1>
    <div class="search-container">
        <form method="GET" action="library.php">
            <input type="text" name="search" placeholder="Chercher un document..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
            <button type="submit">Rechercher</button>
        </form>
    </div>
    <?php $display_argument = display_argument::Borrow; include 'display_documents.php'; ?>
</body>