<?php

namespace app\model;

use PDO;

class DonModel {

    private $db;

    public function __construct($db){
        $this->db = $db;
    }

    // public function getAllDons(){
    //     $sql = "SELECT * FROM don ORDER BY date_saisie ASC";
    //     return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    // }

    public function getAllDons() {
        $sql = "
            SELECT 
                d.id,
                a.nom AS article,
                d.quantite,
                d.donateur_nom,
                d.date_saisie
            FROM don d
            LEFT JOIN article a ON d.article_id = a.id
            ORDER BY d.date_saisie DESC, d.id DESC
        ";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }


    public function insertDon($article_id, $quantite, $donateur, $date_saisie){
        $sql = "INSERT INTO don (article_id, quantite, donateur_nom, date_saisie)
                VALUES (?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$article_id, $quantite, $donateur, $date_saisie]);

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

    // Retourne le total d'argent disponible dans les dons
    public function getTotalArgentDisponible() {
        $sql = "
            SELECT IFNULL(SUM(d.quantite),0) AS total_argent
            FROM don d
            JOIN article a ON d.article_id = a.id
            JOIN type_besoin tb ON a.type_besoin_id = tb.id
            WHERE tb.libelle = 'Argent'
        ";
        return $this->db->query($sql)->fetch(PDO::FETCH_ASSOC)['total_argent'];
    }

    // Retourne le total d'achats déjà réalisés
    public function getTotalAchatsEffectues() {
        $sql = "SELECT IFNULL(SUM(montant_total),0) AS total_achats FROM achat";
        return $this->db->query($sql)->fetch(PDO::FETCH_ASSOC)['total_achats'];
    }

}