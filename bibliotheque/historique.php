<?php
// ============================================================
//  historique.php — Mes emprunts (en cours + historique)
// ============================================================
session_start();
require_once 'config.php';

$titrePage = 'Mes emprunts — Bibliothèque';
require_once 'includes/header.php';

$pdo    = getConnexion();
$userId = (int)$_SESSION['user_id'];

$message = $_SESSION['message'] ?? '';
$typeMsg = $_SESSION['type_msg'] ?? '';
unset($_SESSION['message'], $_SESSION['type_msg']);

// Emprunts en cours
$stmtEncours = $pdo->prepare(
    'SELECT l.titre, l.auteur, e.date_emprunt,
            DATEDIFF(CURDATE(), e.date_emprunt) AS jours
     FROM emprunts e
     JOIN livres l ON l.id = e.livre_id
     WHERE e.user_id = ? AND e.date_retour IS NULL
     ORDER BY e.date_emprunt DESC'
);
$stmtEncours->execute([$userId]);
$encours = $stmtEncours->fetchAll();

// Historique rendus
$stmtHistorique = $pdo->prepare(
    'SELECT l.titre, l.auteur, e.date_emprunt, e.date_retour
     FROM emprunts e
     JOIN livres l ON l.id = e.livre_id
     WHERE e.user_id = ? AND e.date_retour IS NOT NULL
     ORDER BY e.date_retour DESC'
);
$stmtHistorique->execute([$userId]);
$historique = $stmtHistorique->fetchAll();
?>

<h1 class="page-title">Mes emprunts</h1>

<?php if ($message !== ''): ?>
    <div class="alert alert-<?= htmlspecialchars($typeMsg) ?>">
        <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>

<h2 style="font-size:15px;font-weight:500;margin-bottom:12px">
    En cours
    <?php if (!empty($encours)): ?>
        <span class="badge badge-encours" style="font-size:11px;vertical-align:middle"><?= count($encours) ?></span>
    <?php endif; ?>
</h2>

<div class="table-wrap" style="margin-bottom:32px">
    <table>
        <thead>
            <tr>
                <th>Titre</th>
                <th>Auteur</th>
                <th>Date d'emprunt</th>
                <th>Jours écoulés</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($encours)): ?>
                <tr><td colspan="4" style="text-align:center;color:var(--muted);padding:28px">Aucun emprunt en cours.</td></tr>
            <?php else: ?>
                <?php foreach ($encours as $e): ?>
                    <tr>
                        <td style="font-weight:500"><?= htmlspecialchars($e['titre']) ?></td>
                        <td><?= htmlspecialchars($e['auteur']) ?></td>
                        <td><?= date('d/m/Y', strtotime($e['date_emprunt'])) ?></td>
                        <td><span class="badge badge-encours"><?= (int)$e['jours'] ?> jour<?= $e['jours'] > 1 ? 's' : '' ?></span></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<h2 style="font-size:15px;font-weight:500;margin-bottom:12px">Historique</h2>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Titre</th>
                <th>Auteur</th>
                <th>Date d'emprunt</th>
                <th>Date de retour</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($historique)): ?>
                <tr><td colspan="5" style="text-align:center;color:var(--muted);padding:28px">Aucun emprunt terminé.</td></tr>
            <?php else: ?>
                <?php foreach ($historique as $h): ?>
                    <tr>
                        <td style="font-weight:500"><?= htmlspecialchars($h['titre']) ?></td>
                        <td><?= htmlspecialchars($h['auteur']) ?></td>
                        <td><?= date('d/m/Y', strtotime($h['date_emprunt'])) ?></td>
                        <td><?= date('d/m/Y', strtotime($h['date_retour'])) ?></td>
                        <td><span class="badge badge-rendu">Rendu</span></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once 'includes/footer.php'; ?>
