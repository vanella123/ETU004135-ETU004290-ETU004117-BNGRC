CREATE DATABASE IF NOT EXISTS echange_db;

USE echange_db;

CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(255) NOT NULL UNIQUE,
    email VARCHAR (255) NOT NULL UNIQUE,
    mdp  VARCHAR(255) NOT NULL,
    photo VARCHAR(255) default 'default.png'
);
insert into users (username, email, mdp) values ('admin', 'admin@gmail.com', 'admin');

CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(255) NOT NULL,
    icon VARCHAR(255)
);

CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(255) NOT NULL,
    description VARCHAR(255),
    prix  DECIMAL(10,2),
    image VARCHAR(255), 
    categorie_id INT NOT NULL,
    user_id INT NOT NULL,
    FOREIGN KEY (categorie_id) REFERENCES categories(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE echange_status (
    id INT PRIMARY KEY AUTO_INCREMENT,
    etat VARCHAR(50)
);

CREATE TABLE echange (
    id INT PRIMARY KEY AUTO_INCREMENT,
    produit1_id INT NOT NULL,
    produit2_id INT NOT NULL,
    user1_id INT NOT NULL,
    user2_id INT NOT NULL,
    status_id INT NOT NULL,
    FOREIGN KEY (produit1_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (produit2_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user1_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (user2_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (status_id) REFERENCES echange_status(id) ON DELETE CASCADE
);

INSERT IGNORE INTO echange_status (id, etat) VALUES
(1, 'En attente'),
(2, 'Refusé'),
(3, 'Accepté');


INSERT INTO echange (produit1_id, produit2_id, user1_id, user2_id, status_id) VALUES
-- Échange en attente entre Aaron et Emily
(1, 2, 1, 2, 1),

-- Échange accepté entre Mike et Aaron
(3, 5, 3, 1, 3),

-- Échange refusé entre Emily et Mike
(4, 3, 2, 3, 2),

-- Échange en attente entre Aaron et Mike
(5, 3, 1, 3, 1);


