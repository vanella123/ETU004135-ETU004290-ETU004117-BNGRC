INSERT INTO region (nom) VALUES
('Atsinanana'),
('Vatovavy'),
('Atsimo Atsinanana'),
('Diana'),
('Menabe');

INSERT INTO ville (nom, region_id) VALUES
('Toamasina', 1),
('Mananjary', 2),
('Farafangana', 3),
('Nosy Be', 4),
('Morondava', 5);

INSERT INTO type_besoin (libelle) VALUES
('nature'),
('materiel'),
('argent');


INSERT INTO article (nom, type_besoin_id, prix_unitaire) VALUES
('Riz (kg)', 1, 3000),
('Eau (L)', 1, 1000),
('Huile (L)', 1, 6000),
('Haricots', 1, 4000),

('Tôle', 2, 25000),
('Bâche', 2, 15000),
('Clous (kg)', 2, 8000),
('Bois', 2, 10000),
('Groupe électrogène', 2, 6750000),

('Argent', 3, 1);





INSERT INTO besoin (ville_id, article_id, quantite, date_saisie) VALUES

-- TOAMASINA
(1, 1, 800, '2026-02-16'),
(1, 2, 1500, '2026-02-15'),
(1, 5, 120, '2026-02-16'),
(1, 6, 200, '2026-02-15'),
(1,10, 12000000, '2026-02-16'),
(1, 9, 3, '2026-02-15'),

-- MANANJARY
(2, 1, 500, '2026-02-15'),
(2, 3, 120, '2026-02-16'),
(2, 5, 80, '2026-02-15'),
(2, 7, 60, '2026-02-16'),
(2,10, 6000000, '2026-02-15'),

-- FARAFANGANA
(3, 1, 600, '2026-02-16'),
(3, 2, 1000, '2026-02-15'),
(3, 6, 150, '2026-02-16'),
(3, 8, 100, '2026-02-15'),
(3,10, 8000000, '2026-02-16'),

-- NOSY BE
(4, 1, 300, '2026-02-15'),
(4, 4, 200, '2026-02-16'),
(4, 5, 40, '2026-02-15'),
(4, 7, 30, '2026-02-16'),
(4,10, 4000000, '2026-02-15'),

-- MORONDAVA
(5, 1, 700, '2026-02-16'),
(5, 2, 1200, '2026-02-15'),
(5, 6, 180, '2026-02-16'),
(5, 8, 150, '2026-02-15'),
(5,10, 10000000, '2026-02-16');


INSERT INTO don (article_id, quantite, donateur_nom, date_saisie) VALUES
(1, 2000, 'ONG Aide Sud', '2026-02-17'),
(2, 3000, 'Croix Rouge', '2026-02-17'),
(5, 200, 'Entreprise BTP Mada', '2026-02-17'),
(10, 25000000, 'Banque Solidarité', '2026-02-17');


INSERT INTO configuration (id, frais_achat) VALUES
(1, 5.00);