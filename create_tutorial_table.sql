-- Création de la table pour les étapes du tutoriel
CREATE TABLE IF NOT EXISTS tutorial_steps (
    id INT AUTO_INCREMENT PRIMARY KEY,
    step_order INT NOT NULL,
    element_id VARCHAR(50) NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    position VARCHAR(20) NOT NULL DEFAULT 'bottom',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
