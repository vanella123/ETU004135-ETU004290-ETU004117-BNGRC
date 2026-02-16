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
        $sql = "SELECT * FROM article"; 
        return $this->db->query($sql)->fetchAll();
    }
    public function getProduitById($id) {
        $stmt = $this->db->prepare("SELECT * FROM article WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function addProduit($nom, $type_besoin_id, $prix_unitaire){
        $sql = "INSERT INTO article (nom, type_besoin_id , prix_unitaire) VALUES (:nom, :type_besoin_id, :prix_unitaire)";
        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':type_besoin_id', $type_besoin_id);
        $stmt->bindParam(':prix_unitaire', $prix_unitaire);
        return $stmt->execute(); 
    }
    public function deleteProduit($id){
        $sql = "DELETE FROM article WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }
    public function updateProduit($id, $nom, $type_besoin_id, $prix_unitaire){
        $sql = "UPDATE article 
                SET nom = :nom, type_besoin_id = :type_besoin_id, prix_unitaire = :prix_unitaire
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':type_besoin_id', $type_besoin_id);
        $stmt->bindParam(':prix_unitaire', $prix_unitaire);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

}