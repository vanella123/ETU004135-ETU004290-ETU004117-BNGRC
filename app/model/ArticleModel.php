<?php
namespace app\model;

use PDO;

class ArticleModel {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    // ======================
    // Obtenir tous les produits
    // ======================
    public function getAllProduits() {
        $sql = "SELECT a.id, a.nom, a.prix_unitaire, tb.libelle AS type_besoin
                FROM article a
                JOIN type_besoin tb ON a.type_besoin_id = tb.id
                ORDER BY a.nom ASC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // ======================
    // Obtenir un produit par ID
    // ======================
    public function getProduitById($id) {
        $stmt = $this->db->prepare("SELECT * FROM article WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Récupérer l'ID d'un article par son nom
    public function getIdByNomArticle($nom) {
        $stmt = $this->db->prepare("SELECT id FROM article WHERE nom = :nom");
        $stmt->execute(['nom' => $nom]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res ? $res['id'] : null;
    }

    // ======================
    // Ajouter un produit
    // ======================
    public function addProduit($nom, $type_besoin_id, $prix_unitaire) {
        $sql = "INSERT INTO article (nom, type_besoin_id, prix_unitaire) 
                VALUES (:nom, :type_besoin_id, :prix_unitaire)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nom' => $nom,
            ':type_besoin_id' => $type_besoin_id,
            ':prix_unitaire' => $prix_unitaire
        ]);
    }

    // ======================
    // Modifier un produit
    // ======================
    public function updateProduit($id, $nom, $type_besoin_id, $prix_unitaire) {
        $sql = "UPDATE article
                SET nom = :nom, type_besoin_id = :type_besoin_id, prix_unitaire = :prix_unitaire
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nom' => $nom,
            ':type_besoin_id' => $type_besoin_id,
            ':prix_unitaire' => $prix_unitaire,
            ':id' => $id
        ]);
    }

    // ======================
    // Supprimer un produit
    // ======================
    public function deleteProduit($id) {
        $sql = "DELETE FROM article WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    // ======================
    // Obtenir tous les types de besoin (pour les listes déroulantes)
    // ======================
    public function getAllTypesBesoin() {
        $sql = "SELECT * FROM type_besoin ORDER BY nom ASC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}
