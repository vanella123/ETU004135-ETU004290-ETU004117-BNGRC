<?php 

namespace app\controllers;

use app\models\DispatchModel;
use Flight;
use Throwable;

class DispatchController {

    public function executerDispatch($don_id){

        $db = Flight::db();
        $dispatch = new DispatchModel($db);

        try {

            $resultat = $dispatch->executerDispatch($don_id);

            return [
                "success" => true,
                "message" => "Dispatch effectué avec succès",
                "data" => $resultat
            ];

        } catch (Throwable $e) {

            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }
}