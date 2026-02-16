<?php
namespace app\model; 
use Flight;
use PDO;

class DonModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Insert un don
    public function insertDon($article_id, $quantite, $donateur, $date_saisie) {
        $sql = "INSERT INTO don (article_id, quantite, donateur, date_saisie)
                VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([$article_id, $quantite, $donateur, $date_saisie]);
    }

    // Récupère tous les dons triés par date décroissante
    public function getAllDons() {
        $sql = "SELECT d.id, a.nom AS article, d.quantite, d.donateur, d.date_saisie
                FROM don d
                JOIN article a ON d.article_id = a.id
                ORDER BY d.date_saisie DESC, d.id DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
