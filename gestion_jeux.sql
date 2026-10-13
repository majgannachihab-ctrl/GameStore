-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 13, 2026 at 01:39 PM
-- Server version: 8.4.7
-- PHP Version: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gestion_jeux`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `nom`) VALUES
(1, 'Action'),
(2, 'Aventure'),
(3, 'RPG'),
(4, 'Sport'),
(5, 'Course');

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

DROP TABLE IF EXISTS `clients`;
CREATE TABLE IF NOT EXISTS `clients` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mot_de_passe` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `nom`, `email`, `mot_de_passe`) VALUES
(2, 'SHINDO', 'SHINDO@gmail.com', '$2y$10$5D8C4aGIjuA6GqOV85ONiOeWQWHjk46LWWP1e/1XRvpkVTb6roxeW');

-- --------------------------------------------------------

--
-- Table structure for table `commandes`
--

DROP TABLE IF EXISTS `commandes`;
CREATE TABLE IF NOT EXISTS `commandes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `client_id` int NOT NULL,
  `date_commande` datetime DEFAULT CURRENT_TIMESTAMP,
  `total` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_commande_client` (`client_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `commandes`
--

INSERT INTO `commandes` (`id`, `client_id`, `date_commande`, `total`) VALUES
(1, 2, '2026-07-12 21:40:10', 39.99),
(2, 2, '2026-07-13 12:44:30', 66.99),
(3, 2, '2026-07-13 15:24:23', 78.98),
(4, 2, '2026-07-13 16:02:57', 78.98);

-- --------------------------------------------------------

--
-- Table structure for table `commande_lignes`
--

DROP TABLE IF EXISTS `commande_lignes`;
CREATE TABLE IF NOT EXISTS `commande_lignes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `commande_id` int NOT NULL,
  `jeu_id` int DEFAULT NULL,
  `quantite` int NOT NULL DEFAULT '1',
  `prix_unitaire` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_ligne_commande` (`commande_id`),
  KEY `fk_ligne_jeu` (`jeu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `commande_lignes`
--

INSERT INTO `commande_lignes` (`id`, `commande_id`, `jeu_id`, `quantite`, `prix_unitaire`) VALUES
(1, 1, 2, 1, 39.99),
(2, 2, 7, 1, 66.99),
(3, 3, 8, 1, 55.99),
(4, 3, 6, 1, 22.99),
(5, 4, 8, 1, 55.99),
(6, 4, 6, 1, 22.99);

-- --------------------------------------------------------

--
-- Table structure for table `jeux`
--

DROP TABLE IF EXISTS `jeux`;
CREATE TABLE IF NOT EXISTS `jeux` (
  `id` int NOT NULL AUTO_INCREMENT,
  `titre` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `prix` decimal(10,2) NOT NULL DEFAULT '0.00',
  `date_sortie` date DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `categorie_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_jeu_categorie` (`categorie_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jeux`
--

INSERT INTO `jeux` (`id`, `titre`, `description`, `prix`, `date_sortie`, `image`, `categorie_id`) VALUES
(1, 'THE WALKING DEAD', 'Un jeu d\'aventure captivant.', 49.99, '2023-05-10', 'jeu_6a53f8fc90de02.78966140.jpg', 1),
(2, 'LIFE IS STRANGE', 'make choes game', 39.99, '2022-11-20', 'jeu_6a53f894c935b9.28520492.jpg', 1),
(4, 'DARK SOULS 3', 'soulslike game', 55.99, '2015-02-22', 'jeu_6a53f940de6de0.57870391.jpg', 3),
(5, 'TOMB RAIDER', 'AVENTURE GAME', 45.99, NULL, 'jeu_6a53f9c76da4f6.36330652.jpg', 2),
(6, 'HOLLOW KNIGHT', 'SOULSLIKE', 22.99, '2018-12-22', 'jeu_6a5408ac3f8c81.31968459.jpg', 3),
(7, 'DARK SOULS 2', 'SOULSLIKE GAME', 66.99, NULL, 'jeu_6a54cf20aaf736.02003946.jpg', 3),
(8, 'DARK SOULS REMASTRD', 'SOULSLIKE', 55.99, NULL, 'jeu_6a54d0bb2e7c72.07122753.jpg', 3);

-- --------------------------------------------------------

--
-- Table structure for table `utilisateurs`
--

DROP TABLE IF EXISTS `utilisateurs`;
CREATE TABLE IF NOT EXISTS `utilisateurs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `login` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mot_de_passe` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `login`, `mot_de_passe`) VALUES
(1, 'admin', '$2y$10$.odhT4zZlblQE0jFozsFgOfvytssToh5sd50kwTbWtBaZWrVzG7j.');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `commandes`
--
ALTER TABLE `commandes`
  ADD CONSTRAINT `fk_commande_client` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `commande_lignes`
--
ALTER TABLE `commande_lignes`
  ADD CONSTRAINT `fk_ligne_commande` FOREIGN KEY (`commande_id`) REFERENCES `commandes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ligne_jeu` FOREIGN KEY (`jeu_id`) REFERENCES `jeux` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `jeux`
--
ALTER TABLE `jeux`
  ADD CONSTRAINT `fk_jeu_categorie` FOREIGN KEY (`categorie_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
