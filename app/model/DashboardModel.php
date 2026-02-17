<?php

namespace app\model;

use PDO;

class DashboardModel {

    private $db;

    public function __construct($db){
        $this->db = $db;
    }

    public function getDashboardData(){

        // D'abord mettre à jour les statuts en fonction de l'état des besoins
        $this->mettreAJourStatuts();

        $sql = "
            SELECT 
                v.id AS ville_id,
                v.nom AS ville,
                a.nom AS article,
                b.id AS besoin_id,
                b.quantite AS quantite_demandee,
                IFNULL(SUM(r.quantite_repartie), 0) AS quantite_attribuee,
                GREATEST(0, b.quantite - IFNULL(SUM(r.quantite_repartie), 0)) AS quantite_restante,
                s.libelle AS statut,
                s.id_statut
            FROM besoin b
            JOIN ville v ON b.ville_id = v.id
            JOIN article a ON b.article_id = a.id
            JOIN statut s ON b.statut_id = s.id_statut
            LEFT JOIN repartition_don r ON b.id = r.besoin_id
            GROUP BY b.id
            ORDER BY v.nom ASC, a.nom ASC
        ";

        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // Option bonus : total par ville
    public function getTotalParVille(){

        $sql = "
            SELECT 
                v.nom AS ville,
                SUM(b.quantite) AS total_demandee,
                IFNULL(SUM(r.quantite_repartie), 0) AS total_attribuee,
                GREATEST(0, SUM(b.quantite) - IFNULL(SUM(r.quantite_repartie), 0)) AS total_restante
            FROM besoin b
            JOIN ville v ON b.ville_id = v.id
            LEFT JOIN repartition_don r ON b.id = r.besoin_id
            GROUP BY v.id
            ORDER BY v.nom ASC
        ";

        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Met à jour les statuts des besoins en fonction de l'état de leurs attributions
     */
    private function mettreAJourStatuts(){
        
        // Statut 1 = En attente (pas de dons du tout)
        // Statut 2 = En cours (partiellement satisfait)
        // Statut 3 = Satisfait (totalement satisfait)
        
        $sql = "
            UPDATE besoin b 
            SET b.statut_id = (
                CASE 
                    WHEN (SELECT IFNULL(SUM(r.quantite_repartie), 0) 
                          FROM repartition_don r 
                          WHERE r.besoin_id = b.id) = 0 
                    THEN 1  -- En attente
                    
                    WHEN (SELECT IFNULL(SUM(r.quantite_repartie), 0) 
                          FROM repartition_don r 
                          WHERE r.besoin_id = b.id) >= b.quantite 
                    THEN 3  -- Satisfait
                    
                    ELSE 2  -- En cours
                END
            )
        ";
        
        return $this->db->exec($sql);
    }

    public function getTotals(){
        // met à jour les statuts avant calcul
        $this->mettreAJourStatuts();

        $sql = "
            SELECT
                COUNT(DISTINCT v.id) AS total_villes,
                COUNT(b.id) AS total_besoins,
                SUM(CASE WHEN b.statut_id = 3 THEN 1 ELSE 0 END) AS total_satisfaits,
                SUM(CASE WHEN b.statut_id = 2 THEN 1 ELSE 0 END) AS total_en_cours,
                SUM(CASE WHEN b.statut_id = 1 THEN 1 ELSE 0 END) AS total_en_attente,
                SUM(b.quantite) AS total_quantite_demandee,
                IFNULL(SUM(r.quantite_repartie), 0) AS total_quantite_attribuee,
                GREATEST(0, SUM(b.quantite) - IFNULL(SUM(r.quantite_repartie), 0)) AS total_quantite_restante
            FROM besoin b
            JOIN ville v ON b.ville_id = v.id
            LEFT JOIN repartition_don r ON b.id = r.besoin_id
        ";

        return $this->db->query($sql)->fetch(PDO::FETCH_ASSOC);
    }

    public function getDashboardDatas(){
        $sql = "SELECT * FROM vue_dashboard ORDER BY ville ASC, article ASC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retourne les dons qui ont encore de la quantité non répartie
     */
    public function getDonsNonRepartis(){
        $sql = "
            SELECT 
                d.id,
                a.nom AS article,
                d.donateur_nom AS donateur,
                d.quantite AS quantite_totale,
                IFNULL(SUM(r.quantite_repartie), 0) AS quantite_repartie,
                GREATEST(0, d.quantite - IFNULL(SUM(r.quantite_repartie), 0)) AS reste
            FROM don d
            JOIN article a ON d.article_id = a.id
            LEFT JOIN repartition_don r ON d.id = r.don_id
            GROUP BY d.id
            HAVING reste > 0
            ORDER BY d.date_saisie ASC
        ";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Compte le nombre total de dons dans la base
     */
    public function getTotalDons(){
        $sql = "SELECT COUNT(*) FROM don";
        return (int) $this->db->query($sql)->fetchColumn();
    }

    /**
     * Supprime toutes les répartitions et remet les statuts à "En attente"
     */
    public function resetRepartitions(){
        // 1) Supprimer toutes les lignes de repartition_don
        $this->db->exec("DELETE FROM repartition_don");

        // 2) Remettre tous les besoins en statut 1 (En attente)
        $this->db->exec("UPDATE besoin SET statut_id = 1");

        return true;
    }
}
