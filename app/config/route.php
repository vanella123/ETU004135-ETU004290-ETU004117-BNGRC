<?php 
namespace app\config;
use flight\Engine;
use app\controllers\BesoinController;
use app\controllers\ProduitController;
use app\controllers\VilleController;

use flight\net\Router;
use Flight; 
/** 
 * @var Router $router 
 * @var Engine $app 
 */
session_start();
// Route pour afficher le formulaire de saisie
Flight::route('/saisieBesoin', function() {
    // On récupère toutes les villes
    $controllerVille = new VilleController();
    $controllerProduit = new ProduitController();
    $villes =$controllerVille->getAllVille(); // retourne un tableau ex: [['id'=>1,'nom'=>'Antananarivo'],...]

    // On récupère tous les produits
    $produits =$controllerProduit->getAllProduit(); // retourne un tableau ex: [['id'=>1,'nom'=>'Riz'], ...]
    Flight::render('SaisieBesoin', ['villes' => $villes, 'produits' => $produits]);
});

