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
    id INT AUTO_INCREMENT PRIMARY KEY,  -- id auto-incrémenté
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    mdp VARCHAR(64) NOT NULL, -- SHA-256 hash du mot de passe
    type_user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_sign TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (type_user_id) REFERENCES type_user(id)
);


CREATE TABLE ponts (
    pont_id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL, 
    adresse VARCHAR(255) NOT NULL, -- Adresse sous forme de texte
);

INSERT INTO ponts (nom, adresse) VALUES 
('Pont A', '12 Rue des Ponts, 59140 Dunkerque'),
('Pont B', 'Avenue du Littoral, 59240 Dunkerque');


CREATE TABLE reservations (
    reservation_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL, -- L’utilisateur qui réserve
    pont_id INT NOT NULL, -- Le pont concerné
    date_debut DATETIME NOT NULL, 
    date_fin DATETIME NOT NULL, 
    statut ENUM('confirmé', 'annulé', 'en attente','maintenance') DEFAULT 'en attente',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (pont_id) REFERENCES ponts(pont_id) ON DELETE CASCADE
);

CREATE TABLE capteurs (
    capteur_id INT PRIMARY KEY AUTO_INCREMENT,
    pont_id INT NOT NULL, 
    niveau_eau FLOAT,
    temperature FLOAT,
    humidite FLOAT,
    date_heure TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pont_id) REFERENCES ponts(pont_id) ON DELETE CASCADE
);
