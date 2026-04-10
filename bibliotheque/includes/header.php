<?php
// ============================================================
//  includes/header.php — En-tête commun + vérification session
//  Inclure en début de chaque page protégée APRÈS session_start()
// ============================================================
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titrePage ?? 'Bibliothèque') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --bleu:       #1a2d5a;
            --texte:      #1e293b;
            --muted:      #64748b;
            --border:     #e2e8f0;
            --bg:         #f8fafc;
            --blanc:      #ffffff;
            --success-bg: #f0fdf4;
            --success:    #15803d;
            --danger-bg:  #fef2f2;
            --danger:     #dc2626;
            --warning-bg: #fffbeb;
            --warning:    #b45309;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'DM Sans', sans-serif; background: var(--bg); color: var(--texte); min-height: 100vh; }

        nav {
            background: var(--bleu); height: 56px; padding: 0 32px;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 10;
        }
        .nav-brand { font-family: 'DM Serif Display', serif; font-size: 18px; color: #fff; text-decoration: none; }
        .nav-links { display: flex; gap: 4px; }
        .nav-links a {
            color: #b5d4f4; text-decoration: none; font-size: 14px;
            padding: 6px 14px; border-radius: 6px; transition: background .15s, color .15s;
        }
        .nav-links a:hover,
        .nav-links a.active { background: rgba(255,255,255,.12); color: #fff; }
        .nav-right { display: flex; align-items: center; gap: 16px; }
        .nav-user  { font-size: 13px; color: #b5d4f4; }
        .nav-logout {
            font-size: 13px; color: #b5d4f4; text-decoration: none;
            padding: 5px 12px; border: 1px solid rgba(255,255,255,.2);
            border-radius: 6px; transition: background .15s;
        }
        .nav-logout:hover { background: rgba(255,255,255,.1); color: #fff; }

        .container  { max-width: 1100px; margin: 32px auto; padding: 0 24px; }
        .page-title { font-family: 'DM Serif Display', serif; font-size: 22px; color: var(--bleu); margin-bottom: 20px; }

        .table-wrap { background: var(--blanc); border: 1px solid var(--border); border-radius: 10px; overflow: hidden; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        thead th {
            background: var(--bg); padding: 10px 14px; text-align: left;
            font-size: 12px; font-weight: 500; color: var(--muted);
            border-bottom: 1px solid var(--border);
            text-transform: uppercase; letter-spacing: .04em;
        }
        tbody td { padding: 12px 14px; border-bottom: 1px solid var(--border); }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover td { background: var(--bg); }

        .badge          { display: inline-block; padding: 2px 10px; border-radius: 20px; font-size: 12px; font-weight: 500; }
        .badge-dispo    { background: #dcfce7; color: #15803d; }
        .badge-emprunte { background: #fee2e2; color: #b91c1c; }
        .badge-encours  { background: #fef3c7; color: #b45309; }
        .badge-rendu    { background: #dcfce7; color: #15803d; }

        .btn          { display: inline-block; padding: 7px 16px; border-radius: 7px; font-family: 'DM Sans', sans-serif; font-size: 13px; font-weight: 500; cursor: pointer; text-decoration: none; border: none; transition: opacity .15s; }
        .btn-primary  { background: var(--bleu); color: #fff; }
        .btn-primary:hover { opacity: .88; }
        .btn-outline  { background: transparent; color: var(--texte); border: 1px solid var(--border); }
        .btn-outline:hover { background: var(--bg); }
        .btn-disabled { background: var(--bg); color: var(--muted); border: 1px solid var(--border); cursor: not-allowed; pointer-events: none; }

        .alert         { padding: 12px 16px; border-radius: 8px; font-size: 14px; margin-bottom: 20px; }
        .alert-success { background: var(--success-bg); color: var(--success); border: 1px solid #bbf7d0; }
        .alert-danger  { background: var(--danger-bg);  color: var(--danger);  border: 1px solid #fecaca; }
    </style>
</head>
<body>
<nav>
    <a class="nav-brand" href="catalogue.php">Bibliothèque</a>
    <div class="nav-links">
        <a href="catalogue.php"  class="<?= basename($_SERVER['PHP_SELF']) === 'catalogue.php'  ? 'active' : '' ?>">Catalogue</a>
        <a href="historique.php" class="<?= basename($_SERVER['PHP_SELF']) === 'historique.php' ? 'active' : '' ?>">Mes emprunts</a>
    </div>
    <div class="nav-right">
        <span class="nav-user"><?= htmlspecialchars($_SESSION['user_nom']) ?></span>
        <a href="logout.php" class="nav-logout">Déconnexion</a>
    </div>
</nav>
<div class="container">
