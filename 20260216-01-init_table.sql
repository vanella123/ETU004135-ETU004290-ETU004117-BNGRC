CREATE DATABASE IF NOT EXISTS bngrc_db;

USE bngrc_db;

CREATE TABLE region (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(255) NOT NULL UNIQUE
);

CREATE TABLE ville (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(255) NOT NULL,
    region_id INT NOT NULL,
    FOREIGN KEY (region_id) REFERENCES region(id) ON DELETE CASCADE
);

CREATE TABLE type_besoin (
    id INT PRIMARY KEY AUTO_INCREMENT,
    libelle VARCHAR(255) NOT NULL UNIQUE
);

CREATE TABLE article (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(255) NOT NULL,
    type_besoin_id INT NOT NULL,
    prix_unitaire DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (type_besoin_id) REFERENCES type_besoin(id) ON DELETE CASCADE
);

CREATE TABLE besoin (
    id INT PRIMARY KEY AUTO_INCREMENT,
    ville_id INT NOT NULL,
    article_id INT NOT NULL,
    quantite INT NOT NULL,
    date_saisie DATE NOT NULL,
    FOREIGN KEY (ville_id) REFERENCES ville(id) ON DELETE CASCADE,
    FOREIGN KEY (article_id) REFERENCES article(id) ON DELETE CASCADE
);

CREATE TABLE don (
    id INT PRIMARY KEY AUTO_INCREMENT,
    article_id INT NOT NULL,
    quantite INT NOT NULL,
    donateur_nom VARCHAR(255),
    date_saisie DATE NOT NULL,
    FOREIGN KEY (article_id) REFERENCES article(id) ON DELETE CASCADE
);

CREATE TABLE repartition_don (
    id INT PRIMARY KEY AUTO_INCREMENT,
    don_id INT NOT NULL,
    besoin_id INT NOT NULL,
    quantite_repartie INT NOT NULL,
    date_repartition DATETIME NOT NULL,
    FOREIGN KEY (don_id) REFERENCES don(id) ON DELETE CASCADE,
    FOREIGN KEY (besoin_id) REFERENCES besoin(id) ON DELETE CASCADE
);

------------------------- Données initiales si besoin -------------------------

INSERT INTO region (nom) VALUES
('Analamanga'),
('Atsinanana'),
('Haute Matsiatra'),
('Boeny');

INSERT INTO ville (nom, region_id) VALUES
('Antananarivo', 1),
('Ambohidratrimo', 1),
('Toamasina', 2),
('Fianarantsoa', 3),
('Mahajanga', 4);

INSERT INTO type_besoin (libelle) VALUES ('Nature'), ('Materiaux'), ('Argent');

INSERT INTO article (nom, type_besoin_id, prix_unitaire) VALUES
-- Nature
('Riz (kg)', 1, 2500),
('Huile (L)', 1, 8000),
('Sucre (kg)', 1, 3000),

-- Materiaux
('Tole', 2, 45000),
('Clou (kg)', 2, 12000),
('Bois', 2, 35000),

-- Argent
('Argent (MGA)', 3, 1);


INSERT INTO besoin (ville_id, article_id, quantite, date_saisie) VALUES
-- Antananarivo
(1, 1, 500, '2026-02-01'), -- Riz
(1, 4, 100, '2026-02-01'), -- Tole
(1, 7, 2000000, '2026-02-02'), -- Argent

-- Toamasina
(3, 1, 800, '2026-02-03'),
(3, 2, 200, '2026-02-03'),
(3, 5, 50, '2026-02-04'),

-- Fianarantsoa
(4, 3, 400, '2026-02-05'),
(4, 6, 70, '2026-02-05'),
(4, 7, 1500000, '2026-02-06'),

-- Mahajanga
(5, 1, 300, '2026-02-07'),
(5, 4, 60, '2026-02-07');


INSERT INTO don (article_id, quantite, donateur_nom, date_saisie) VALUES
-- Riz
(1, 1000, 'Association Riz Pour Tous', '2026-02-02'),

-- Huile
(2, 300, 'ONG Huile & Santé', '2026-02-03'),

-- Tôle
(4, 120, 'Entreprise Bâtiment Mahajanga', '2026-02-04'),

-- Clou
(5, 100, 'Entreprise Bâtiment Mahajanga', '2026-02-05'),

-- Argent
(7, 5000000, 'Fondation Solidarité Madagascar', '2026-02-06');



