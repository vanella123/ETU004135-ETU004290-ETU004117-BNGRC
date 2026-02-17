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


INSERT INTO configuration (id, frais_achat) VALUES
(1, 5.00);

ALTER TABLE besoin ADD COLUMN ordre INT DEFAULT 1;

INSERT INTO besoin (ville_id, article_id, quantite, date_saisie, ordre) VALUES

-- =======================
-- TOAMASINA
-- =======================
(1, 1, 800, '2026-02-16', 17),   -- Riz
(1, 2, 1500, '2026-02-15', 4),   -- Eau
(1, 5, 120, '2026-02-16', 23),   -- Tôle
(1, 6, 200, '2026-02-15', 1),    -- Bâche
(1,10, 12000000, '2026-02-16', 12), -- Argent
(1, 9, 3, '2026-02-15', 16),     -- Groupe électrogène

-- =======================
-- MANANJARY
-- =======================
(2, 1, 500, '2026-02-15', 9),    -- Riz
(2, 3, 120, '2026-02-16', 25),   -- Huile
(2, 5, 80, '2026-02-15', 6),     -- Tôle
(2, 7, 60, '2026-02-16', 19),    -- Clous
(2,10, 6000000, '2026-02-15', 3),-- Argent

-- =======================
-- FARAFANGANA
-- =======================
(3, 1, 600, '2026-02-16', 21),   -- Riz
(3, 2, 1000, '2026-02-15', 14),  -- Eau
(3, 6, 150, '2026-02-16', 8),    -- Bâche
(3, 8, 100, '2026-02-15', 26),   -- Bois
(3,10, 8000000, '2026-02-16', 10),-- Argent

-- =======================
-- NOSY BE
-- =======================
(4, 1, 300, '2026-02-15', 5),    -- Riz
(4, 4, 200, '2026-02-16', 18),   -- Haricots
(4, 5, 40, '2026-02-15', 2),     -- Tôle
(4, 7, 30, '2026-02-16', 24),    -- Clous
(4,10, 4000000, '2026-02-15', 7),-- Argent

-- =======================
-- MORONDAVA
-- =======================
(5, 1, 700, '2026-02-16', 11),   -- Riz
(5, 2, 1200, '2026-02-15', 20),  -- Eau
(5, 6, 180, '2026-02-16', 15),   -- Bâche
(5, 8, 150, '2026-02-15', 22),   -- Bois
(5,10, 10000000, '2026-02-16', 13); -- Argent


