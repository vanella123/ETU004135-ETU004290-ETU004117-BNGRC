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

create table statut (
    id_statut int auto_increment primary key,
    libelle varchar(255) not null
);

insert into statut (libelle) values ('En attente'), ('En cours'), ('Satisfait');
alter table besoin add column statut_id int default 1;
alter table besoin add foreign key (statut_id) references statut(id_statut);
ALTER TABLE besoin ADD COLUMN ordre INT DEFAULT 1;

CREATE TABLE achat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    article_id INT NOT NULL,
    ville_id INT NOT NULL,
    quantite INT NOT NULL,
    prix_unitaire DECIMAL(10,2) NOT NULL,
    frais_pourcentage DECIMAL(5,2) NOT NULL,
    montant_total DECIMAL(10,2) NOT NULL,
    date_achat DATE NOT NULL,
    FOREIGN KEY (article_id) REFERENCES article(id),
    FOREIGN KEY (ville_id) REFERENCES ville(id)
);

CREATE TABLE configuration (
    id INT PRIMARY KEY,
    frais_achat DECIMAL(5,2) NOT NULL
);


CREATE OR REPLACE VIEW view_besoins_restants AS
SELECT
    v.nom AS ville,
    a.nom AS article,
    tb.libelle AS type_besoin,
    b.quantite AS quantite_restante,
    a.prix_unitaire,
    (b.quantite * a.prix_unitaire) AS montant_restant

FROM besoin b
JOIN article a ON b.article_id = a.id
JOIN type_besoin tb ON a.type_besoin_id = tb.id
JOIN ville v ON b.ville_id = v.id

-- uniquement les besoins non satisfaits
WHERE b.statut_id <> 2 OR b.statut_id IS NULL;
