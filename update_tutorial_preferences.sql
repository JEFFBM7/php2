-- Ajouter la colonne tutorial_enabled à la table etudiant
ALTER TABLE etudiant
ADD COLUMN tutorial_enabled BOOLEAN DEFAULT TRUE AFTER last_login;

-- Créer une table user_preferences si besoin
CREATE TABLE IF NOT EXISTS user_preferences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    tutorial_enabled BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_id (user_id),
    FOREIGN KEY (user_id) REFERENCES etudiant(id) ON DELETE CASCADE
);
