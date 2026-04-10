<?php
// ============================================================
//  config.php — Connexion PDO centralisée
// ============================================================

define('DB_HOST',    'localhost');
define('DB_NAME',    'bibliotheque');
define('DB_USER',    'root');
define('DB_PASS',    '');
define('DB_CHARSET', 'utf8mb4');

/**
 * Hashe un mot de passe en SHA-256.
 * Même algorithme que le client lourd Python (hashlib.sha256).
 */
function hasherMotDePasse(string $mdp): string {
    return hash('sha256', $mdp);
}

/**
 * Vérifie un mot de passe contre son hash SHA-256.
 */
function verifierMotDePasse(string $mdp, string $hashStocke): bool {
    return hash_equals($hashStocke, hash('sha256', $mdp));
}

function getConnexion(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST
             . ';dbname=' . DB_NAME
             . ';charset=' . DB_CHARSET;
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            die('Erreur de connexion à la base de données.');
        }
    }
    return $pdo;
}
