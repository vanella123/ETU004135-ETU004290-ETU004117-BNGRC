CREATE OR REPLACE VIEW vue_dashboard AS
SELECT 
    v.id AS ville_id,
    v.nom AS ville,
    a.id AS article_id,
    a.nom AS article,
    b.id AS besoin_id,
    b.quantite AS quantite_demandee,
    IFNULL(SUM(r.quantite_repartie), 0) AS quantite_attribuee,
    GREATEST(0, b.quantite - IFNULL(SUM(r.quantite_repartie), 0)) AS quantite_restante
FROM besoin b
JOIN ville v ON b.ville_id = v.id
JOIN article a ON b.article_id = a.id
LEFT JOIN repartition_don r ON b.id = r.besoin_id
GROUP BY b.id;


