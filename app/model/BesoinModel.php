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
        $sql = "SELECT * FROM besoin"; 
        return $this->db->query($sql)->fetchAll();
    }
    public function addbesoin($article_id, $ville_id, $quantite, $date_saisie){
        $sql = "INSERT INTO besoin (article_id, ville_id , quantite , date_saisie) VALUES (:article_id, :ville_id, :quantite, :date_saisie)";
        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(':article_id', $article_id);
        $stmt->bindParam(':ville_id', $ville_id);
        $stmt->bindParam(':quantite', $quantite);
        $stmt->bindParam(':date_saisie', $date_saisie);

        return $stmt->execute(); 
    }

}