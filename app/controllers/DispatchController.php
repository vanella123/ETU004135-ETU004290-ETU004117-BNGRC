<?php 

namespace app\controllers;

use app\model\DispatchModel;
use Flight;
use Throwable;

class DispatchController {

    /**
     * Dispatcher TOUS les dons non encore répartis (par ordre chronologique)
     */
    public function dispatchAll(){

        $db = Flight::db();
        $dispatch = new DispatchModel($db);

        try {

            $resultats = $dispatch->dispatchTousLesDons();

            return [
                "success" => true,
                "message" => "Dispatch global effectué avec succès",
                "dons_traites" => count($resultats),
                "details" => $resultats
            ];

        } catch (Throwable $e) {

            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    /**
     * Dispatcher un don spécifique
     */
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