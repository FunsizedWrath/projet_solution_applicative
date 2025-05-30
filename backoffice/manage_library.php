<?php
require_once '../database/db_connection.php';

session_start();

$role = $_SESSION['role'] ?? null;
if ($role === null) {
    header("Location: login.php");
    exit();
} elseif ($role < 1 || $role >= 4) {
    echo "Access denied.";
    exit();
}

// Handle form submission for creating, updating, or deleting documents
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        // Document fields
        $type = $_POST['type'];
        $title = $_POST['title'];
        $description = $_POST['description'];
        $publishing_date = $_POST['publishing_date'];
        $acquisition_date = $_POST['acquisition_date'];
        $id_location = $_POST['id_location'];

        // Book-specific fields
        $author = $_POST['author'] ?? null;
        $nbr_words = $_POST['nbr_words'] ?? null;
        $publisher = $_POST['publisher'] ?? null;

        // Disk-specific fields
        $artist = $_POST['artist'] ?? null;
        $producer = $_POST['producer'] ?? null;
        $director = $_POST['director'] ?? null;

        $stmt = $pdo->prepare("INSERT INTO document (title_document, publishing_date_document, description_document, acquisition_date_document, id_location) VALUES (:title_document, :publishing_date_document, :description_document, :acquisition_date_document, :id_location)");
        $stmt->execute([
            'title_document' => $title,
            'publishing_date_document' => $publishing_date,
            'description_document' => $description,
            'acquisition_date_document' => $acquisition_date,
            'id_location' => $id_location,
        ]);
        $last_insert_id = $pdo->lastInsertId();
        if ($type == 'book') {
            $stmt = $pdo->prepare("INSERT INTO book (id_document, author_book, nbr_words_book, publisher_book) VALUES (:last_insert_id, :author_book, :nbr_words_book, :publisher_book)");
            $stmt->execute([
                'last_insert_id' => $last_insert_id,
                'author_book' => $author,
                'nbr_words_book' => $nbr_words,
                'publisher_book' => $publisher,
            ]);
        } elseif ($type == 'disk') {
            $stmt = $pdo->prepare("INSERT INTO disk (id_document, artist_disk, producer_disk, director_disk) VALUES (:last_insert_id, :artist_disk, :producer_disk, :director_disk)");
            $stmt->execute([
                'last_insert_id' => $last_insert_id,
                'artist_disk' => $artist,
                'producer_disk' => $producer,
                'director_disk' => $director,
            ]);
        }
    } elseif ($action === 'delete') {
        $id = $_POST['id'];

        $stmt = $pdo->prepare("DELETE FROM document WHERE id_document = :id_document");
        $stmt->execute([':id_document' => $id]);
    }
}

$locations = $pdo->query("SELECT * FROM location")->fetchAll(PDO::FETCH_ASSOC);

// // Fetch all documents
// $stmt = $pdo->query("SELECT * FROM document");
// $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all books and disks for each document
$stmt = $pdo->query("SELECT d.*, b.author_book, b.nbr_words_book, b.publisher_book, di.artist_disk, di.producer_disk, di.director_disk, l.* FROM document d LEFT JOIN book b ON d.id_document = b.id_document LEFT JOIN disk di ON d.id_document = di.id_document LEFT JOIN location l ON d.id_location = l.id_location");
$documents = $stmt->fetchAll(PDO::FETCH_ASSOC);


include '../database/document_type_enum.php';

$result = [];
$search_term = "";
$search_term = $_GET['search'] ?? null;
if ($search_term != null) {
    $search_term = htmlspecialchars($search_term);
    $stmt = $pdo->prepare("SELECT d.*, b.author_book, b.nbr_words_book, b.publisher_book, di.artist_disk, di.producer_disk, di.director_disk, l.* FROM document d LEFT JOIN book b ON d.id_document = b.id_document LEFT JOIN disk di ON d.id_document = di.id_document LEFT JOIN location l ON d.id_location = l.id_location WHERE d.title_document LIKE :search_term OR d.description_document LIKE :search_term");
    $stmt->execute(['search_term' => '%' . $search_term . '%']);
    $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
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
        <label for="type">Type :</label>
        <select id="type" name="type" required>
            <option value="book">Livre</option>
            <option value="disk">Disque</option>
        </select>

        <label for="title">Titre :</label>
        <input type="text" id="title" name="title" required>

        <label for="description">Description :</label>
        <textarea id="description" name="description"></textarea>

        <label for="publishing_date">Date de publication :</label>
        <input type="date" id="publishing_date" name="publishing_date" required>

        <label for="acquisition_date">Date d'acquisition :</label>
        <input type="date" id="acquisition_date" name="acquisition_date" required>

        <!-- <label for="id_location">Emplacement :</label>
        <input type="text" id="id_location" name="id_location" required> -->

        <label for="id_location">Emplacement :</label><br>
            <select id="id_location" name="id_location" required>
                <?php foreach ($locations as $location): ?>
                    <option value="<?= $location['id_location'] ?>">
                        <?= htmlspecialchars($location['name_location']) ?>
                    </option>
                <?php endforeach; ?>
            </select><br><br>

        <div id="dynamic-form"></div>

        <!-- <div id="book-fields">
            <label for="author">Auteur :</label>
            <input type="text" id="author" name="author" required>
            <label for="nbr_words">Nombre de mots :</label>
            <input type="number" id="nbr_words" name="nbr_words">
            <label for="publisher">Editeur :</label>
            <input type="text" id="publisher" name="publisher">
        </div>

        <div id="disk-fields">
            <label for="artist">Artiste :</label>
            <input type="text" id="artist" name="artist">
            <label for="producer">Producteur :</label>
            <input type="text" id="producer" name="producer">
            <label for="director">Directeur :</label>
            <input type="text" id="director" name="director">
        </div> -->

        <script>
            let refreshFields = function () {
                const dynamicForm = document.getElementById('dynamic-form');
                const type = document.getElementById('type').value;

                if (type === 'book') {
                    dynamicForm.innerHTML = `
                    <div id="book-fields">
                        <label for="author">Auteur :</label>
                        <input type="text" id="author" name="author" required>
                        <label for="nbr_words">Nombre de mots :</label>
                        <input type="number" id="nbr_words" name="nbr_words">
                        <label for="publisher">Editeur :</label>
                        <input type="text" id="publisher" name="publisher">
                    </div>`;
                } else if (type === 'disk') {
                    dynamicForm.innerHTML = `
                    <div id="disk-fields">
                        <label for="artist">Artiste :</label>
                        <input type="text" id="artist" name="artist" required>
                        <label for="producer">Producteur :</label>
                        <input type="text" id="producer" name="producer">
                        <label for="director">Directeur :</label>
                        <input type="text" id="director" name="director">
                    </div>`;
                } else {
                    dynamicForm.innerHTML = "";
                }
            };
            refreshFields(); // Call on page load
            document.getElementById('type').addEventListener('change', refreshFields);
        </script>

        <button type="submit">Create</button>
    </form>

    <h2>Existing Documents</h2>
    <div class="search-container">
        <form method="GET" action="manage_library.php">
            <input type="text" name="search" placeholder="Chercher un document..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
            <button type="submit">Rechercher</button>
        </form>
    </div>
    <?php $hide_document_actions = false; include '../display_documents.php'; ?>
</body>
</html>