SET NAMES utf8; 
SET time_zone = '+00:00'; 
SET foreign_key_checks = 0; 
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';





DROP TABLE IF EXISTS `lieu`;
CREATE TABLE `lieu` (
	`id_lieu` INT NOT NULL AUTO_INCREMENT,
	`nom_lieu` VARCHAR(100) NOT NULL,
	`adresse` VARCHAR(100) NOT NULL,
	`nb_place` INT,
	PRIMARY KEY (`id_lieu`)
);

DROP TABLE IF EXISTS `artiste`;
CREATE TABLE `artiste` (
	`id_artiste` INT NOT NULL AUTO_INCREMENT,
	`nom_artiste` VARCHAR(50) NOT NULL,
	PRIMARY KEY(`id_artiste`)
);

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
	`id_user` INT NOT NULL AUTO_INCREMENT,
	`nom_user` VARCHAR(200),
	`email` VARCHAR(256) NOT NULL,
	`password` VARCHAR(256) NOT NULL,
	`role` INT NOT NULL,
	PRIMARY KEY(`id_user`)
);

DROP TABLE IF EXISTS `soiree`;
CREATE TABLE `soiree` (
	`id_soiree` INT NOT NULL AUTO_INCREMENT,
	`nom_soiree` VARCHAR(100) NOT NULL,
	`id_lieu` INT NOT NULL,
	`date` DATE NOT NULL,
	PRIMARY KEY(`id_soiree`),
	FOREIGN KEY (`id_lieu`) REFERENCES `lieu`(`id_lieu`)
);

DROP TABLE IF EXISTS `spectacle`;
CREATE TABLE `spectacle` (
	`id_spectacle` INT NOT NULL AUTO_INCREMENT,
	`nomSpec` VARCHAR(100) NOT NULL,
	`style` VARCHAR(50) NOT NULL,
	`id_soiree` INT NOT NULL,
	`duree` INT NOT NULL,
	PRIMARY KEY(`id_spectacle`),
	FOREIGN KEY (`id_soiree`) REFERENCES `soiree`(`id_soiree`)
);


DROP TABLE IF EXISTS `spectacle2artiste`;
CREATE TABLE `spectacle2artiste` (
	`id_spectacle` INT NOT NULL,
	`id_artiste` INT NOT NULL,
	PRIMARY KEY (`id_spectacle`, `id_artiste`),
	FOREIGN KEY (`id_spectacle`) REFERENCES `spectacle`(`id_spectacle`),
	FOREIGN KEY (`id_artiste`) REFERENCES `artiste`(`id_artiste`)
);


DROP TABLE IF EXISTS `soiree2spectacle`;
CREATE TABLE `soiree2spectacle` (
	`id_spectacle` INT NOT NULL,
	`id_soiree` INT NOT NULL,
	PRIMARY KEY(`id_spectacle`, `id_soiree`),
	FOREIGN KEY (`id_soiree`) REFERENCES   `soiree`(`id_soiree`),
	FOREIGN KEY (`id_spectacle`) REFERENCES `spectacle`(`id_spectacle`)
);


INSERT INTO lieu (id_lieu ,nom_lieu, adresse, nb_place) VALUES
                                                       (1,'Zénith de Nancy', 'Rue du Zénith, Maxéville', 5000),
                                                       (2,'L\'Autre  Gérome Canals', '45 Bd d\'Austrasie, Nancy', 1200),
                                                       (3,'Théâtre de la Manufacture', '10 Rue Baron Louis, Nancy', 850);


INSERT INTO `user` (nom_user, email, password, role) VALUES
                                                         ('Festival Staff', 'staff@nrvfest.com', 'password123', 1),
                                                         ('Festival Admin', 'admin@nrvfest.com', 'adminpassword', 100);


INSERT INTO artiste (id_artiste , nom_artiste) VALUES
                                      (1,'The Rocking Tigers'),
                                      (2,'DestAcid'),
                                      (3,'Techno Masters'),
                                      (4,'Acid Crew'),
                                      (5,'Electric Thunder'),
                                      (6,'Metal Storm'),
                                      (7,'Tristan Tornow'),
                                      (8,'The Vibes'),
                                      (9,'Neo Noir'),
                                      (10,'Urban Funk'),
                                      (11,'Hypno Sound'),
                                      (12,'Strobe Vision'),
                                      (13,'Night Ravers'),
                                      (14,'Sonic Drive');




INSERT INTO spectacle (id_spectacle, nomSpec, style, duree , id_soiree) VALUES
                                                                            (1, 'Rock Vibes', 'Rock', 90, 1),
                                                                            (2, 'Hardcore Thunder', 'Hard Rock', 105, 1),
                                                                            (3, 'Techno Pulse', 'Techno', 100, 2),
                                                                            (4, 'Acid Flow', 'Acid', 95, 2),
                                                                            (5, 'Electric Night', 'Electronic', 85, 3),
                                                                            (6, 'Metal Scream', 'Metal', 110, 3),
                                                                            (7, 'Blue Fusion', 'Blues', 90, 4),
                                                                            (8, 'Synthwave Dreams', 'Synthwave', 80, 4),
                                                                            (9, 'Groove Jam', 'Funk', 75, 5),
                                                                            (10, 'Dark Soundscapes', 'Darkwave', 90, 5),
                                                                            (11, 'Hypnotic Journey', 'Trance', 95, 6),
                                                                            (12, 'Neon Nights', 'Electro', 85, 6),
                                                                            (13, 'Night Bassline', 'House', 90, 7),
                                                                            (14, 'Pulse Driver', 'Drum and Bass', 105, 7);


INSERT INTO `soiree` (id_soiree , nom_soiree,id_lieu, date) VALUES
                                    (1, 'Rock Fest', 1, '2024-06-01'),
                                      (2, 'Electro Vibes', 2, '2024-06-02'),
                                      (3, 'Metal Mania', 3, '2024-06-03'),
                                      (4, 'Jazz Night', 1, '2024-06-04'),
                                      (5, 'Indie Sounds', 2, '2024-06-05'),
                                      (6, 'Pop Parade', 3, '2024-06-06'),
                                      (7, 'Acoustic Evening', 1, '2024-06-07'),
                                      (8, 'Reggae Groove', 2, '2024-06-08'),
                                      (9, 'Classical Harmonies', 3, '2024-06-09'),
                                      (10, 'Folk Fest', 1, '2024-06-10'),
                                      (11, 'Hip Hop Beats', 2, '2024-06-11'),
                                      (12, 'Dancefloor Fever', 3, '2024-06-12');




INSERT INTO spectacle (id_spectacle, nomSpec, style, duree , id_soiree) VALUES
                                                                            (1, 'Rock Vibes', 'Rock', 90, 1),
                                                                            (2, 'Hardcore Thunder', 'Hard Rock', 105, 1),
                                                                            (3, 'Techno Pulse', 'Techno', 100, 2),
                                                                            (4, 'Acid Flow', 'Acid', 95, 2),
                                                                            (5, 'Electric Night', 'Electronic', 85, 3),
                                                                            (6, 'Metal Scream', 'Metal', 110, 3),
                                                                            (7, 'Blue Fusion', 'Blues', 90, 4),
                                                                            (8, 'Synthwave Dreams', 'Synthwave', 80, 4),
                                                                            (9, 'Groove Jam', 'Funk', 75, 5),
                                                                            (10, 'Dark Soundscapes', 'Darkwave', 90, 5),
                                                                            (11, 'Hypnotic Journey', 'Trance', 95, 6),
                                                                            (12, 'Neon Nights', 'Electro', 85, 6),
                                                                            (13, 'Night Bassline', 'House', 90, 7),
                                                                            (14, 'Pulse Driver', 'Drum and Bass', 105, 7);



INSERT INTO `soiree2spectacle` (id_spectacle, id_soiree) VALUES
                                                             (1, 1), (2, 1),
                                                             (3, 2), (4, 2),
                                                             (5, 3), (6, 3),
                                                             (7, 4), (8, 4),
                                                             (9, 5), (10, 5),
                                                             (11, 6), (12, 6),
                                                             (13, 7), (14, 7),
                                                             (1, 8), (3, 8),
                                                             (4, 9), (5, 9),
                                                             (6, 10), (7, 10),
                                                             (8, 11), (9, 11),
                                                             (10, 12), (11, 12);
INSERT INTO `spectacle2artiste` (id_spectacle, id_artiste) VALUES
                                                               (1, 1),
                                                               (1, 2),
                                                               (2, 3),
                                                               (2, 4),
                                                               (3, 5),
                                                               (3, 6),
                                                               (4, 7),
                                                               (4, 8),
                                                               (5, 9),
                                                               (5, 10),
                                                               (6, 11),
                                                               (6, 12),
                                                               (7, 13),
                                                               (7, 14),
                                                               (8, 1),
                                                               (8, 3),
                                                               (9, 4),
                                                               (9, 5),
                                                               (10, 6),
                                                               (10, 7),
                                                               (11, 8),
                                                               (11, 9),
                                                               (12, 10),
                                                               (12, 11),
                                                               (13, 12),
                                                               (13, 13),
                                                               (14, 14);
