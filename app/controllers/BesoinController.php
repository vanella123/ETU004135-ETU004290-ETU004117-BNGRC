<?php

namespace app\controllers;

use app\models\BesoinModel;
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

            $data = Flight::request()->data;

            $id = $besoin->insertBesoin(
                $data->ville_id,
                $data->article_id,
                $data->quantite,
                $data->date_saisie
            );

            return [
                "success" => true,
                "message" => "Besoin ajouté avec succès",
                "id" => $id
            ];

        } catch (Throwable $e){

            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }
}