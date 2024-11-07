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
