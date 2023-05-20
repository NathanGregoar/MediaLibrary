CREATE DATABASE IF NOT EXISTS movie_collection;

USE movie_collection;

CREATE TABLE IF NOT EXISTS films (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  director VARCHAR(255) NOT NULL,
  release_year INT NOT NULL
);
