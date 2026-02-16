<?php

namespace app\controllers;

use app\models\DashboardModel;
use Flight;
use Throwable;

class DashboardController {

    public function index(){

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

    public function viewDashboard(){

    $db = Flight::db();
    $dashboardModel = new DashboardModel($db);

    $data = $dashboardModel->getDashboardData();

    // Envoie les données à la vue
    Flight::render('dashboard', [
        'dashboard' => $data
    ]);
}
}