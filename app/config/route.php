<?php 
namespace app\config;
use flight\Engine;
use app\controllers\BesoinController;
use app\controllers\DonController;
    use app\controllers\DispatchController;
    use app\controllers\ProduitController;
    use app\controllers\VilleController;
    use app\controllers\DashbordController;
    use app\controllers\ArticleController;
use app\controllers\ArticleController;

use flight\net\Router;
use Flight; 
/** 
 * @var Router $router 
 * @var Engine $app 
 */
session_start();
// Route pour afficher le formulaire de saisie
Flight::route('/saisieBesoin', function () {
    $villeController = new VilleController();
    $produitController = new ProduitController();

    Flight::render('SaisieBesoin', [
        'villes'   => $villeController->getAllVilles(),
        'produits' => $produitController->getAllProduits(),
    ]);
});

Flight::route('/',function(){
    $controller = new DashbordController();
    $bord = $controller->getbord();
    Flight::render('dashbord', ['dashboard' => $bord]);
});




