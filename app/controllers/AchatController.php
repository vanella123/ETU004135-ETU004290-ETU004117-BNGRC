<?php
namespace app\controllers;

use app\model\AchatModel;
use app\model\DonModel;
use Flight;
use Throwable;

class AchatController {
    private $achatModel;
    private $donModel;

    public function __construct(){
        $db = Flight::db();
        $this->achatModel = new AchatModel($db);
        $this->donModel = new DonModel($db);
    }

    // Ajouter un achat
    public function addAchat($article_id, $ville_id, $quantite, $prix_unitaire, $frais_pourcentage, $date_achat){
        try {
            // Calcul montant total
            $montant_base = $quantite * $prix_unitaire;
            $montant_total = $montant_base + ($montant_base * $frais_pourcentage / 100);

            // Vérifier argent disponible
            $total_argent = $this->donModel->getTotalArgentDisponible();
            $total_achats = $this->donModel->getTotalAchatsEffectues();
            $reste = $total_argent - $total_achats;

            if($montant_total > $reste){
                return [
                    'success' => false,
                    'message' => "Fonds insuffisants pour effectuer cet achat. Argent restant : $reste, achat demandé : $montant_total"
                ];
            }

            $id = $this->achatModel->insertAchat(
                $article_id,
                $ville_id,
                $quantite,
                $prix_unitaire,
                $frais_pourcentage,
                $montant_total,
                $date_achat
            );

            return [
                'success' => true,
                'message' => 'Achat effectué avec succès !',
                'id' => $id
            ];

        } catch(Throwable $e){
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    // Récupérer achats (option ville)
    public function getAchats($ville_id = null){
        if($ville_id){
            return $this->achatModel->getAchatsByVille($ville_id);
        }
        return $this->achatModel->getAllAchats();
    }
}
