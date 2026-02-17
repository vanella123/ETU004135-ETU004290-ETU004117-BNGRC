<?php

namespace app\controllers;

use app\model\DonModel;
use app\model\DispatchModel;
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

    public function addDon($article_id, $quantite, $donateur, $date_saisie){
        try {
            $id = $this->donModel->insertDon(
                $article_id,
                $quantite,
                $donateur,
                $date_saisie
            );

            // Dispatch automatique après chaque insertion de don
            $db = Flight::db();
            $dispatch = new DispatchModel($db);
            $dispatch->dispatchTousLesDons();

            return $id;

        } catch (Throwable $e){
            return false;
        }
    }
    public function repartirDonsParOdreCroissant()
    {
        $db = Flight::db();
        $don = new DonModel($db);
        $don->repartirDonsParOdreCroissanr();
    }
}



