<?php require_once 'database/document_type_enum.php'; ?>

<table border="1" class="document-table">
    <thead>
        <tr>
            <th>Title</th>
            <th>Type</th>
            <th>Infos</th>
            <?php if (!$hide_document_actions): ?> <th>Actions</th> <?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($documents as $document): ?>
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
                <?php if (!$hide_document_actions): ?>
                    <td>
                        <a href="update_document.php?id_document=<?= $document['id_document'] ?>&type=<?= $document['type_document']->name ?>" style="display: inline-block;">
                            <button type="button">Update</button>
                        </a>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $document['id_document'] ?>">
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                <?php endif; ?>

            </tr>
        <?php endforeach; ?>
    </tbody>
</table>