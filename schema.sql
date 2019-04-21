CREATE DATABASE yeticave
DEFAULT CHARACTER SET utf8
DEFAULT COLLATE utf8_general_ci;

USE yeticave;

CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  char_code CHAR(20) NOT NULL UNIQUE
);

CREATE TABLE lots (
  id INT AUTO_INCREMENT PRIMARY KEY,
  date DATETIME NOT NULL,
  name CHAR(255) NOT NULL,
  description TEXT,
  img_url CHAR(255),
  price INT NOT NULL,
  date_finish DATETIME NOT NULL,
  step INT NOT NULL,
  user_id INT NOT NULL,
  winner_id INT,
  category_id INT NOT NULL
);

CREATE TABLE bets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  date DATETIME NOT NULL,
  price INT NOT NULL,
  user_id INT NOT NULL,
  lot_id INT NOT NULL
);

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  date_reg DATETIME NOT NULL,
  email CHAR(128) NOT NULL UNIQUE,
  name CHAR(128) NOT NULL UNIQUE,
  password CHAR(64) NOT NULL,
  avatar_url CHAR(255),
  contacts TEXT
);

CREATE TABLE created_lots (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  lot_id INT NOT NULL
);

CREATE TABLE created_bets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  bets_id INT NOT NULL
);

CREATE INDEX name ON lots(name);

