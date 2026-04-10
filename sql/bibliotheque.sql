-- ============================================================
--  GESTION DE BIBLIOTHÈQUE — BTS SIO SLAM — Projet E6
--  Base de données : bibliotheque
--  Compatible MySQL 5.7+ / MariaDB 10+
-- ============================================================

CREATE DATABASE IF NOT EXISTS bibliotheque
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE bibliotheque;

-- ============================================================
--  TABLE : utilisateurs
-- ============================================================
CREATE TABLE IF NOT EXISTS utilisateurs (
    id           INT          NOT NULL AUTO_INCREMENT,
    nom          VARCHAR(100) NOT NULL,
    email        VARCHAR(150) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    role         ENUM('membre','admin') NOT NULL DEFAULT 'membre',
    created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB;

-- ============================================================
--  TABLE : livres
-- ============================================================
CREATE TABLE IF NOT EXISTS livres (
    id         INT          NOT NULL AUTO_INCREMENT,
    titre      VARCHAR(200) NOT NULL,
    auteur     VARCHAR(150) NOT NULL,
    isbn       VARCHAR(20)           DEFAULT NULL,
    genre      VARCHAR(80)           DEFAULT NULL,
    dispo      TINYINT(1)   NOT NULL DEFAULT 1,
    created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB;

-- ============================================================
--  TABLE : emprunts
-- ============================================================
CREATE TABLE IF NOT EXISTS emprunts (
    id           INT  NOT NULL AUTO_INCREMENT,
    user_id      INT  NOT NULL,
    livre_id     INT  NOT NULL,
    date_emprunt DATE NOT NULL DEFAULT (CURRENT_DATE),
    date_retour  DATE          DEFAULT NULL,
    PRIMARY KEY (id),
    CONSTRAINT fk_emprunt_user
        FOREIGN KEY (user_id)  REFERENCES utilisateurs(id) ON DELETE CASCADE,
    CONSTRAINT fk_emprunt_livre
        FOREIGN KEY (livre_id) REFERENCES livres(id)       ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
--  INDEX
-- ============================================================
CREATE INDEX idx_livres_dispo     ON livres   (dispo);
CREATE INDEX idx_emprunts_user    ON emprunts (user_id);
CREATE INDEX idx_emprunts_livre   ON emprunts (livre_id);
CREATE INDEX idx_emprunts_retour  ON emprunts (date_retour);

-- ============================================================
--  VUES UTILES
-- ============================================================
CREATE OR REPLACE VIEW v_emprunts_en_cours AS
SELECT
    e.id AS emprunt_id,
    u.nom AS membre,
    u.email,
    l.titre,
    l.auteur,
    e.date_emprunt,
    DATEDIFF(CURRENT_DATE, e.date_emprunt) AS jours_depuis_emprunt
FROM emprunts e
JOIN utilisateurs u ON u.id = e.user_id
JOIN livres      l ON l.id = e.livre_id
WHERE e.date_retour IS NULL;

CREATE OR REPLACE VIEW v_historique_emprunts AS
SELECT
    e.id AS emprunt_id,
    u.nom AS membre,
    l.titre,
    l.auteur,
    e.date_emprunt,
    e.date_retour,
    CASE WHEN e.date_retour IS NULL THEN 'En cours' ELSE 'Rendu' END AS statut
FROM emprunts e
JOIN utilisateurs u ON u.id = e.user_id
JOIN livres      l ON l.id = e.livre_id
ORDER BY e.date_emprunt DESC;

-- ============================================================
--  VÉRIFICATION
-- ============================================================
SELECT 'utilisateurs' AS table_name, COUNT(*) AS nb_lignes FROM utilisateurs
UNION ALL SELECT 'livres',   COUNT(*) FROM livres
UNION ALL SELECT 'emprunts', COUNT(*) FROM emprunts;
