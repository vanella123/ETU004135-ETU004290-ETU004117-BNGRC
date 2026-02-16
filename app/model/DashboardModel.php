<?php

namespace app\model;

use PDO;

class DashboardModel {

    private $db;

    public function __construct($db){
        $this->db = $db;
    }

    public function getDashboardData(){

        // D'abord mettre à jour les statuts en fonction de l'état des besoins
        $this->mettreAJourStatuts();

        $sql = "
            SELECT 
                v.id AS ville_id,
                v.nom AS ville,
                a.nom AS article,
                b.id AS besoin_id,
                b.quantite AS quantite_demandee,
                IFNULL(SUM(r.quantite_repartie), 0) AS quantite_attribuee,
                (b.quantite - IFNULL(SUM(r.quantite_repartie), 0)) AS quantite_restante,
                s.libelle AS statut,
                s.id_statut
            FROM besoin b
            JOIN ville v ON b.ville_id = v.id
            JOIN article a ON b.article_id = a.id
            JOIN statut s ON b.statut_id = s.id_statut
            LEFT JOIN repartition_don r ON b.id = r.besoin_id
            GROUP BY b.id
            ORDER BY v.nom ASC, a.nom ASC
        ";

        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // Option bonus : total par ville
    public function getTotalParVille(){

        $sql = "
            SELECT 
                v.nom AS ville,
                SUM(b.quantite) AS total_demandee,
                IFNULL(SUM(r.quantite_repartie), 0) AS total_attribuee,
                (SUM(b.quantite) - IFNULL(SUM(r.quantite_repartie), 0)) AS total_restante
            FROM besoin b
            JOIN ville v ON b.ville_id = v.id
            LEFT JOIN repartition_don r ON b.id = r.besoin_id
            GROUP BY v.id
            ORDER BY v.nom ASC
        ";

        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Met à jour les statuts des besoins en fonction de l'état de leurs attributions
     */
    private function mettreAJourStatuts(){
        
        // Statut 1 = En attente (pas de dons du tout)
        // Statut 2 = En cours (partiellement satisfait)
        // Statut 3 = Satisfait (totalement satisfait)
        
        $sql = "
            UPDATE besoin b 
            SET b.statut_id = (
                CASE 
                    WHEN (SELECT IFNULL(SUM(r.quantite_repartie), 0) 
                          FROM repartition_don r 
                          WHERE r.besoin_id = b.id) = 0 
                    THEN 1  -- En attente
                    
                    WHEN (SELECT IFNULL(SUM(r.quantite_repartie), 0) 
                          FROM repartition_don r 
                          WHERE r.besoin_id = b.id) >= b.quantite 
                    THEN 3  -- Satisfait
                    
                    ELSE 2  -- En cours
                END
            )
        ";
        
        return $this->db->exec($sql);
    }

    public function getDashboardDatas(){

        $sql = "SELECT * FROM vue_dashboard ORDER BY ville ASC, article ASC";

        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}
