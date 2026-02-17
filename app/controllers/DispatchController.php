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

    /**
     * Simuler le dispatch sans l'exécuter réellement (par date — chronologique)
     */
    public function simuler(){

        $db = Flight::db();
        $dispatch = new DispatchModel($db);

        try {

            $simulation = $dispatch->simulerDispatch();

            return [
                "success" => true,
                "message" => "Simulation effectuée avec succès",
                "simulation" => $simulation
            ];

        } catch (Throwable $e) {

            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    /**
     * Simuler le dispatch proportionnel sans l'exécuter
     */
    public function simulerProportionnel(){

        $db = Flight::db();
        $dispatch = new DispatchModel($db);

        try {
            $simulation = $dispatch->simulerDispatchProportionnel();

            return [
                "success" => true,
                "message" => "Simulation proportionnelle effectuée avec succès",
                "simulation" => $simulation
            ];
        } catch (Throwable $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    /**
     * Dispatcher proportionnellement TOUS les dons non répartis
     */
    public function dispatchProportionnel(){

        $db = Flight::db();
        $dispatch = new DispatchModel($db);

        try {
            $resultats = $dispatch->dispatchProportionnel();

            return [
                "success" => true,
                "message" => "Dispatch proportionnel effectué avec succès",
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
     * Simuler le dispatch par ordre croissant des besoins
     */
    public function simulerOrdreCroissant(){

        $db = Flight::db();
        $dispatch = new DispatchModel($db);

        try {
            $simulation = $dispatch->simulerDispatchOrdreCroissant();

            return [
                "success" => true,
                "message" => "Simulation par ordre croissant effectuée avec succès",
                "simulation" => $simulation
            ];
        } catch (Throwable $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    /**
     * Dispatcher par ordre croissant des besoins (plus petits d'abord)
     */
    public function dispatchOrdreCroissant(){

        $db = Flight::db();
        $dispatch = new DispatchModel($db);

        try {
            $resultat = $dispatch->dispatchOrdreCroissant();

            return [
                "success" => true,
                "message" => "Dispatch par ordre croissant effectué avec succès",
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