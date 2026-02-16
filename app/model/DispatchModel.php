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
     * SIMULE le dispatch de tous les dons sans exécuter les INSERT en base.
     * Retourne un aperçu complet : tous les besoins + répartitions simulées.
     */
    public function simulerDispatch(){

        // 1) Récupérer tous les besoins actuels
        $tousLesBesoins = $this->getTousLesBesoins();
        
        // 2) Simuler les répartitions
        $donsNonRepartis = $this->getDonsAvecReste();
        $repartitionsSimulees = [];
        
        foreach ($donsNonRepartis as $don) {
            $repartitions = $this->simulerDispatchDon($don);
            if (!empty($repartitions)) {
                foreach ($repartitions as $rep) {
                    $repartitionsSimulees[] = [
                        'don_id' => $don['id'],
                        'besoin_id' => $rep['besoin_id'],
                        'quantite' => $rep['quantite'],
                        'ville' => $rep['ville']
                    ];
                }
            }
        }
        
        // 3) Calculer l'état final pour chaque besoin
        foreach ($tousLesBesoins as &$besoin) {
            // Quantité qui sera ajoutée par la simulation
            $ajoutSimule = 0;
            foreach ($repartitionsSimulees as $rep) {
                if ($rep['besoin_id'] == $besoin['id']) {
                    $ajoutSimule += $rep['quantite'];
                }
            }
            
            $besoin['ajout_simule'] = $ajoutSimule;
            $besoin['nouveau_attribue'] = $besoin['quantite_attribuee'] + $ajoutSimule;
            $besoin['nouveau_reste'] = max(0, $besoin['quantite_restante'] - $ajoutSimule);
        }
        
        return [
            'besoins' => $tousLesBesoins,
            'repartitions_simulees' => $repartitionsSimulees
        ];
    }

    /**
     * Simule le dispatch d'un don spécifique sans INSERT.
     */
    private function simulerDispatchDon($don){
        
        $resteDon = $don['reste'];
        $besoins = $this->getBesoinsNonSatisfaits($don['article_id']);
        $repartitions = [];

        foreach ($besoins as $besoin) {
            if ($resteDon <= 0) break;

            $quantiteARepartir = min($resteDon, $besoin['reste']);

            $repartitions[] = [
                'besoin_id' => $besoin['id'],
                'ville' => $besoin['ville'] ?? 'N/A',
                'quantite' => $quantiteARepartir
            ];

            $resteDon -= $quantiteARepartir;
        }

        return $repartitions;
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

    /**
     * Récupère TOUS les besoins avec leur état actuel (satisfait ou non)
     */
    private function getTousLesBesoins(){
        
        $sql = "
            SELECT 
                b.id,
                b.quantite AS quantite_demandee,
                v.nom AS ville,
                a.nom AS article,
                IFNULL(SUM(r.quantite_repartie), 0) AS quantite_attribuee,
                (b.quantite - IFNULL(SUM(r.quantite_repartie), 0)) AS quantite_restante,
                s.libelle AS statut_actuel
            FROM besoin b
            JOIN ville v ON b.ville_id = v.id
            JOIN article a ON b.article_id = a.id
            LEFT JOIN statut s ON b.statut_id = s.id_statut
            LEFT JOIN repartition_don r ON b.id = r.besoin_id
            GROUP BY b.id
            ORDER BY v.nom ASC, a.nom ASC, b.id ASC
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
            SELECT b.*, v.nom as ville,
            (b.quantite - IFNULL(SUM(r.quantite_repartie),0)) AS reste
            FROM besoin b
            LEFT JOIN repartition_don r ON b.id = r.besoin_id
            LEFT JOIN ville v ON b.ville_id = v.id
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