CREATE DATABASE IF NOT EXISTS media_library;

USE media_library;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS livres_souhaites (
  id INT AUTO_INCREMENT PRIMARY KEY,
  titre VARCHAR(255) NOT NULL,
  auteur VARCHAR(255) DEFAULT '/',
  numero_tome INT DEFAULT 1,
  nombre_total_tomes INT DEFAULT 1,
  prix DECIMAL(10,2) DEFAULT 0.00,
  format VARCHAR(50) DEFAULT NULL,
  maison_edition VARCHAR(255) DEFAULT NULL,
  resume_livre TEXT,
  added_by INT DEFAULT NULL,
  FOREIGN KEY (added_by) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS livres_possedes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  titre VARCHAR(255) NOT NULL,
  auteur VARCHAR(255) DEFAULT '/',
  numero_tome INT DEFAULT 1,
  nombre_total_tomes INT DEFAULT 1,
  prix DECIMAL(10,2) DEFAULT 0.00,
  format VARCHAR(50) DEFAULT NULL,
  maison_edition VARCHAR(255) DEFAULT NULL,
  resume_livre TEXT,
  theme VARCHAR(255) DEFAULT NULL,
  notation_etoile INT DEFAULT NULL,
  commentaire TEXT,
  favori BOOLEAN DEFAULT false,
  notation_piments INT DEFAULT NULL,
  ecole BOOLEAN DEFAULT false,
  added_by INT DEFAULT NULL,
  FOREIGN KEY (added_by) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS films (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  director VARCHAR(255) DEFAULT '/',
  release_year INT DEFAULT 0,
  external_hard_drive INT DEFAULT NULL,
  added_by INT DEFAULT NULL,
  FOREIGN KEY (added_by) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS series (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  langue_serie VARCHAR(255) DEFAULT '/',
  complete_season BOOLEAN DEFAULT 0,
  episode_count INT DEFAULT 0,
  season_number INT DEFAULT 0,
  external_hard_drive INT DEFAULT NULL,
  added_by INT DEFAULT NULL,
  FOREIGN KEY (added_by) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS concerts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  artist VARCHAR(255) NOT NULL,
  external_hard_drive INT DEFAULT NULL,
  added_by INT DEFAULT NULL,
  FOREIGN KEY (added_by) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS documentaires (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  topic VARCHAR(255) DEFAULT NULL,
  external_hard_drive INT DEFAULT NULL,
  added_by INT DEFAULT NULL,
  FOREIGN KEY (added_by) REFERENCES users(id)
);

GRANT ALL PRIVILEGES ON media_library.* TO 'nathan'@'%' IDENTIFIED BY '444719';
FLUSH PRIVILEGES;
