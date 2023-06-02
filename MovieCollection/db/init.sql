CREATE DATABASE IF NOT EXISTS movie_collection;

USE movie_collection;

CREATE TABLE IF NOT EXISTS films (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  director VARCHAR(255) DEFAULT '/',
  release_year INT DEFAULT 0,
  external_hard_drive INT DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS series (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  langue_serie VARCHAR(255) DEFAULT '/',
  complete_season BOOLEAN DEFAULT 0,
  episode_count INT DEFAULT 0,
  season_number INT DEFAULT 0,
  external_hard_drive INT DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS livres (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  author VARCHAR(255) DEFAULT '/',
  publication_year INT DEFAULT 0,
  genre VARCHAR(255) DEFAULT NULL,
  tome_count INT DEFAULT 1,
  external_hard_drive INT DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS concerts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  artist VARCHAR(255) NOT NULL,
  external_hard_drive INT DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS documentaires (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  topic VARCHAR(255) DEFAULT NULL,
  external_hard_drive INT DEFAULT NULL
);

GRANT ALL PRIVILEGES ON movie_collection.* TO 'nathan'@'%' IDENTIFIED BY '444719';
FLUSH PRIVILEGES;
