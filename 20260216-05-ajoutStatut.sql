create table statut (
    id_statut int auto_increment primary key,
    libelle varchar(255) not null
);
insert into statut (libelle) values ('En attente'), ('En cours'), ('Satisfait');
alter table besoin add column statut_id int default 1;
alter table besoin add foreign key (statut_id) references statut(id_statut);

CREATE VIEW view_resume_besoins AS
SELECT
    SUM(b.quantite * a.prix_unitaire) AS besoins_totaux,

    SUM(CASE 
        WHEN b.statut_id = 2 
        THEN b.quantite * a.prix_unitaire
        ELSE 0
    END) AS besoins_satisfaits,

    SUM(CASE 
        WHEN b.statut_id <> 2 OR b.statut_id IS NULL
        THEN b.quantite * a.prix_unitaire
        ELSE 0 
    END) AS besoins_restants

FROM besoin b 
JOIN article a ON b.article_id = a.id;
