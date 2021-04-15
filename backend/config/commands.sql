CREATE DATABASE assistance;

USE assistance;

CREATE TABLE users (
  id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  photo VARCHAR(30) NOT NULL,
  name VARCHAR(60) NOT NULL,
  email VARCHAR(60) NOT NULL,
  password CHAR(60) NOT NULL,
  cpf CHAR(15) NOT NULL,
  is_admin BOOLEAN NOT NULL,
  address VARCHAR(100) NOT NULL
);

CREATE TABLE user_numbers (
  id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  phone_number VARCHAR(20) NOT NULL,
  user_id INT NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
);

CREATE TABLE equipments (
  id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  specifications TEXT NOT NULL,
  user_id INT NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
);

CREATE TABLE equipment_photos (
  id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  photo CHAR(30) NOT NULL,
  equipment_id INT NOT NULL,
  FOREIGN KEY (equipment_id) REFERENCES equipments (id) ON DELETE CASCADE
);


CREATE TABLE requests (
  id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  created_at TIMESTAMP DEFAULT now(),
  updated_at TIMESTAMP DEFAULT now() ON UPDATE now(),
  status INT NOT NULL,
  cost varchar(20) NOT NULL,
  report TEXT NOT NULL,
  description TEXT NOT NULL,
  equipment_id INT NOT NULL,
  FOREIGN KEY (equipment_id) REFERENCES equipments (id) ON DELETE CASCADE
);