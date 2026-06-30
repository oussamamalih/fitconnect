-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 29, 2026 at 12:43 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fitconnect_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `abonnement`
--

CREATE TABLE `abonnement` (
  `id_abonnement` int(11) NOT NULL,
  `type_abonnement` enum('Mensuel','Trimestriel','Annuel') NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `statut` enum('Actif','Expire','Resilie') NOT NULL,
  `id_adherent` int(11) NOT NULL
) ;

--
-- Dumping data for table `abonnement`
--

INSERT INTO `abonnement` (`id_abonnement`, `type_abonnement`, `date_debut`, `date_fin`, `statut`, `id_adherent`) VALUES
(1, 'Annuel', '2026-01-10', '2027-01-09', 'Actif', 1),
(2, 'Mensuel', '2026-06-01', '2026-06-30', 'Actif', 2),
(3, 'Trimestriel', '2026-03-15', '2026-06-14', 'Expire', 3),
(4, 'Annuel', '2026-04-20', '2027-04-19', 'Actif', 4),
(5, 'Mensuel', '2026-05-12', '2026-06-11', 'Expire', 5);

-- --------------------------------------------------------

--
-- Table structure for table `adherent`
--

CREATE TABLE `adherent` (
  `id_adherent` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `date_inscription` date NOT NULL,
  `id_salle` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `adherent`
--

INSERT INTO `adherent` (`id_adherent`, `nom`, `prenom`, `email`, `telephone`, `date_inscription`, `id_salle`) VALUES
(1, 'Alaoui', 'Yassine', 'yassine.alaoui@gmail.com', '0612345678', '2026-01-10', 1),
(2, 'Benali', 'Sara', 'sara.benali@gmail.com', '0623456789', '2026-02-05', 2),
(3, 'Amrani', 'Hamza', 'hamza.amrani@gmail.com', '0634567890', '2026-03-15', 3),
(4, 'Idrissi', 'Nadia', 'nadia.idrissi@gmail.com', '0645678901', '2026-04-20', 4),
(5, 'El Fassi', 'Omar', 'omar.elfassi@gmail.com', '0656789012', '2026-05-12', 1);

-- --------------------------------------------------------

--
-- Table structure for table `salle`
--

CREATE TABLE `salle` (
  `id_salle` int(11) NOT NULL,
  `nom_salle` varchar(100) NOT NULL,
  `ville` varchar(100) NOT NULL,
  `adresse` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `salle`
--

INSERT INTO `salle` (`id_salle`, `nom_salle`, `ville`, `adresse`) VALUES
(1, 'FitConnect Centre', 'Casablanca', '12 Boulevard Hassan II'),
(2, 'FitConnect Maarif', 'Casablanca', '45 Rue Ghandi'),
(3, 'FitConnect Agdal', 'Rabat', '18 Avenue Mohammed V'),
(4, 'FitConnect Guéliz', 'Marrakech', '22 Rue Ibn Aicha');

-- --------------------------------------------------------

--
-- Table structure for table `seance`
--

CREATE TABLE `seance` (
  `id_seance` int(11) NOT NULL,
  `date_seance` date NOT NULL,
  `type_activite` varchar(100) NOT NULL,
  `duree` int(11) NOT NULL,
  `equipement_utilise` varchar(100) DEFAULT NULL,
  `id_adherent` int(11) NOT NULL,
  `id_salle` int(11) NOT NULL
) ;

--
-- Dumping data for table `seance`
--

INSERT INTO `seance` (`id_seance`, `date_seance`, `type_activite`, `duree`, `equipement_utilise`, `id_adherent`, `id_salle`) VALUES
(1, '2026-06-20', 'Musculation', 60, 'Haltères', 1, 1),
(2, '2026-06-21', 'Cardio', 45, 'Tapis de course', 2, 2),
(3, '2026-06-18', 'Cyclisme', 50, 'Vélo', 3, 3),
(4, '2026-06-22', 'Yoga', 90, NULL, 4, 4),
(5, '2026-06-23', 'Musculation', 75, 'Barre olympique', 1, 1),
(6, '2026-06-24', 'Cardio', 40, 'Elliptique', 2, 2),
(7, '2026-06-20', 'Musculation', 60, 'Haltères', 1, 1),
(8, '2026-06-21', 'Cardio', 45, 'Tapis de course', 2, 2),
(9, '2026-06-18', 'Cyclisme', 50, 'Vélo', 3, 3),
(10, '2026-06-22', 'Yoga', 90, NULL, 4, 4),
(11, '2026-06-23', 'Musculation', 75, 'Barre olympique', 1, 1),
(12, '2026-06-24', 'Cardio', 40, 'Elliptique', 2, 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `abonnement`
--
ALTER TABLE `abonnement`
  ADD PRIMARY KEY (`id_abonnement`),
  ADD KEY `fk_abonnement_adherent` (`id_adherent`);

--
-- Indexes for table `adherent`
--
ALTER TABLE `adherent`
  ADD PRIMARY KEY (`id_adherent`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_adherent_salle` (`id_salle`);

--
-- Indexes for table `salle`
--
ALTER TABLE `salle`
  ADD PRIMARY KEY (`id_salle`);

--
-- Indexes for table `seance`
--
ALTER TABLE `seance`
  ADD PRIMARY KEY (`id_seance`),
  ADD KEY `fk_seance_adherent` (`id_adherent`),
  ADD KEY `fk_seance_salle` (`id_salle`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `abonnement`
--
ALTER TABLE `abonnement`
  MODIFY `id_abonnement` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `adherent`
--
ALTER TABLE `adherent`
  MODIFY `id_adherent` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `salle`
--
ALTER TABLE `salle`
  MODIFY `id_salle` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `seance`
--
ALTER TABLE `seance`
  MODIFY `id_seance` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `abonnement`
--
ALTER TABLE `abonnement`
  ADD CONSTRAINT `fk_abonnement_adherent` FOREIGN KEY (`id_adherent`) REFERENCES `adherent` (`id_adherent`);

--
-- Constraints for table `adherent`
--
ALTER TABLE `adherent`
  ADD CONSTRAINT `fk_adherent_salle` FOREIGN KEY (`id_salle`) REFERENCES `salle` (`id_salle`);

--
-- Constraints for table `seance`
--
ALTER TABLE `seance`
  ADD CONSTRAINT `fk_seance_adherent` FOREIGN KEY (`id_adherent`) REFERENCES `adherent` (`id_adherent`),
  ADD CONSTRAINT `fk_seance_salle` FOREIGN KEY (`id_salle`) REFERENCES `salle` (`id_salle`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
