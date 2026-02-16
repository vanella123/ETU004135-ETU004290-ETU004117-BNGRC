<?php 
namespace app\config;
use flight\Engine;
use app\controllers\BesoinController;
use app\controllers\DonController;
use app\controllers\DispatchController;
use app\controllers\VilleController;
use app\controllers\DashbordController;

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
    $villes =$controllerVille->getAllVille(); // retourne un tableau ex: [['id'=>1,'nom'=>'Antananarivo'],...]

    // TODO: Adapter pour utiliser les nouveaux contrôleurs (Besoin/Don au lieu de Produit)
    Flight::render('SaisieBesoin', ['villes' => $villes]);
});

Flight::route('GET /dashboard', function(){

    $controller = new DashboardController();
    $controller->viewDashboard();

});

Flight::route('GET /dashboard/view', function(){

    $controller = new DashboardController();
    $controller->viewDashboard();

});


