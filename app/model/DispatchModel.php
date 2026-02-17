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
        $donsSimules = [];

        // Copie locale des restes de besoins pour simulation successive
        // (évite la double allocation quand plusieurs dons couvrent le même article)
        $restesBesoins = [];
        foreach ($tousLesBesoins as $b) {
            $restesBesoins[$b['id']] = max(0, $b['quantite_restante']);
        }
        
        foreach ($donsNonRepartis as $don) {
            $resteDon = $don['reste'];
            $besoins = $this->getBesoinsNonSatisfaits($don['article_id']);
            $repartitions = [];

            foreach ($besoins as $besoin) {
                if ($resteDon <= 0) break;

                // Utiliser le reste simulé au lieu du reste DB
                $resteSimule = $restesBesoins[$besoin['id']] ?? $besoin['reste'];
                if ($resteSimule <= 0) continue;

                $quantiteARepartir = min($resteDon, $resteSimule);

                $repartitions[] = [
                    'besoin_id' => $besoin['id'],
                    'ville' => $besoin['ville'] ?? 'N/A',
                    'quantite' => $quantiteARepartir
                ];

                $resteDon -= $quantiteARepartir;
                // Mettre à jour le reste simulé
                $restesBesoins[$besoin['id']] = max(0, $resteSimule - $quantiteARepartir);
            }

            // conserver la structure attendue par la vue `simulation.php`
            $donsSimules[] = [
                'don_id' => $don['id'],
                'article_id' => $don['article_id'],
                'reste' => $don['reste'],
                'repartitions' => $repartitions
            ];

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
            'repartitions_simulees' => $repartitionsSimulees,
            'dons' => $donsSimules
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
                   GREATEST(0, d.quantite - IFNULL(SUM(r.quantite_repartie), 0)) AS reste
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
                GREATEST(0, b.quantite - IFNULL(SUM(r.quantite_repartie), 0)) AS quantite_restante,
                s.libelle AS statut_actuel
            FROM besoin b
            JOIN ville v ON b.ville_id = v.id
            JOIN article a ON b.article_id = a.id
            LEFT JOIN statut s ON b.statut_id = s.id_statut
            LEFT JOIN repartition_don r ON b.id = r.besoin_id
            GROUP BY b.id
            ORDER BY v.nom ASC, a.nom ASC, b.date_saisie ASC, b.ordre ASC, b.id ASC
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
            SELECT GREATEST(0, d.quantite -
            IFNULL(SUM(r.quantite_repartie),0)) AS reste
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
            GREATEST(0, b.quantite - IFNULL(SUM(r.quantite_repartie),0)) AS reste
            FROM besoin b
            LEFT JOIN repartition_don r ON b.id = r.besoin_id
            LEFT JOIN ville v ON b.ville_id = v.id
            WHERE b.article_id = ?
            GROUP BY b.id
            HAVING reste > 0
            ORDER BY b.date_saisie ASC, b.ordre ASC, b.id ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$article_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ========== DISPATCH PROPORTIONNEL ==========

    /**
     * Dispatch proportionnel de TOUS les dons non répartis.
     * Chaque don est réparti proportionnellement aux besoins restants du même article.
     */
    public function dispatchProportionnel(){
        $donsNonRepartis = $this->getDonsAvecReste();
        $resultats = [];

        foreach ($donsNonRepartis as $don) {
            $resultats[] = [
                'don_id'   => $don['id'],
                'article'  => $don['article_id'],
                'quantite' => $don['reste'],
                'resultat' => $this->executerDispatchProportionnel($don['id'])
            ];
        }

        return $resultats;
    }

    /**
     * Exécute le dispatch proportionnel d'un don spécifique.
     */
    public function executerDispatchProportionnel($don_id){
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("SELECT * FROM don WHERE id = ?");
            $stmt->execute([$don_id]);
            $don = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$don) throw new Exception("Don introuvable");

            $resteDon = $this->getResteDon($don_id);
            if ($resteDon <= 0) {
                $this->db->commit();
                return "Don déjà totalement distribué";
            }

            $besoins = $this->getBesoinsNonSatisfaits($don['article_id']);
            if (empty($besoins)) {
                $this->db->commit();
                return "Aucun besoin non satisfait pour cet article";
            }

            // Calcul du total des restes de besoins
            $totalBesoins = 0;
            foreach ($besoins as $b) {
                $totalBesoins += $b['reste'];
            }
            if ($totalBesoins == 0) {
                $this->db->commit();
                return "Total des besoins restants = 0";
            }

            // Calcul proportionnel (troncature sans arrondi)
            $repartitions = [];
            $totalDistribue = 0;
            foreach ($besoins as $index => $b) {
                $partExacte = ($b['reste'] / $totalBesoins) * $resteDon;
                $part = floor($partExacte);
                $repartitions[] = [
                    'besoin_id' => $b['id'],
                    'quantite' => $part,
                    'decimale' => $partExacte - $part  // partie après la virgule
                ];
                $totalDistribue += $part;
            }

            // Distribuer le reste à ceux qui ont la plus grande partie décimale
            $reste = $resteDon - $totalDistribue;
            if ($reste > 0) {
                // Trier par partie décimale décroissante
                usort($repartitions, function($a, $b) {
                    return $b['decimale'] <=> $a['decimale'];
                });
                for ($i = 0; $i < $reste && $i < count($repartitions); $i++) {
                    $repartitions[$i]['quantite'] += 1;
                }
            }

            // Insertion en base
            $insert = $this->db->prepare("
                INSERT INTO repartition_don (don_id, besoin_id, quantite_repartie, date_repartition)
                VALUES (?, ?, ?, NOW())
            ");
            foreach ($repartitions as $rep) {
                if ($rep['quantite'] > 0) {
                    $insert->execute([$don_id, $rep['besoin_id'], $rep['quantite']]);
                }
            }

            $this->db->commit();
            return "Dispatch proportionnel terminé";

        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * SIMULE le dispatch proportionnel sans modifier la base.
     */
    public function simulerDispatchProportionnel(){
        $tousLesBesoins = $this->getTousLesBesoins();
        $donsNonRepartis = $this->getDonsAvecReste();
        $repartitionsSimulees = [];
        $donsSimules = [];

        // Copie locale des restes de besoins pour simulation successive
        $restesBesoins = [];
        foreach ($tousLesBesoins as $b) {
            $restesBesoins[$b['id']] = $b['quantite_restante'];
        }

        foreach ($donsNonRepartis as $don) {
            $resteDon = $don['reste'];
            $besoins = $this->getBesoinsNonSatisfaits($don['article_id']);

            // Recalculer avec restes simulés
            $besoinsActifs = [];
            foreach ($besoins as $b) {
                $resteSimule = $restesBesoins[$b['id']] ?? $b['reste'];
                if ($resteSimule > 0) {
                    $b['reste_simule'] = $resteSimule;
                    $besoinsActifs[] = $b;
                }
            }

            $totalBesoins = 0;
            foreach ($besoinsActifs as $b) $totalBesoins += $b['reste_simule'];

            $repartitionsDon = [];
            if ($totalBesoins > 0) {
                // Calcul proportionnel (troncature sans arrondi)
                $totalDistribue = 0;
                foreach ($besoinsActifs as $b) {
                    $partExacte = ($b['reste_simule'] / $totalBesoins) * $resteDon;
                    $part = floor($partExacte);
                    $repartitionsDon[] = [
                        'besoin_id' => $b['id'],
                        'ville' => $b['ville'] ?? 'N/A',
                        'quantite' => $part,
                        'decimale' => $partExacte - $part  // partie après la virgule
                    ];
                    $totalDistribue += $part;
                }

                // Distribuer le reste à ceux qui ont la plus grande partie décimale
                $reste = $resteDon - $totalDistribue;
                if ($reste > 0) {
                    usort($repartitionsDon, function($a, $b) {
                        return $b['decimale'] <=> $a['decimale'];
                    });
                    for ($i = 0; $i < $reste && $i < count($repartitionsDon); $i++) {
                        $repartitionsDon[$i]['quantite'] += 1;
                    }
                }

                // Mettre à jour les restes simulés
                foreach ($repartitionsDon as $rep) {
                    $restesBesoins[$rep['besoin_id']] = max(0, ($restesBesoins[$rep['besoin_id']] ?? 0) - $rep['quantite']);
                }
            }

            $donsSimules[] = [
                'don_id' => $don['id'],
                'article_id' => $don['article_id'],
                'reste' => $don['reste'],
                'repartitions' => $repartitionsDon
            ];

            foreach ($repartitionsDon as $rep) {
                $repartitionsSimulees[] = [
                    'don_id' => $don['id'],
                    'besoin_id' => $rep['besoin_id'],
                    'quantite' => $rep['quantite'],
                    'ville' => $rep['ville']
                ];
            }
        }

        // Calculer l'état final pour chaque besoin
        foreach ($tousLesBesoins as &$besoin) {
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
            'repartitions_simulees' => $repartitionsSimulees,
            'dons' => $donsSimules
        ];
    }

    // ========== DISPATCH PAR ORDRE CROISSANT ==========

    /**
     * Dispatch en priorisant les plus petits besoins d'abord (ordre croissant)
     */
    public function dispatchOrdreCroissant(){
        $donsNonRepartis = $this->getDonsAvecReste();
        $resultats = [];

        // Exécution directe (pas don par don, mais globalement)
        try {
            $this->db->beginTransaction();

            // 1️⃣ Besoins non satisfaits (tri croissant par reste)
            $besoins = $this->db->query("
                SELECT 
                    b.id,
                    b.article_id,
                    b.quantite,
                    COALESCE(SUM(r.quantite_repartie),0) AS deja_repartie,
                    (b.quantite - COALESCE(SUM(r.quantite_repartie),0)) AS reste
                FROM besoin b
                LEFT JOIN repartition_don r ON b.id = r.besoin_id
                GROUP BY b.id
                HAVING reste > 0
                ORDER BY reste ASC
            ")->fetchAll(PDO::FETCH_ASSOC);

            // 2️⃣ Dons non totalement affectés
            $dons = $this->db->query("
                SELECT 
                    d.id,
                    d.article_id,
                    d.quantite,
                    COALESCE(SUM(r.quantite_repartie),0) AS deja_repartie,
                    (d.quantite - COALESCE(SUM(r.quantite_repartie),0)) AS reste
                FROM don d
                LEFT JOIN repartition_don r ON d.id = r.don_id
                GROUP BY d.id
                HAVING reste > 0
                ORDER BY d.date_saisie ASC
            ")->fetchAll(PDO::FETCH_ASSOC);

            foreach ($besoins as $besoin) {
                $resteBesoin = $besoin['reste'];

                foreach ($dons as &$don) {
                    if ($don['article_id'] != $besoin['article_id']) continue;
                    if ($don['reste'] <= 0) continue;
                    if ($resteBesoin <= 0) break;

                    $quantite = min($resteBesoin, $don['reste']);

                    $stmt = $this->db->prepare("
                        INSERT INTO repartition_don
                        (don_id, besoin_id, quantite_repartie, date_repartition)
                        VALUES (?, ?, ?, NOW())
                    ");
                    $stmt->execute([$don['id'], $besoin['id'], $quantite]);

                    $don['reste'] -= $quantite;
                    $resteBesoin -= $quantite;
                }
            }

            $this->db->commit();
            return ['success' => true, 'message' => 'Dispatch par ordre croissant terminé'];
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * SIMULE le dispatch par ordre croissant des besoins
     */
    public function simulerDispatchOrdreCroissant(){
        $tousLesBesoins = $this->getTousLesBesoins();
        $repartitionsSimulees = [];
        $donsSimules = [];

        // 1️⃣ Besoins triés par reste croissant
        $besoins = $this->db->query("
            SELECT 
                b.id,
                b.article_id,
                v.nom AS ville,
                GREATEST(0, b.quantite - COALESCE(SUM(r.quantite_repartie),0)) AS reste
            FROM besoin b
            LEFT JOIN repartition_don r ON b.id = r.besoin_id
            LEFT JOIN ville v ON b.ville_id = v.id
            GROUP BY b.id
            HAVING reste > 0
            ORDER BY reste ASC
        ")->fetchAll(PDO::FETCH_ASSOC);

        // 2️⃣ Dons avec reste
        $dons = $this->getDonsAvecReste();
        
        // Copie locale pour simulation
        $donsCopie = [];
        foreach ($dons as $d) {
            $donsCopie[] = [
                'id' => $d['id'],
                'article_id' => $d['article_id'],
                'reste_simule' => $d['reste']
            ];
        }

        foreach ($besoins as $besoin) {
            $resteBesoin = $besoin['reste'];
            $repartitionsBesoin = [];

            foreach ($donsCopie as &$don) {
                if ($don['article_id'] != $besoin['article_id']) continue;
                if ($don['reste_simule'] <= 0) continue;
                if ($resteBesoin <= 0) break;

                $quantite = min($resteBesoin, $don['reste_simule']);

                $repartitionsBesoin[] = [
                    'don_id' => $don['id'],
                    'besoin_id' => $besoin['id'],
                    'quantite' => $quantite,
                    'ville' => $besoin['ville']
                ];

                $don['reste_simule'] -= $quantite;
                $resteBesoin -= $quantite;
            }

            $repartitionsSimulees = array_merge($repartitionsSimulees, $repartitionsBesoin);
        }

        // Construire les dons simulés
        foreach ($dons as $d) {
            $repartitionsDon = [];
            foreach ($repartitionsSimulees as $r) {
                if ($r['don_id'] == $d['id']) {
                    $repartitionsDon[] = [
                        'besoin_id' => $r['besoin_id'],
                        'ville' => $r['ville'],
                        'quantite' => $r['quantite']
                    ];
                }
            }

            $donsSimules[] = [
                'don_id' => $d['id'],
                'article_id' => $d['article_id'],
                'reste' => $d['reste'],
                'repartitions' => $repartitionsDon
            ];
        }

        // Calculer l'état final pour chaque besoin
        foreach ($tousLesBesoins as &$besoin) {
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
            'repartitions_simulees' => $repartitionsSimulees,
            'dons' => $donsSimules
        ];
    }
}