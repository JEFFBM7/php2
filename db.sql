-- 1. Création et sélection de la base de données
CREATE DATABASE IF NOT EXISTS gestion_etudiants;
USE gestion_etudiants;

-- 2. Table parente : etudiant
DROP TABLE IF EXISTS etudiant;
CREATE TABLE IF NOT EXISTS etudiant (
    id INT NOT NULL AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    promotion VARCHAR(50) NOT NULL,
    telephone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- 3. Table enfant : produit
DROP TABLE IF EXISTS produit;
CREATE TABLE IF NOT EXISTS produit (
    id INT NOT NULL AUTO_INCREMENT,
    etudiant_id INT NOT NULL,
    nom VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    image VARCHAR(255) DEFAULT NULL,
    prix DECIMAL(10,2) NOT NULL,
    devis VARCHAR(3) NOT NULL DEFAULT 'USD',
    PRIMARY KEY (id),
    INDEX idx_etudiant (etudiant_id),
    CONSTRAINT fk_etudiant_prod FOREIGN KEY (etudiant_id)
        REFERENCES etudiant (id)
        ON DELETE CASCADE
        ON UPDATE RESTRICT
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- 4. Table de liaison : etudiant_produit (many-to-many)
CREATE TABLE IF NOT EXISTS etudiant_produit (
    etudiant_id INT NOT NULL,
    produit_id   INT NOT NULL,
    PRIMARY KEY (etudiant_id, produit_id),
    CONSTRAINT fk_ep_etudiant
        FOREIGN KEY (etudiant_id)
        REFERENCES etudiant (id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_ep_produit
        FOREIGN KEY (produit_id)
        REFERENCES produit (id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- 5. Table enfant : note
DROP TABLE IF EXISTS note;
CREATE TABLE IF NOT EXISTS note (
    id INT NOT NULL AUTO_INCREMENT,
    etudiant_id INT NOT NULL,
    matiere VARCHAR(100) NOT NULL,
    note DECIMAL(5,2) NOT NULL,
    PRIMARY KEY (id),
    INDEX idx_etudiant_note (etudiant_id),
    CONSTRAINT fk_etudiant_note FOREIGN KEY (etudiant_id)
        REFERENCES etudiant (id)
        ON DELETE CASCADE
        ON UPDATE RESTRICT
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;
