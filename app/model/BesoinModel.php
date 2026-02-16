<?php
namespace app\model;

use PDO;

class BesoinModel {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    // ======================
    // Obtenir tous les besoins avec ville et article
    // ======================
    public function getAllBesoins() {
        $sql = "SELECT b.id, b.quantite, b.date_saisie, 
                       v.nom AS ville, 
                       a.nom AS article, 
                       a.prix_unitaire
                FROM besoin b
                JOIN ville v ON b.ville_id = v.id
                JOIN article a ON b.article_id = a.id
                ORDER BY b.date_saisie ASC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // ======================
    // Obtenir un besoin par ID
    // ======================
    public function getBesoinById($id) {
        $stmt = $this->db->prepare("SELECT * FROM besoin WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ======================
    // Ajouter un besoin
    // ======================
    public function addBesoin($ville_id, $article_id, $quantite, $date_saisie) {
        $sql = "INSERT INTO besoin (ville_id, article_id, quantite, date_saisie)
                VALUES (:ville_id, :article_id, :quantite, :date_saisie)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':ville_id' => $ville_id,
            ':article_id' => $article_id,
            ':quantite' => $quantite,
            ':date_saisie' => $date_saisie
        ]);
    }

    // ======================
    // Modifier un besoin
    // ======================
    public function updateBesoin($id, $ville_id, $article_id, $quantite, $date_saisie) {
        $sql = "UPDATE besoin
                SET ville_id = :ville_id, article_id = :article_id, quantite = :quantite, date_saisie = :date_saisie
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':ville_id' => $ville_id,
            ':article_id' => $article_id,
            ':quantite' => $quantite,
            ':date_saisie' => $date_saisie,
            ':id' => $id
        ]);
    }

    // ======================
    // Supprimer un besoin
    // ======================
    public function deleteBesoin($id) {
        $sql = "DELETE FROM besoin WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    // ======================
    // Obtenir tous les besoins pour une ville spécifique
    // ======================
    public function getBesoinsByVille($ville_id) {
        $sql = "SELECT b.id, b.quantite, b.date_saisie, 
                       a.nom AS article, 
                       a.prix_unitaire
                FROM besoin b
                JOIN article a ON b.article_id = a.id
                WHERE b.ville_id = :ville_id
                ORDER BY b.date_saisie ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':ville_id' => $ville_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRecapBesoin(){
        $sql = "select * from view_resume_besoins"; 
        return $this->db->query($sql)->fetch(PDO::FETCH_ASSOC);
    }
}
