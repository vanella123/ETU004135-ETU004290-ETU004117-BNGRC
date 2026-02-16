<?php 

namespace app\controllers; 

use app\model\BesoinModel;
use Flight;
use Throwable;

class BesoinController {

    public function getAllBesoin(){
        $db = Flight::db();
        $besoin = new BesoinModel($db);

        $allBesoin = $besoin->getAllbesoins();

        return $allBesoin;
    }

    public function getBesoin($id){
        $db = Flight::db();
        $besoin = new BesoinModel($db);
        return $besoin->getBesoinById($id);
    }
 

    public function addBesoin($nom, $icon){
        $db = Flight::db();
        $besoin = new BesoinModel($db);
        return $besoin->addBesoin($nom, $icon);
    }

    public function deleteBesoin($id){
        $db = Flight::db();
        $besoin = new BesoinModel($db);
        return $besoin->deleteBesoin($id);
    }

    public function updateBesoin($id, $nom, $icon){
        $db = Flight::db();
        $besoin = new BesoinModel($db);
        return $besoin->updateBesoin($id, $nom, $icon);
    }
}
