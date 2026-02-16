<?php 
namespace app\model; 
use Flight;
use PDO;

Class ProduitModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllProduits() {
        $sql = "SELECT * FROM produits"; 
        return $this->db->query($sql)->fetchAll();
    }
    public function getProduitById($id) {
        $stmt = $this->db->prepare("SELECT id, nom, categorie_id FROM produits WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function addProduit($nom, $categorie_id){
        $sql = "INSERT INTO produits (nom, categorie_id) VALUES (:nom, :categorie_id)";
        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':categorie_id', $categorie_id);

        return $stmt->execute(); 
    }
    public function deleteProduit($id){
        $sql = "DELETE FROM produits WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }
    public function updateProduit($id, $nom, $categorie_id){
        $sql = "UPDATE produits 
                SET nom = :nom, categorie_id = :categorie_id
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

}