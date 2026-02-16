<?php 
namespace app\model; 
use Flight;
use PDO;

Class VilleModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllVille() {
        $sql = "SELECT * FROM ville"; 
        return $this->db->query($sql)->fetchAll();
    }
    public function getVilleById($id) {
        $stmt = $this->db->prepare("SELECT id, nom FROM ville WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function addVille($nom){
        $sql = "INSERT INTO ville (nom) VALUES (:nom)";
        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(':nom', $nom);

        return $stmt->execute(); 
    }
    public function deleteVille($id){
        $sql = "DELETE FROM ville WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }
    public function updateVille($id, $nom){
        $sql = "UPDATE ville 
                SET nom = :nom
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

}