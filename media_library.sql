-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 20 juin 2024 à 01:02
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `media_library`
--

-- --------------------------------------------------------

--
-- Structure de la table `concerts`
--

CREATE TABLE `concerts` (
  `id` int(11) NOT NULL,
  `artist` varchar(255) NOT NULL,
  `external_hard_drive` int(11) DEFAULT NULL,
  `added_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `documentaires`
--

CREATE TABLE `documentaires` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `topic` varchar(255) DEFAULT NULL,
  `external_hard_drive` int(11) DEFAULT NULL,
  `added_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ecollyday`
--

CREATE TABLE `ecollyday` (
  `id` int(11) NOT NULL,
  `cell_number` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `films`
--

CREATE TABLE `films` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `director` varchar(255) DEFAULT '/',
  `release_year` int(11) DEFAULT 0,
  `external_hard_drive` int(11) DEFAULT NULL,
  `added_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `lien_users_roles`
--

CREATE TABLE `lien_users_roles` (
  `user_id` int(11) DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `livres_possedes`
--

CREATE TABLE `livres_possedes` (
  `id` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `auteur` varchar(255) DEFAULT '/',
  `numero_tome` int(11) DEFAULT 1,
  `nombre_total_tomes` int(11) DEFAULT 1,
  `prix` decimal(10,2) DEFAULT 0.00,
  `format` varchar(50) DEFAULT NULL,
  `maison_edition` varchar(255) DEFAULT NULL,
  `resume_livre` text DEFAULT NULL,
  `theme` varchar(255) DEFAULT NULL,
  `notation_etoile` int(11) DEFAULT NULL,
  `commentaire` text DEFAULT NULL,
  `favori` tinyint(1) DEFAULT 0,
  `notation_piments` int(11) DEFAULT NULL,
  `ecole` tinyint(1) DEFAULT 0,
  `added_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `livres_souhaites`
--

CREATE TABLE `livres_souhaites` (
  `id` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `auteur` varchar(255) DEFAULT '/',
  `numero_tome` int(11) DEFAULT 1,
  `nombre_total_tomes` int(11) DEFAULT 1,
  `prix` decimal(10,2) DEFAULT 0.00,
  `format` varchar(50) DEFAULT NULL,
  `maison_edition` varchar(255) DEFAULT NULL,
  `resume_livre` text DEFAULT NULL,
  `added_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `olympe`
--

CREATE TABLE `olympe` (
  `id` int(11) NOT NULL,
  `added_by` int(11) DEFAULT NULL,
  `budget_min` int(11) DEFAULT NULL,
  `budget_max` int(11) DEFAULT NULL,
  `dispo` varchar(255) DEFAULT NULL,
  `indispo` varchar(255) DEFAULT NULL,
  `pays_oui` varchar(255) DEFAULT NULL,
  `pays_non` varchar(255) DEFAULT NULL,
  `coef_pays` int(11) DEFAULT NULL,
  `transport` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `role_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `series`
--

CREATE TABLE `series` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `langue_serie` varchar(255) DEFAULT '/',
  `complete_season` tinyint(1) DEFAULT 0,
  `episode_count` int(11) DEFAULT 0,
  `season_number` int(11) DEFAULT 0,
  `external_hard_drive` int(11) DEFAULT NULL,
  `added_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `concerts`
--
ALTER TABLE `concerts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `added_by` (`added_by`);

--
-- Index pour la table `documentaires`
--
ALTER TABLE `documentaires`
  ADD PRIMARY KEY (`id`),
  ADD KEY `added_by` (`added_by`);

--
-- Index pour la table `ecollyday`
--
ALTER TABLE `ecollyday`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `films`
--
ALTER TABLE `films`
  ADD PRIMARY KEY (`id`),
  ADD KEY `added_by` (`added_by`);

--
-- Index pour la table `lien_users_roles`
--
ALTER TABLE `lien_users_roles`
  ADD KEY `role_id` (`role_id`),
  ADD KEY `lien_users_roles_ibfk_1` (`user_id`);

--
-- Index pour la table `livres_possedes`
--
ALTER TABLE `livres_possedes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `added_by` (`added_by`);

--
-- Index pour la table `livres_souhaites`
--
ALTER TABLE `livres_souhaites`
  ADD PRIMARY KEY (`id`),
  ADD KEY `added_by` (`added_by`);

--
-- Index pour la table `olympe`
--
ALTER TABLE `olympe`
  ADD PRIMARY KEY (`id`),
  ADD KEY `added_by` (`added_by`);

--
-- Index pour la table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `series`
--
ALTER TABLE `series`
  ADD PRIMARY KEY (`id`),
  ADD KEY `added_by` (`added_by`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `concerts`
--
ALTER TABLE `concerts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `documentaires`
--
ALTER TABLE `documentaires`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `ecollyday`
--
ALTER TABLE `ecollyday`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `films`
--
ALTER TABLE `films`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `livres_possedes`
--
ALTER TABLE `livres_possedes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `livres_souhaites`
--
ALTER TABLE `livres_souhaites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `olympe`
--
ALTER TABLE `olympe`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `series`
--
ALTER TABLE `series`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `concerts`
--
ALTER TABLE `concerts`
  ADD CONSTRAINT `concerts_ibfk_1` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `documentaires`
--
ALTER TABLE `documentaires`
  ADD CONSTRAINT `documentaires_ibfk_1` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `ecollyday`
--
ALTER TABLE `ecollyday`
  ADD CONSTRAINT `ecollyday_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `films`
--
ALTER TABLE `films`
  ADD CONSTRAINT `films_ibfk_1` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `lien_users_roles`
--
ALTER TABLE `lien_users_roles`
  ADD CONSTRAINT `lien_users_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lien_users_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

--
-- Contraintes pour la table `livres_possedes`
--
ALTER TABLE `livres_possedes`
  ADD CONSTRAINT `livres_possedes_ibfk_1` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `livres_souhaites`
--
ALTER TABLE `livres_souhaites`
  ADD CONSTRAINT `livres_souhaites_ibfk_1` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `series`
--
ALTER TABLE `series`
  ADD CONSTRAINT `series_ibfk_1` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
