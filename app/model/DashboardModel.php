<?php

namespace app\model;

use PDO;

class DashboardModel {

    private $db;

    public function __construct($db){
        $this->db = $db;
    }

    public function getDashboardData(){

        $sql = "
            SELECT 
                v.id AS ville_id,
                v.nom AS ville,
                a.nom AS article,
                b.id AS besoin_id,
                b.quantite AS quantite_demandee,
                IFNULL(SUM(r.quantite_repartie), 0) AS quantite_attribuee,
                (b.quantite - IFNULL(SUM(r.quantite_repartie), 0)) AS quantite_restante
            FROM besoin b
            JOIN ville v ON b.ville_id = v.id
            JOIN article a ON b.article_id = a.id
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

    public function getDashboardDatas(){

        $sql = "SELECT * FROM vue_dashboard ORDER BY ville ASC, article ASC";

        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}
