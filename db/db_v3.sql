CREATE TABLE `type_user` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `name` VARCHAR(50) NOT NULL
);

CREATE TABLE `users` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(255) UNIQUE NOT NULL,
  `mdp` VARCHAR(64) NOT NULL,
  `type_user_id` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT (now()),
  `last_sign` TIMESTAMP DEFAULT (now())
);

CREATE TABLE `ponts` (
  `pont_id` INT PRIMARY KEY AUTO_INCREMENT,
  `libelle_pont` VARCHAR(100) NOT NULL,
  `adresse` VARCHAR(255) NOT NULL
);

CREATE TABLE `capteurs` (
  `capteur_id` INT PRIMARY KEY AUTO_INCREMENT,
  `pont_id` INT NOT NULL,
  `libelle_capteur` VARCHAR(100) NOT NULL,
  `valeur_capteur` float(4) NOT NULL
);

CREATE TABLE `creneaux` (
  `creneau_id` INT PRIMARY KEY AUTO_INCREMENT,
  `type_creneau` ENUM(entrée,sortie) NOT NULL,
  `periode` ENUM(hiver,été) NOT NULL,
  `libelle` VARCHAR(50),
  `heure_debut` TIME NOT NULL,
  `heure_fin` TIME NOT NULL,
  `capacite_max` INT NOT NULL
);

CREATE TABLE `bateaux` (
  `bateau_id` INT PRIMARY KEY AUTO_INCREMENT,
  `libelle_bateau` VARCHAR(100) NOT NULL,
  `immatriculation` VARCHAR(50) NOT NULL,
  `hauteur_mat` FLOAT NOT NULL,
  `user_id` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT (now())
);

CREATE TABLE `status` (
  `status_id` INT PRIMARY KEY AUTO_INCREMENT,
  `reservation_id` INT NOT NULL,
  `libelle_status` ENUM(confirmé,annulé,en attente,maintenance) DEFAULT 'en attente'
);

CREATE TABLE `reservations` (
  `reservation_id` INT PRIMARY KEY AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `pont_id` INT NOT NULL,
  `creneau_id` INT NOT NULL,
  `bateau_id` INT NOT NULL,
  `date_reservation` TIMESTAMP DEFAULT (now())
);

ALTER TABLE `users` ADD FOREIGN KEY (`type_user_id`) REFERENCES `type_user` (`id`);

ALTER TABLE `capteurs` ADD FOREIGN KEY (`pont_id`) REFERENCES `ponts` (`pont_id`);

ALTER TABLE `bateaux` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `status` ADD FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`reservation_id`);

ALTER TABLE `reservations` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `reservations` ADD FOREIGN KEY (`pont_id`) REFERENCES `ponts` (`pont_id`);

ALTER TABLE `reservations` ADD FOREIGN KEY (`creneau_id`) REFERENCES `creneaux` (`creneau_id`);

ALTER TABLE `reservations` ADD FOREIGN KEY (`bateau_id`) REFERENCES `bateaux` (`bateau_id`);
