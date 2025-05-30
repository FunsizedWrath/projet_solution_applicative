<?php
require_once 'database/document_type_enum.php';
require_once 'display_argument_enum.php';
?>

<table border="1" class="full-width-table">
    <thead>
        <tr>
            <th>Title</th>
            <th>Type</th>
            <th>Infos</th>
            <?php if ($display_argument == display_argument::NoAction): ?> <th>Status</th>
            <?php else: ?> <th>Actions</th> <?php endif; ?>
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
                <?php if ($display_argument == display_argument::Update): ?>
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
                <?php elseif ($display_argument == display_argument::Borrow): ?>
                    <td>
                        <?php if ($document['available_document'] == 0): ?>
                            <p>Document non disponible</p>
                        <?php else: ?>
                            <a href="borrow_document.php?id_document=<?= $document['id_document'] ?>&search=<?= $search ?>" style="display: inline-block;">
                                <button type="button">Borrow</button>
                            </a>
                        <?php endif; ?>
                    </td>
                <?php elseif ($display_argument == display_argument::Return): ?>
                    <td>
                        <p>Emprunté le : <?= htmlspecialchars($document['date_borrowed']) ?></p>
                        <?php if ($document['return_date_borrowed'] == null): ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="return">
                                <input type="hidden" name="id_borrowed" value="<?= $document['id_borrowed'] ?>">
                                <input type="hidden" name="id_document" value="<?= $document['id_document'] ?>">
                                <button type="submit">Return</button>
                            </form>
                        <?php else: ?>
                            <p>Rendu le : <?= htmlspecialchars($document['return_date_borrowed']) ?></p>
                        <?php endif; ?>
                        <a href="create_dispute.php?id_document=<?= $document['id_document'] ?>&id_user=<?= $user_id ?>" style="display: inline-block;">
                            <button type="button">Créer un contentieux</button>
                        </a>
                    </td>
                <?php elseif ($display_argument == display_argument::NoAction): ?>
                    <td>
                        <p>Emprunté le : <?= htmlspecialchars($document['date_borrowed']) ?></p>
                        <?php if ($document['return_date_borrowed'] == null): ?>
                            Non rendu
                        <?php else: ?>
                            <p>Rendu le : <?= htmlspecialchars($document['return_date_borrowed']) ?></p>
                        <?php endif; ?>
                    </td>
                <?php endif; ?>

            </tr>
        <?php endforeach; ?>
    </tbody>
</table>