<?php
// Start session
session_start();

// Include database connection
require_once 'database/db_connection.php';
require_once 'database/document_type_enum.php';

$result = [];
$search_term = "";
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $message = $_GET['message'] ?? null;
    if ($message != null) {
        echo htmlspecialchars($message);
    }
    $search_term = $_GET['search'] ?? null;
    if ($search_term != null) {
        $search_term = htmlspecialchars($search_term);
        $stmt = $pdo->prepare("SELECT d.*, b.author_book, b.nbr_words_book, b.publisher_book, di.artist_disk, di.producer_disk, di.director_disk, l.* FROM document d LEFT JOIN book b ON d.id_document = b.id_document LEFT JOIN disk di ON d.id_document = di.id_document LEFT JOIN location l ON d.id_location = l.id_location WHERE d.title_document LIKE :search_term OR d.description_document LIKE :search_term");
        $stmt->execute(['search_term' => '%' . $search_term . '%']);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
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
    <div class="results-container">
        <?php if (!empty($result)): ?>
            <h2>Résultats de la recherche :</h2>
            <table border="1" class="document-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Infos</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($result as $document): ?>
                        <?php $document['type_document'] = array_key_exists('author_book', $document) && $document['author_book'] != null
                            ? type_document::Book
                            : type_document::Disk ?>
                        <tr>
                            <td><?= htmlspecialchars($document['title_document']) ?></td>
                            <td><?=
                                $document['type_document'] == type_document::Book ? 'Livre' : 'Disque'
                                ?></td>
                            <td>
                                <div style="display: flex; flex-direction: row; gap:2px; width: 100%;">
                                    <div class="document-details-table-cell">
                                        <!-- display the document details based on its type -->
                                        <?php if ($document['type_document'] == type_document::Book): ?>
                                            <p>Auteur : <?= htmlspecialchars($document['author_book']) ?></p>
                                            <p>Nombre de mots : <?= htmlspecialchars($document['nbr_words_book']) ?></p>
                                            <p>Editeur : <?= htmlspecialchars($document['publisher_book']) ?></p>
                                        <?php else : ?>
                                            <p>Artiste : <?= htmlspecialchars($document['artist_disk']) ?></p>
                                            <p>Producteur : <?= htmlspecialchars($document['producer_disk']) ?></p>
                                            <p>Directeur : <?= htmlspecialchars($document['director_disk']) ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="document-details-table-cell">
                                        <p>Description : <?= htmlspecialchars($document['description_document']) ?></p>
                                        <p>Date de publication : <?= htmlspecialchars($document['publishing_date_document']) ?></p>
                                        <p>Date d'acquisition : <?= htmlspecialchars($document['acquisition_date_document']) ?></p>
                                        <p>Emplacement : <?= htmlspecialchars($document['name_location']) ?></p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <a href="borrow_document.php?id_document=<?= $document['id_document'] ?>&search=<?= $search_term ?>" style="display: inline-block;">
                                    <button type="button">Emprunter</button>
                                </a>
                            </td>

                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Aucun document trouvé.</p>
        <?php endif; ?>
    </div>
</body>