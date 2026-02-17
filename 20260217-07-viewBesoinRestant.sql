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
