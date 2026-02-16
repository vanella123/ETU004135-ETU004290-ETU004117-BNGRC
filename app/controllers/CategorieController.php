<?php 

namespace app\controllers; 

use app\model\CategorieModel;
use Flight;
use Throwable;

class CategorieController {

    public function getAllCategorie(){
        $db = Flight::db();
        $categorie = new CategorieModel($db);

        $allCategories = $categorie->getAllCategorie();

        return $allCategories;
    }

    public function getCategorie($id){
        $db = Flight::db();
        $categorie = new CategorieModel($db);
        return $categorie->getCategorieById($id);
    }
 

    public function addCategorie($nom, $icon){
        $db = Flight::db();
        $categorie = new CategorieModel($db);
        return $categorie->addCategorie($nom, $icon);
    }

    public function deleteCategorie($id){
        $db = Flight::db();
        $categorie = new CategorieModel($db);
        return $categorie->deleteCategorie($id);
    }

    public function updateCategorie($id, $nom, $icon){
        $db = Flight::db();
        $categorie = new CategorieModel($db);
        return $categorie->updateCategorie($id, $nom, $icon);
    }
}
