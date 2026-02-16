------------------------- Besoins supplémentaires -------------------------

-- Antananarivo
INSERT INTO besoin (ville_id, article_id, quantite, date_saisie) VALUES
(1, 2, 150, '2026-02-08'),  -- Huile
(1, 3, 100, '2026-02-09');  -- Sucre

-- Ambohidratrimo
INSERT INTO besoin (ville_id, article_id, quantite, date_saisie) VALUES
(2, 1, 200, '2026-02-08'),  -- Riz
(2, 5, 30, '2026-02-09');   -- Clou

-- Toamasina
INSERT INTO besoin (ville_id, article_id, quantite, date_saisie) VALUES
(3, 3, 150, '2026-02-10'), -- Sucre
(3, 6, 40, '2026-02-10');  -- Bois

-- Fianarantsoa
INSERT INTO besoin (ville_id, article_id, quantite, date_saisie) VALUES
(4, 2, 100, '2026-02-11'), -- Huile
(4, 5, 20, '2026-02-11');  -- Clou

-- Mahajanga
INSERT INTO besoin (ville_id, article_id, quantite, date_saisie) VALUES
(5, 3, 80, '2026-02-12'),  -- Sucre
(5, 6, 50, '2026-02-12');  -- Bois

------------------------- Dons supplémentaires -------------------------

-- Riz
INSERT INTO don (article_id, quantite, donateur_nom, date_saisie) VALUES
(1, 500, 'Association Riz Solidaire', '2026-02-09');

-- Huile
INSERT INTO don (article_id, quantite, donateur_nom, date_saisie) VALUES
(2, 200, 'ONG Nutrition', '2026-02-10');

-- Sucre
INSERT INTO don (article_id, quantite, donateur_nom, date_saisie) VALUES
(3, 300, 'Collectif Sucre Pour Tous', '2026-02-10');

-- Bois
INSERT INTO don (article_id, quantite, donateur_nom, date_saisie) VALUES
(6, 100, 'Entreprise Bois Malagasy', '2026-02-11');

-- Clou
INSERT INTO don (article_id, quantite, donateur_nom, date_saisie) VALUES
(5, 50, 'Société Clous et Matériaux', '2026-02-12');