CREATE DATABASE customs_map
  DEFAULT CHARACTER SET utf8
  DEFAULT COLLATE utf8_general_ci;

USE customs_map;

CREATE TABLE customs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
    name VARCHAR(128),
    address VARCHAR(128),
    telephone VARCHAR(128),
    fax VARCHAR(128),
    email VARCHAR(128)
);

CREATE FULLTEXT INDEX customs_ft_search ON customs(name);