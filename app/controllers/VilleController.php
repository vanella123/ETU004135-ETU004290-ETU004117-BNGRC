<?php
namespace app\controllers;

use app\model\VilleModel;
use Flight;
use Throwable;

class VilleController
{
    public function getAllVilles()
    {
        $db = Flight::db();
        $villeModel = new VilleModel($db);

        return $villeModel->getAllVille();
    }

    public function getVilleById(int $id)
    {
        $db = Flight::db();
        $villeModel = new VilleModel($db);

        return $villeModel->getVilleById($id);
    }

    public function addVille(string $nom)
    {
        $db = Flight::db();
        $villeModel = new VilleModel($db);

        return $villeModel->addVille($nom);
    }

    public function updateVille(int $id, string $nom)
    {
        $db = Flight::db();
        $villeModel = new VilleModel($db);

        return $villeModel->updateVille($id, $nom);
    }

    public function deleteVille(int $id)
    {
        $db = Flight::db();
        $villeModel = new VilleModel($db);

        return $villeModel->deleteVille($id);
    }
}
