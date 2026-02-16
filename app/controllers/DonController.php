<?php

namespace app\controllers;

use app\model\DonModel;
use app\model\DispatchModel;
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
        $dispatch = new DispatchModel($db);

        try {

            $data = Flight::request()->data;

            // Insérer le don
            $id = $don->insertDon(
                $data->article_id,
                $data->quantite,
                $data->date_saisie,
                $data->donateur_nom ?? null
            );

            // Dispatcher TOUS les dons non encore répartis (y compris celui-ci)
            $dispatch->dispatchTousLesDons();

            return [
                "success" => true,
                "message" => "Don ajouté et dispatch de tous les dons effectué avec succès",
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