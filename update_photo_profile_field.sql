-- Ajouter la colonne photo_profile à la table etudiant si elle n'existe pas déjà
ALTER TABLE etudiant ADD COLUMN photo_profile VARCHAR(255) DEFAULT NULL;
