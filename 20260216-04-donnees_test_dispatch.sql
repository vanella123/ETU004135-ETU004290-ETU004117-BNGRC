-- =====================================================================
-- DONNÉES DE TEST RAPIDES - À exécuter après init_table.sql
-- =====================================================================
-- Ce fichier ajoute des besoins supplémentaires pour tester le dispatch
-- Ensuite, vous pouvez ajouter des dons via l'API ou SQL
-- =====================================================================

USE bngrc_db;

-- =====================================================================
-- BESOINS SUPPLÉMENTAIRES POUR TESTS
-- =====================================================================

-- Besoins en Sucre
INSERT INTO besoin (ville_id, article_id, quantite, date_saisie) VALUES
(1, 3, 200, '2026-02-10'), -- Antananarivo : 200 kg Sucre
(3, 3, 150, '2026-02-11'), -- Toamasina : 150 kg Sucre
(5, 3, 100, '2026-02-12'); -- Mahajanga : 100 kg Sucre

-- Besoins en Clou
INSERT INTO besoin (ville_id, article_id, quantite, date_saisie) VALUES
(1, 5, 30, '2026-02-13'), -- Antananarivo : 30 kg Clou
(4, 5, 40, '2026-02-14'), -- Fianarantsoa : 40 kg Clou
(3, 5, 25, '2026-02-15'); -- Toamasina : 25 kg Clou

-- Besoins en Bois
INSERT INTO besoin (ville_id, article_id, quantite, date_saisie) VALUES
(3, 6, 50, '2026-02-16'), -- Toamasina : 50 unités Bois
(5, 6, 35, '2026-02-17'), -- Mahajanga : 35 unités Bois
(1, 6, 40, '2026-02-18'); -- Antananarivo : 40 unités Bois


SELECT 'État des Besoins' AS Info;

SELECT 
    v.nom AS Ville,
    a.nom AS Article,
    b.quantite AS Demandé,
    IFNULL(SUM(r.quantite_repartie), 0) AS Attribué,
    GREATEST(0, b.quantite - IFNULL(SUM(r.quantite_repartie), 0)) AS Restant,
    CASE 
        WHEN GREATEST(0, b.quantite - IFNULL(SUM(r.quantite_repartie), 0)) = 0 THEN '✅ Satisfait'
        WHEN IFNULL(SUM(r.quantite_repartie), 0) > 0 THEN '⚠️ Partiel'
        ELSE '❌ Non satisfait'
    END AS Statut
FROM besoin b
JOIN ville v ON b.ville_id = v.id
JOIN article a ON b.article_id = a.id
LEFT JOIN repartition_don r ON b.id = r.besoin_id
GROUP BY b.id
ORDER BY v.nom, a.nom;

-- =====================================================================

SELECT 'État des Dons' AS Info;

SELECT 
    d.id AS ID,
    d.donateur_nom AS Donateur,
    a.nom AS Article,
    d.quantite AS Quantité,
    IFNULL(SUM(r.quantite_repartie), 0) AS Distribué,
    GREATEST(0, d.quantite - IFNULL(SUM(r.quantite_repartie), 0)) AS Reste,
    CASE 
        WHEN GREATEST(0, d.quantite - IFNULL(SUM(r.quantite_repartie), 0)) = 0 THEN '✅ Totalement distribué'
        WHEN IFNULL(SUM(r.quantite_repartie), 0) > 0 THEN '⚠️ Partiellement distribué'
        ELSE '❌ Non distribué'
    END AS Statut
FROM don d
JOIN article a ON d.article_id = a.id
LEFT JOIN repartition_don r ON d.id = r.don_id
GROUP BY d.id
ORDER BY d.date_saisie;

-- =====================================================================

SELECT 'Répartitions Effectuées' AS Info;

SELECT 
    r.id AS ID,
    d.donateur_nom AS Donateur,
    v.nom AS Ville,
    a.nom AS Article,
    r.quantite_repartie AS Quantité,
    r.date_repartition AS Date
FROM repartition_don r
JOIN don d ON r.don_id = d.id
JOIN besoin b ON r.besoin_id = b.id
JOIN ville v ON b.ville_id = v.id
JOIN article a ON d.article_id = a.id
ORDER BY r.date_repartition DESC
LIMIT 20;

-- =====================================================================
-- NETTOYAGE (si besoin de recommencer les tests)
-- =====================================================================

-- Décommentez ces lignes pour nettoyer toutes les données de test
-- et recommencer depuis zéro (garde la structure des tables)

-- DELETE FROM repartition_don;
-- DELETE FROM don;
-- DELETE FROM besoin;
-- ALTER TABLE repartition_don AUTO_INCREMENT = 1;
-- ALTER TABLE don AUTO_INCREMENT = 1;
-- ALTER TABLE besoin AUTO_INCREMENT = 1;

-- Puis réexécutez les INSERT des besoins ci-dessus
