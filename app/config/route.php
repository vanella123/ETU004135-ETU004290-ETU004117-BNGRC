<?php 
namespace app\config;
use flight\Engine;
use app\controllers\BesoinController;
use app\controllers\DonController;
    use app\controllers\DispatchController;
    use app\controllers\VilleController;
    use app\controllers\DashbordController;
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
    $db = Flight::db();
    $villeController = new VilleController($db);
    $produitController = new ArticleController($db);

    Flight::render('SaisieBesoin', [
        'villes'   => $villeController->getAllVilles(),
        'produits' => $produitController->getAllArticles(),
    ]);
});

Flight::route('/', function(){

    $controller = new DashbordController();
    $bord = $controller->getbord();
    $data = isset($bord['success']) && $bord['success'] ? $bord['data'] : [];
    Flight::render('dashbord', ['dashboard' => $data]);

});

// Bouton Dispatch : lance le dispatch global puis redirige vers le dashboard
Flight::route('POST /dispatch', function(){
    $controller = new DispatchController();
    $controller->dispatchAll();
    Flight::redirect('/');
});




