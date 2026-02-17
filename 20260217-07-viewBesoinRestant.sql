CREATE OR REPLACE VIEW view_besoins_restants AS
SELECT
    v.nom AS ville,
    a.nom AS article,
    b.quantite AS quantite_restante,
    a.prix_unitaire,
    (b.quantite * a.prix_unitaire) AS montant_restant,
    b.type_besoin  -- <-- type du besoin

FROM besoin b
JOIN article a ON b.article_id = a.id
JOIN ville v ON b.ville_id = v.id

WHERE b.statut_id <> 2 OR b.statut_id IS NULL;
