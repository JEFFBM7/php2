-- Ajouter la colonne avatar à la table etudiant
ALTER TABLE etudiant ADD COLUMN avatar VARCHAR(255) DEFAULT 'default.png' AFTER password;
