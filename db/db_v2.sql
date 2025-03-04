-- Table des types d'utilisateurs
CREATE TABLE type_user (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL
);
INSERT INTO type_user (name) VALUES 
('Habitan'),
('Capitaine'),
('Admin');

-- Table des utilisateurs
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    mdp VARCHAR(64) NOT NULL, -- SHA-256 hash du mot de passe
    type_user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_sign TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (type_user_id) REFERENCES type_user(id)
);

-- Table des ponts
CREATE TABLE ponts (
    pont_id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    adresse VARCHAR(255) NOT NULL
);
INSERT INTO ponts (nom, adresse) VALUES 
('Pont A','12 Rue des Ponts, 59140 Dunkerque'),
('Pont B','Avenue du Littoral, 59240 Dunkerque');

-- Table des créneaux (définit le type de passage, la période et la capacité)
CREATE TABLE creneaux (
    creneau_id INT PRIMARY KEY AUTO_INCREMENT,
    pont_id INT NOT NULL,
    type_creneau ENUM('entrée', 'sortie') NOT NULL,
    periode ENUM('hiver', 'été') NOT NULL,
    libelle VARCHAR(50) NULL,  -- facultatif : Ex. 'Matin', 'Après-midi', etc.
    heure_debut TIME NOT NULL,
    heure_fin TIME NOT NULL,
    capacite_max INT NOT NULL,
    FOREIGN KEY (pont_id) REFERENCES ponts(pont_id) ON DELETE CASCADE
);

-- Exemples de créneaux

-- Pour Pont A en hiver
INSERT INTO creneaux (pont_id, type_creneau, periode, libelle, heure_debut, heure_fin, capacite_max)
VALUES 
(1, 'sortie', 'hiver', 'Matin', '09:30:00', '09:50:00', 5),
(1, 'sortie', 'hiver', 'Après-midi', '14:30:00', '14:50:00', 5),
(1, 'entrée', 'hiver', 'Matin', '10:20:00', '11:10:00', 5),
(1, 'entrée', 'hiver', 'Après-midi', '15:30:00', '16:30:00', 5);

-- Pour Pont B en été
INSERT INTO creneaux (pont_id, type_creneau, periode, libelle, heure_debut, heure_fin, capacite_max)
VALUES 
(2, 'sortie', 'été', 'Matin', '09:00:00', '09:20:00', 5),
(2, 'sortie', 'été', 'Fin de matinée', '11:00:00', '11:20:00', 5),
(2, 'sortie', 'été', 'Soirée', '18:30:00', '18:50:00', 5),
(2, 'entrée', 'été', 'Matin', '10:20:00', '11:10:00', 5),
(2, 'entrée', 'été', 'Après-midi', '15:30:00', '16:30:00', 5),
(2, 'entrée', 'été', 'Soirée', '21:30:00', '21:50:00', 5);

-- Table des réservations (mise à jour pour lier à un créneau)
CREATE TABLE reservations (
    reservation_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    pont_id INT NOT NULL,
    creneau_id INT NOT NULL,
    bateau_id INT NOT NULL,
    statut ENUM('confirmé', 'annulé', 'en attente', 'maintenance') DEFAULT 'en attente',
    date_reservation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (pont_id) REFERENCES ponts(pont_id) ON DELETE CASCADE,
    FOREIGN KEY (creneau_id) REFERENCES creneaux(creneau_id) ON DELETE CASCADE,
    FOREIGN KEY (bateau_id) REFERENCES bateaux(bateau_id) ON DELETE CASCADE
);


CREATE TABLE bateaux (
    bateau_id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    immatriculation VARCHAR(50) NOT NULL,
    hauteur_mat FLOAT NOT NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);