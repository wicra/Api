
INSERT INTO type_user (name) VALUES 
('Habitan'),
('Capitaine'),
('Admin');


INSERT INTO ponts (nom, adresse) VALUES 
('Pont A','12 Rue des Ponts, 59140 Dunkerque'),
('Pont B','Avenue du Littoral, 59240 Dunkerque');



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
