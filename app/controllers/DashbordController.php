<?php

namespace app\controllers;

use app\model\DashboardModel;
use Flight;
use Throwable;

class DashbordController {

    public function getbord(){

        $db = Flight::db();
        $dashboard = new DashboardModel($db);

        try {

            return [
                "success" => true,
                "data" => $dashboard->getDashboardData()
            ];

        } catch (Throwable $e){

            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    // Option bonus : résumé global par ville
    public function resumeVille(){

        $db = Flight::db();
        $dashboard = new DashboardModel($db);

        try {

            return [
                "success" => true,
                "data" => $dashboard->getTotalParVille()
            ];

        } catch (Throwable $e){

            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    public function getTotals(){
        $db = Flight::db();
        $dashboard = new DashboardModel($db);

        try {
            return [ 'success' => true, 'data' => $dashboard->getTotals() ];
        } catch (Throwable $e) {
            return [ 'success' => false, 'message' => $e->getMessage() ];
        }
    }

    public function getDonsNonRepartis(){
        $db = Flight::db();
        $dashboard = new DashboardModel($db);

        try {
            return [ 'success' => true, 'data' => $dashboard->getDonsNonRepartis() ];
        } catch (Throwable $e) {
            return [ 'success' => false, 'message' => $e->getMessage() ];
        }
    }

    public function resetRepartitions(){
        $db = Flight::db();
        $dashboard = new DashboardModel($db);

        try {
            $dashboard->resetRepartitions();
            return [ 'success' => true, 'message' => 'Répartitions supprimées' ];
        } catch (Throwable $e) {
            return [ 'success' => false, 'message' => $e->getMessage() ];
        }
    }

    public function viewDashboard(){

    $db = Flight::db();
    $DashboardModel = new DashboardModel($db);

    $data = $DashboardModel->getDashboardData();
    $totals = $DashboardModel->getTotals();

    // Envoie les données à la vue
    Flight::render('dashboard', [
        'dashboard' => $data,
        'totals'    => $totals
    ]);
}
}