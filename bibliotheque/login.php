<?php
// ============================================================
//  login.php — Connexion membre / admin
// ============================================================
session_start();
require_once 'config.php';

if (isset($_SESSION['user_id'])) {
    header('Location: catalogue.php');
    exit;
}

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $mdp   = $_POST['mot_de_passe'] ?? '';

    if ($email === '' || $mdp === '') {
        $erreur = 'Veuillez remplir tous les champs.';
    } else {
        $pdo  = getConnexion();
        $stmt = $pdo->prepare('SELECT id, nom, mot_de_passe, role FROM utilisateurs WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && verifierMotDePasse($mdp, $user['mot_de_passe'])) {
            session_regenerate_id(true);
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_nom']  = $user['nom'];
            $_SESSION['user_role'] = $user['role'];
            header('Location: catalogue.php');
            exit;
        } else {
            $erreur = 'Email ou mot de passe incorrect.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — Bibliothèque</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root { --bleu: #1a2d5a; --texte: #1e293b; --muted: #64748b; --border: #e2e8f0; --bg: #f8fafc; --blanc: #ffffff; --danger: #dc2626; --danger-bg: #fef2f2; }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'DM Sans', sans-serif; background: var(--bg); color: var(--texte); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card { background: var(--blanc); border: 1px solid var(--border); border-radius: 12px; padding: 40px 36px; width: 100%; max-width: 400px; }
        .logo { font-family: 'DM Serif Display', serif; font-size: 22px; color: var(--bleu); margin-bottom: 6px; }
        .subtitle { font-size: 13px; color: var(--muted); margin-bottom: 28px; }
        label { display: block; font-size: 13px; font-weight: 500; margin-bottom: 5px; }
        input[type="email"], input[type="password"] { width: 100%; padding: 9px 12px; border: 1px solid var(--border); border-radius: 8px; font-family: 'DM Sans', sans-serif; font-size: 14px; background: var(--blanc); color: var(--texte); margin-bottom: 16px; transition: border-color .15s; }
        input:focus { outline: none; border-color: var(--bleu); }
        .btn { width: 100%; padding: 10px; background: var(--bleu); color: var(--blanc); border: none; border-radius: 8px; font-family: 'DM Sans', sans-serif; font-size: 14px; font-weight: 500; cursor: pointer; transition: opacity .15s; }
        .btn:hover { opacity: .88; }
        .alert { background: var(--danger-bg); color: var(--danger); border: 1px solid #fecaca; border-radius: 8px; padding: 10px 14px; font-size: 13px; margin-bottom: 18px; }
    </style>
</head>
<body>
    <div class="card">
        <p class="logo">Bibliothèque</p>
        <p class="subtitle">Connectez-vous à votre espace membre</p>

        <?php if ($erreur !== ''): ?>
            <div class="alert"><?= htmlspecialchars($erreur) ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <label for="email">Adresse email</label>
            <input type="email" id="email" name="email"
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                   placeholder="nom@exemple.fr" required autofocus>

            <label for="mot_de_passe">Mot de passe</label>
            <input type="password" id="mot_de_passe" name="mot_de_passe"
                   placeholder="••••••••" required>

            <button type="submit" class="btn">Se connecter</button>
        </form>
    </div>
</body>
</html>
