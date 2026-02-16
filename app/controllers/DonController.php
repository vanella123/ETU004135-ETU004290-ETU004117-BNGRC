<?php

namespace app\controllers;

use app\models\DonModel;
use Flight;
use Throwable;

class DonController {

    public function getAllDon(){

        $db = Flight::db();
        $don = new DonModel($db);

        return $don->getAllDons();
    }

    public function addDon(){

        $db = Flight::db();
        $don = new DonModel($db);

        try {

            $data = Flight::request()->data;

            $id = $don->insertDon(
                $data->article_id,
                $data->quantite,
                $data->date_saisie
            );

            return [
                "success" => true,
                "message" => "Don ajouté avec succès",
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