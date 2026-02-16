<?php 
namespace app\config;
use flight\Engine;
use app\controllers\UserController;
use app\controllers\MessageController;
use app\controllers\ProduitController;
use app\controllers\CategorieController;
use app\controllers\EchangeController;

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
    $villes = getAllVille(); // retourne un tableau ex: [['id'=>1,'nom'=>'Antananarivo'],...]

    // On récupère tous les produits
    $produits = getAllProduit(); // retourne un tableau ex: [['id'=>1,'nom'=>'Riz'], ...]
    Fligh

});

