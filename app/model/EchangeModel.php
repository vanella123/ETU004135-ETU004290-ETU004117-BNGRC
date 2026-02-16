<?php 
namespace app\model; 
use Flight;
use PDO;

Class EchangeModel {

    private $db; 

    public function __construct($db){
        $this->db=$db; 
    }

    public function getAllEchanges() {
        $sql = "SELECT 
                    e.id,
                    p1.nom AS produit1,
                    p2.nom AS produit2,
                    u1.username AS user1,
                    u2.username AS user2,
                    s.etat,
                    e.status_id,
                    e.date_envoie,
                    e.date_acceptation
                FROM echange e
                JOIN products p1 ON e.produit1_id = p1.id
                JOIN products p2 ON e.produit2_id = p2.id
                JOIN users u1 ON e.user1_id = u1.id
                JOIN users u2 ON e.user2_id = u2.id
                JOIN echange_status s ON e.status_id = s.id
                ORDER BY e.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Total count of exchanges
        $countSql = "SELECT COUNT(*) AS cnt FROM echange";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute();
        $countRow = $countStmt->fetch(PDO::FETCH_ASSOC);
        $count = isset($countRow['cnt']) ? (int)$countRow['cnt'] : 0;

        return ['count' => $count, 'rows' => $rows];
    }


     public function getAllEchangesrehetre() {
        $sql = "SELECT 
                    e.id,
                    p1.nom AS produit1,
                    p2.nom AS produit2,
                    u1.username AS user1,
                    u2.username AS user2,
                    s.etat,
                    e.status_id,
                    e.date_envoie,
                    e.date_acceptation
                FROM echange e
                JOIN products p1 ON e.produit1_id = p1.id
                JOIN products p2 ON e.produit2_id = p2.id
                JOIN users u1 ON e.user1_id = u1.id
                JOIN users u2 ON e.user2_id = u2.id
                JOIN echange_status s ON e.status_id = s.id
                ORDER BY e.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEchangesByUser($user_id) {
        $sql = "SELECT 
                    e.id,
                    p1.nom AS produit1,
                    p2.nom AS produit2,
                    u1.username AS user1,
                    u2.username AS user2,
                    s.etat,
                    e.status_id,
                    e.date_envoie,
                    e.date_acceptation
                FROM echange e
                JOIN products p1 ON e.produit1_id = p1.id
                JOIN products p2 ON e.produit2_id = p2.id
                JOIN users u1 ON e.user1_id = u1.id
                JOIN users u2 ON e.user2_id = u2.id
                JOIN echange_status s ON e.status_id = s.id
                WHERE e.user1_id = :user_id 
                OR e.user2_id = :user_id
                ORDER BY e.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $user_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getEchangeById($id) {
        $sql = "SELECT e.*, s.etat 
                FROM echange e
                JOIN echange_status s ON e.status_id = s.id
                WHERE e.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

   public function addEchange($produit1_id, $produit2_id, $user1_id, $user2_id , $status_id) {

        $sql = "INSERT INTO echange 
                (produit1_id, produit2_id, user1_id, user2_id, status_id, date_envoie)
                VALUES 
                (:produit1_id, :produit2_id, :user1_id, :user2_id, :status_id, NOW())";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':produit1_id' => $produit1_id,
            ':produit2_id' => $produit2_id,
            ':user1_id'    => $user1_id,
            ':user2_id'    => $user2_id,
            ':status_id'   => $status_id
        ]);
    }

    public function updateEchangeStatus($id, $status_id) {
        $sql = "UPDATE echange 
                SET status_id = :status_id, 
                    date_acceptation = CASE WHEN :status_id = 3 THEN NOW() ELSE date_acceptation END
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':status_id', $status_id, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deleteEchange($id) {
        $sql = "DELETE FROM echange WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getEchangesByStatus($status_id) {
        $sql = "SELECT e.*, s.etat 
                FROM echange e
                JOIN echange_status s ON e.status_id = s.id
                WHERE e.status_id = :status_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':status_id', $status_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Mes demandes envoyées (je suis user1)
    public function getMesDemandesEnvoyees($user_id) {
        $sql = "SELECT 
                    e.id,
                    p1.nom AS produit1,
                    p2.nom AS produit2,
                    u1.username AS user1,
                    u2.username AS user2,
                    s.etat,
                    e.status_id,
                    e.date_envoie,
                    e.date_acceptation
                FROM echange e
                JOIN products p1 ON e.produit1_id = p1.id
                JOIN products p2 ON e.produit2_id = p2.id
                JOIN users u1 ON e.user1_id = u1.id
                JOIN users u2 ON e.user2_id = u2.id
                JOIN echange_status s ON e.status_id = s.id
                WHERE e.user1_id = :user_id
                ORDER BY e.id DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Demandes reçues (je suis user2)
    public function getDemandesRecues($user_id) {
        $sql = "SELECT 
                    e.id,
                    p1.nom AS produit1,
                    p2.nom AS produit2,
                    u1.username AS user1,
                    u2.username AS user2,
                    s.etat,
                    e.status_id,
                    e.date_envoie,
                    e.date_acceptation
                FROM echange e
                JOIN products p1 ON e.produit1_id = p1.id
                JOIN products p2 ON e.produit2_id = p2.id
                JOIN users u1 ON e.user1_id = u1.id
                JOIN users u2 ON e.user2_id = u2.id
                JOIN echange_status s ON e.status_id = s.id
                WHERE e.user2_id = :user_id
                ORDER BY e.id DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Filtrer mes échanges par statut
    public function getMesEchangesByStatus($user_id, $status_id) {
        $sql = "SELECT 
                    e.id,
                    p1.nom AS produit1,
                    p2.nom AS produit2,
                    u1.username AS user1,
                    u2.username AS user2,
                    s.etat,
                    e.status_id,
                    e.date_envoie,
                    e.date_acceptation
                FROM echange e
                JOIN products p1 ON e.produit1_id = p1.id
                JOIN products p2 ON e.produit2_id = p2.id
                JOIN users u1 ON e.user1_id = u1.id
                JOIN users u2 ON e.user2_id = u2.id
                JOIN echange_status s ON e.status_id = s.id
                WHERE (e.user1_id = :user_id OR e.user2_id = :user_id)
                AND e.status_id = :status_id
                ORDER BY e.id DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':user_id' => $user_id,
            ':status_id' => $status_id
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllEchangesUsers($user_id) {
        return $this->getEchangesByUser($user_id);
    }
    
    // Nombre total d'échanges
    public function getNombreEchanges(): int {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM echanges");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) ($result['total'] ?? 0);
    }

    public function getHistoriqueProduit(int $produitId): array {
        $sql = "SELECT 
                    e.id,
                    e.date_envoie,
                    e.date_acceptation,
                    e.status_id,
                    s.etat,
                    CASE WHEN e.produit1_id = :produitId THEN u1.username ELSE u2.username END AS ancien_proprietaire,
                    CASE WHEN e.produit1_id = :produitId THEN u2.username ELSE u1.username END AS nouveau_proprietaire
                FROM echange e
                JOIN users u1 ON e.user1_id = u1.id
                JOIN users u2 ON e.user2_id = u2.id
                JOIN echange_status s ON e.status_id = s.id
                WHERE (e.produit1_id = :produitId OR e.produit2_id = :produitId)
                  AND e.status_id = 3
                ORDER BY COALESCE(e.date_acceptation, e.date_envoie) ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':produitId', $produitId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Vérifier l'état d'un échange entre deux produits
    public function getEchangeEntreDeuxProduits($produit1_id, $produit2_id) {
        $sql = "SELECT e.*, s.etat 
                FROM echange e
                JOIN echange_status s ON e.status_id = s.id
                WHERE ((e.produit1_id = :produit1_id AND e.produit2_id = :produit2_id)
                   OR (e.produit1_id = :produit2_id AND e.produit2_id = :produit1_id))
                ORDER BY e.id DESC 
                LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':produit1_id' => $produit1_id,
            ':produit2_id' => $produit2_id
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

}