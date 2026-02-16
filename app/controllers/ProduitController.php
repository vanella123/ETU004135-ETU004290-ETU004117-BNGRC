<?php
namespace app\controllers; 

use app\model\ProduitModel;
use Flight;
use Throwable;

class ProduitController {

    public function getAllProduits(){

        $db = Flight::db();
        $produit = new ProduitModel($db);
        $allproduits = $produit->getAllProduits();
        return $allproduits;
    }

    public function addProduit($nom, $type_besoin_id, $prix_unitaire){

        $db = Flight::db();
        $produit = new ProduitModel($db);

        $inserer = $produit->addProduit($nom, $type_besoin_id, $prix_unitaire);
        return $inserer ; 
    }

}