


-- Création de la table type_user EN PREMIER
CREATE TABLE type_user (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL
);

-- Insertion des types d'utilisateurs de base
INSERT INTO type_user (name) VALUES 
('Habitan'),
('Capitaine'),
('Admin');


-- Création de la table users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name TEXT NOT NULL,
    email TEXT UNIQUE NOT NULL,
    mdp VARCHAR(64) NOT NULL,
    is_admin BOOLEAN DEFAULT FALSE,
    type_user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_sign TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (type_user_id) REFERENCES type_user(id)
);

