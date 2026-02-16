<?php

namespace app\controllers;

use app\models\DonModel;
use Flight;
use Throwable;

class DonController {
    private $donModel;

    public function __construct() {
        $db = Flight::db();
        $this->donModel = new DonModel($db);
    }

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