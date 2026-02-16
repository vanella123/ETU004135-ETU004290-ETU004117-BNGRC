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
    public function deletebesoin($id){
        $sql = "DELETE FROM besoin WHERE id = :id";
        $stmt = $this->db->prepare($sql);   
        return $stmt->execute();
    }
    public function updatebesoin($id, $article_id, $ville_id, $quantite, $date_saisie){
        $sql = "UPDATE besoin 
                SET article_id = :article_id, ville_id = :ville_id, quantite = :quantite, date_saisie = :date_saisie
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(':article_id', $article_id);
        $stmt->bindParam(':ville_id', $ville_id);
        $stmt->bindParam(':quantite', $quantite);
        $stmt->bindParam(':date_saisie', $date_saisie);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }
    public function getBesoinById($id) {
        $stmt = $this->db->prepare("SELECT * FROM besoin WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    

}