<?php

namespace app\model;

use PDO;
use Exception;

class DispatchModel {

    private $db;

    public function __construct($db){
        $this->db = $db;
    }

    /**
     * Dispatch TOUS les dons qui ont encore un reste non distribué,
     * par ordre chronologique (date_saisie ASC).
     */
    public function dispatchTousLesDons(){

        $donsNonRepartis = $this->getDonsAvecReste();
        $resultats = [];

        foreach ($donsNonRepartis as $don) {
            $resultats[] = [
                'don_id'   => $don['id'],
                'article'  => $don['article_id'],
                'quantite' => $don['reste'],
                'resultat' => $this->executerDispatch($don['id'])
            ];
        }

        return $resultats;
    }

    /**
     * Récupère tous les dons qui ont encore de la quantité non distribuée,
     * triés par date de saisie (les plus anciens d'abord).
     */
    private function getDonsAvecReste(){

        $sql = "
            SELECT d.id, d.article_id,
                   (d.quantite - IFNULL(SUM(r.quantite_repartie), 0)) AS reste
            FROM don d
            LEFT JOIN repartition_don r ON d.id = r.don_id
            GROUP BY d.id
            HAVING reste > 0
            ORDER BY d.date_saisie ASC
        ";

        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function executerDispatch($don_id){

        try {

            $this->db->beginTransaction();

            // 1️⃣ Récupérer le don
            $stmt = $this->db->prepare("SELECT * FROM don WHERE id = ?");
            $stmt->execute([$don_id]);
            $don = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$don) {
                throw new Exception("Don introuvable");
            }

            $resteDon = $this->getResteDon($don_id);

            if ($resteDon <= 0) {
                $this->db->commit();
                return "Don déjà totalement distribué";
            }

            $besoins = $this->getBesoinsNonSatisfaits($don['article_id']);

            foreach ($besoins as $besoin) {

                if ($resteDon <= 0) break;

                $quantiteARepartir = min($resteDon, $besoin['reste']);

                $insert = $this->db->prepare("
                    INSERT INTO repartition_don
                    (don_id, besoin_id, quantite_repartie, date_repartition)
                    VALUES (?, ?, ?, NOW())
                ");

                $insert->execute([
                    $don_id,
                    $besoin['id'],
                    $quantiteARepartir
                ]);

                $resteDon -= $quantiteARepartir;
            }

            $this->db->commit();

            return "Dispatch terminé";

        } catch (Exception $e) {

            $this->db->rollBack();
            throw $e;
        }
    }

    private function getResteDon($don_id){

        $sql = "
            SELECT d.quantite -
            IFNULL(SUM(r.quantite_repartie),0) AS reste
            FROM don d
            LEFT JOIN repartition_don r ON d.id = r.don_id
            WHERE d.id = ?
            GROUP BY d.id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$don_id]);

        return $stmt->fetchColumn();
    }

    private function getBesoinsNonSatisfaits($article_id){

        $sql = "
            SELECT b.*,
            (b.quantite - IFNULL(SUM(r.quantite_repartie),0)) AS reste
            FROM besoin b
            LEFT JOIN repartition_don r ON b.id = r.besoin_id
            WHERE b.article_id = ?
            GROUP BY b.id
            HAVING reste > 0
            ORDER BY b.date_saisie ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$article_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}