<?php
// ============================================================
//  emprunt.php — Confirmation et traitement de l'emprunt
// ============================================================
session_start();
require_once 'config.php';


$pdo     = getConnexion();
$livreId = (int)($_GET['id'] ?? 0);

// Vérification session
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Livre valide ?
if ($livreId <= 0) {
    header('Location: catalogue.php');
    exit;
}

$stmt = $pdo->prepare('SELECT id, titre, auteur, genre, dispo FROM livres WHERE id = ?');
$stmt->execute([$livreId]);
$livre = $stmt->fetch();

if (!$livre) {
    header('Location: catalogue.php');
    exit;
}

// Livre indisponible ?
if (!$livre['dispo']) {
    $_SESSION['message']  = 'Ce livre est actuellement indisponible.';
    $_SESSION['type_msg'] = 'danger';
    header('Location: catalogue.php');
    exit;
}

// Déjà emprunté par ce membre ?
$stmtCheck = $pdo->prepare('SELECT id FROM emprunts WHERE user_id = ? AND livre_id = ? AND date_retour IS NULL');
$stmtCheck->execute([$_SESSION['user_id'], $livreId]);
if ($stmtCheck->fetch()) {
    $_SESSION['message']  = 'Vous avez déjà ce livre en cours d\'emprunt.';
    $_SESSION['type_msg'] = 'danger';
    header('Location: catalogue.php');
    exit;
}

$erreur = '';

// Traitement POST — confirmation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmer'])) {
    try {
        $pdo->beginTransaction();

        $pdo->prepare('INSERT INTO emprunts (user_id, livre_id, date_emprunt) VALUES (?, ?, CURDATE())')
            ->execute([$_SESSION['user_id'], $livreId]);

        $pdo->prepare('UPDATE livres SET dispo = 0 WHERE id = ?')
            ->execute([$livreId]);

        $pdo->commit();

        $_SESSION['message']  = 'Emprunt de « ' . $livre['titre'] . ' » enregistré avec succès.';
        $_SESSION['type_msg'] = 'success';
        header('Location: historique.php');
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        $erreur = 'Une erreur est survenue. Veuillez réessayer.';
    }
}


$titrePage = 'Emprunter un livre — Bibliothèque';
require_once 'includes/header.php';
?>

<h1 class="page-title">Confirmer l'emprunt</h1>

<?php if ($erreur !== ''): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
<?php endif; ?>

<div style="background:var(--blanc);border:1px solid var(--border);border-radius:10px;padding:28px;max-width:520px;margin-bottom:24px">
    <p style="font-size:12px;font-weight:500;color:var(--muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:16px">
        Récapitulatif
    </p>
    <div style="display:grid;grid-template-columns:130px 1fr;gap:10px;font-size:14px">
        <span style="color:var(--muted)">Titre</span>
        <span style="font-weight:500"><?= htmlspecialchars($livre['titre']) ?></span>

        <span style="color:var(--muted)">Auteur</span>
        <span><?= htmlspecialchars($livre['auteur']) ?></span>

        <span style="color:var(--muted)">Genre</span>
        <span><?= htmlspecialchars($livre['genre'] ?? '—') ?></span>

        <span style="color:var(--muted)">Membre</span>
        <span><?= htmlspecialchars($_SESSION['user_nom']) ?></span>

        <span style="color:var(--muted)">Date d'emprunt</span>
        <span><?= date('d/m/Y') ?></span>
    </div>
</div>

<form method="POST" action="emprunt.php?id=<?= $livreId ?>" style="display:flex;gap:10px">
    <button type="submit" name="confirmer" class="btn btn-primary">Confirmer l'emprunt</button>
    <a href="catalogue.php" class="btn btn-outline">Annuler</a>
</form>

<?php require_once 'includes/footer.php'; ?>