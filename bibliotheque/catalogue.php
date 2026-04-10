<?php
// ============================================================
//  catalogue.php — Liste des livres disponibles
// ============================================================
session_start();
require_once 'config.php';

$titrePage = 'Catalogue — Bibliothèque';
require_once 'includes/header.php';

$pdo    = getConnexion();
$livres = $pdo->query('SELECT id, titre, auteur, genre, isbn, dispo FROM livres ORDER BY titre ASC')->fetchAll();

$message = $_SESSION['message'] ?? '';
$typeMsg = $_SESSION['type_msg'] ?? '';
unset($_SESSION['message'], $_SESSION['type_msg']);
?>

<h1 class="page-title">Catalogue des livres</h1>

<?php if ($message !== ''): ?>
    <div class="alert alert-<?= htmlspecialchars($typeMsg) ?>">
        <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Titre</th>
                <th>Auteur</th>
                <th>Genre</th>
                <th>ISBN</th>
                <th>Disponibilité</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($livres)): ?>
                <tr>
                    <td colspan="6" style="text-align:center;color:var(--muted);padding:32px">
                        Aucun livre dans le catalogue.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($livres as $livre): ?>
                    <tr>
                        <td><?= htmlspecialchars($livre['titre']) ?></td>
                        <td><?= htmlspecialchars($livre['auteur']) ?></td>
                        <td><?= htmlspecialchars($livre['genre'] ?? '—') ?></td>
                        <td style="font-size:12px;color:var(--muted)">
                            <?= htmlspecialchars($livre['isbn'] ?? '—') ?>
                        </td>
                        <td>
                            <?php if ($livre['dispo']): ?>
                                <span class="badge badge-dispo">Disponible</span>
                            <?php else: ?>
                                <span class="badge badge-emprunte">Emprunté</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($livre['dispo']): ?>
                                <a href="emprunt.php?id=<?= (int)$livre['id'] ?>" class="btn btn-primary">Emprunter</a>
                            <?php else: ?>
                                <span class="btn btn-disabled">Indisponible</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once 'includes/footer.php'; ?>
