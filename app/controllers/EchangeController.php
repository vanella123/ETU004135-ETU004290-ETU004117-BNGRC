<?php
namespace app\controllers; 

use app\model\EchangeModel;
use app\model\UserModel;
use Flight;
use Throwable;

class EchangeController {

    public function getAllEchanges(){
        $db = Flight::db();
        $echange = new EchangeModel($db);
        $allechanges = $echange->getAllEchanges();
        return $allechanges;
    }

    public function getAllEchangesUsers($iduser){
        $db = Flight::db();
        $echange = new EchangeModel($db);
        $allechanges = $echange->getEchangesByUser($iduser);
        return $allechanges;
    }

    public function addEchange($produit1_id, $produit2_id, $user1_id, $user2_id , $status_id) {

        $db = Flight::db();
        $echange = new EchangeModel($db);

        $inserer = $echange->addEchange(
            $produit1_id,
            $produit2_id,
            $user1_id,
            $user2_id,
            $status_id
        );

        return ['success' => $inserer];
    }

    public function updateEchangeStatus($id , $status_id) {
        
        $db = Flight::db();
        $echange = new EchangeModel($db);
        $success = $echange->updateEchangeStatus($id, $status_id);

        return ['success' => $success];
    }


    public function deleteEchange($id) {
        $db = Flight::db();
        $echange = new EchangeModel($db);
        $success = $echange->deleteEchange($id);
        return ['success' => $success];
    }

    public function getEchangesByStatus($status_id) {
        $db = Flight::db();
        $echange = new EchangeModel($db);
        $success = $echange->getEchangesByStatus($status_id);
        return $success ;
    }

    public function getMesDemandesEnvoyees($user_id) {
        $db = Flight::db();
        $echange = new EchangeModel($db);
        return $echange->getMesDemandesEnvoyees($user_id);
    }

    public function getDemandesRecues($user_id) {
        $db = Flight::db();
        $echange = new EchangeModel($db);
        return $echange->getDemandesRecues($user_id);
    }

    public function getMesEchangesByStatus($user_id, $status_id) {
        $db = Flight::db();
        $echange = new EchangeModel($db);
        return $echange->getMesEchangesByStatus($user_id, $status_id);
    }

    public function getHistoriqueProduit($produitId) {
        $db = Flight::db();
        $echange = new EchangeModel($db);
        return $echange->getHistoriqueProduit((int) $produitId);
    }

    public function getStats(): array {
        return [
            'nombre_utilisateurs' => (new UserModel(Flight::db()))->getNombreUtilisateurs(),
            'nombre_echanges'    => (new EchangeModel(Flight::db()))->getNombreEchanges(),
        ];
    }


}