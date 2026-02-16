create table statut (
    id_statut int auto_increment primary key,
    libelle varchar(255) not null
);
alter table besoin add column statut_id int default 1;
alter table besoin add foreign key (statut_id) references statut(id_statut);