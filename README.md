# Bibliothèque — Client Léger (PHP)

Application web de gestion de bibliothèque développée en PHP dans le cadre de l'épreuve E6 du BTS SIO SLAM.  
Réservée aux **membres** : connexion, consultation du catalogue et emprunt de livres.

---

## Prérequis

- WAMP ou XAMPP (Apache + PHP 8.x + MySQL 8.x)
- Base de données `bibliotheque` créée et importée (`bibliotheque.sql`)

---

## Installation

1. Copier le dossier `bibliotheque/` dans `C:/wamp64/www/` (WAMP) ou `C:/xampp/htdocs/` (XAMPP)
2. Ouvrir `config.php` et ajuster les paramètres de connexion :

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'bibliotheque');
define('DB_USER', 'root');
define('DB_PASS', '');
```

3. Démarrer WAMP / XAMPP
4. Ouvrir [http://localhost/bibliotheque/login.php](http://localhost/bibliotheque/login.php)

---

## Structure des fichiers

```
bibliotheque/
├── config.php            ← Connexion PDO + fonctions hashage SHA-256
├── login.php             ← Connexion membre
├── logout.php            ← Déconnexion
├── catalogue.php         ← Liste des livres + bouton Emprunter
├── emprunt.php           ← Confirmation et enregistrement de l'emprunt
├── historique.php        ← Emprunts en cours + historique du membre
└── includes/
    ├── header.php        ← Vérification session + navbar + styles CSS
    └── footer.php        ← Fermeture HTML commune
```

---

## Fonctionnalités

| Page | Description |
|---|---|
| `login.php` | Connexion par email + mot de passe (SHA-256) |
| `catalogue.php` | Liste tous les livres avec badge disponible / emprunté |
| `emprunt.php` | Récapitulatif + confirmation de l'emprunt |
| `historique.php` | Emprunts en cours et historique du membre connecté |
| `logout.php` | Destruction de session et redirection |

---

## Compte de test

| Email | Mot de passe | Rôle |
|---|---|---|
| test@exemple.com | test123 | Membre |

> Les comptes sont créés via le **client lourd Python** (application administrateur).

---

## Sécurité

- Mots de passe hashés en **SHA-256** — compatibles avec le client lourd Python
- Requêtes SQL via **PDO avec paramètres liés** — protection contre les injections SQL
- `htmlspecialchars()` sur toutes les données affichées — protection XSS
- Vérification de session sur chaque page protégée
- `session_regenerate_id(true)` après connexion — protection contre la fixation de session
- Transaction PDO (`beginTransaction` / `commit` / `rollBack`) sur l'emprunt

---

## Technologies

- PHP 8.x
- HTML5 / CSS3
- MySQL 8.x / MariaDB
- Apache (via WAMP / XAMPP)
