<?php 
namespace app\model; 
use Flight;
use PDO;

Class BesoinModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllbesoins() {
        $sql = "SELECT * FROM besoins"; 
        return $this->db->query($sql)->fetchAll();
    }
    public function addbesoin($nom, $categorie_id, $ville_id){
        $sql = "INSERT INTO besoins (nom, categorie_id, ville_id) VALUES (:nom, :categorie_id, :ville_id)";
        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':categorie_id', $categorie_id);
        $stmt->bindParam(':ville_id', $ville_id);

        return $stmt->execute(); 
    }
    
}