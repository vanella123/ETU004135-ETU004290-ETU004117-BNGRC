<?php

namespace app\model;

use PDO;

class AchatModel {
    private $db;

    public function __construct($db){
        $this->db = $db;
    }

    // Insérer un nouvel achat
    public function insertAchat($article_id, $ville_id, $quantite, $prix_unitaire, $frais_pourcentage, $montant_total, $date_achat){
        $sql = "INSERT INTO achat (article_id, ville_id, quantite, prix_unitaire, frais_pourcentage, montant_total, date_achat)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$article_id, $ville_id, $quantite, $prix_unitaire, $frais_pourcentage, $montant_total, $date_achat]);
        return $this->db->lastInsertId();
    }

    // Récupérer tous les achats
    public function getAllAchats() {
        $sql = "
            SELECT a.id, ar.nom AS article, v.nom AS ville, a.quantite, a.prix_unitaire, a.frais_pourcentage, a.montant_total, a.date_achat
            FROM achat a
            JOIN article ar ON a.article_id = ar.id
            JOIN ville v ON a.ville_id = v.id
            ORDER BY a.date_achat DESC, a.id DESC";

        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer les achats filtrés par ville
    public function getAchatsByVille($ville_id) {
        $sql = "
            SELECT a.id, ar.nom AS article, v.nom AS ville, a.quantite, a.prix_unitaire, a.frais_pourcentage, a.montant_total, a.date_achat
            FROM achat a
            JOIN article ar ON a.article_id = ar.id
            JOIN ville v ON a.ville_id = v.id
            WHERE v.id = ?
            ORDER BY a.date_achat DESC, a.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$ville_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
