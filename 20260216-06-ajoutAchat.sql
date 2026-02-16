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

INSERT INTO achat (article_id, ville_id, quantite, prix_unitaire, frais_pourcentage, montant_total, date_achat) VALUES
(1, 1, 500, 2.50, 5.00, 1312.50, '2026-02-05'),
(2, 2, 200, 1.00, 3.00, 206.00, '2026-02-06'),
(3, 3, 100, 0.50, 2.00, 51.00, '2026-02-07'),
(4, 4, 150, 3.00, 4.00, 468.00, '2026-02-08');

INSERT INTO configuration (id, frais_achat) VALUES (1, 10);
