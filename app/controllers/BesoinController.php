<?php

namespace app\controllers;

use app\model\BesoinModel;
use Flight;
use Throwable;

class BesoinController {

    public function getAllBesoin(){

        $db = Flight::db();
        $besoin = new BesoinModel($db);

        return $besoin->getAllBesoins();
    }

    public function addBesoin(){

        $db = Flight::db();
        $besoin = new BesoinModel($db);

        try {

            $data = Flight::request()->data; // support form submissions & JSON payloads

            $villeId = $data->ville_id ;
            $articleId = $data->article_id ;
            $quantite = $data->quantite ;
            $dateSaisie = $data->date_saisie ;

            if (empty($villeId) || empty($articleId) || empty($quantite) || empty($dateSaisie)) {
                return [
                    "success" => false,
                    "message" => "Champs requis manquants pour l'ajout du besoin."
                ];
            }

            $result = $besoin->addBesoin($villeId, $articleId, $quantite, $dateSaisie);

            return [
                "success" => $result,
                "message" => $result ? "Besoin ajouté avec succès" : "Échec de l'enregistrement du besoin."
            ];

        } catch (Throwable $e){

            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }
    public function getRecapBesoin(){
        $db = Flight::db();
        $besoin = new BesoinModel($db);
        return $besoin->getRecapBesoin();
    }
}