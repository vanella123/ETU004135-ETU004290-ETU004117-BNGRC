<?php

namespace app\model;

use PDO;

class DonModel {

    private $db;

    public function __construct($db){
        $this->db = $db;
    }

    public function getAllDons(){
        $sql = "SELECT * FROM don ORDER BY date_saisie ASC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertDon($article_id, $quantite, $date_saisie){

        $sql = "INSERT INTO don (article_id, quantite, date_saisie)
                VALUES (?, ?, ?)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$article_id, $quantite, $date_saisie]);

        return $this->db->lastInsertId();
    }

    public function getEtatDon($don_id){

        $sql = "
            SELECT d.*,
            (d.quantite - IFNULL(SUM(r.quantite_repartie),0)) AS reste
            FROM don d
            LEFT JOIN repartition_don r ON d.id = r.don_id
            WHERE d.id = ?
            GROUP BY d.id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$don_id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}