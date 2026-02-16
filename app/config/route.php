<?php 
namespace app\config;
use flight\Engine;
use app\controllers\BesoinController;
use app\controllers\DonController;
use app\controllers\DispatchController;
use app\controllers\ArticleController;
use app\controllers\VilleController;
use app\controllers\DashbordController;

use flight\net\Router;
use Flight; 
/** 
 * @var Router $router 
 * @var Engine $app 
 */
session_start();
Flight::route('GET /saisieBesoin', function () {
    $db = Flight::db();
    $villeController = new VilleController($db);
    $produitController = new ArticleController($db);

    $feedback = $_SESSION['besoin_feedback'] ?? null;
    unset($_SESSION['besoin_feedback']);

    Flight::render('saisie', [
        'villes'   => $villeController->getAllVilles(),
        'produits' => $produitController->getAllArticles(),
        'feedback' => $feedback,
    ]);
});

Flight::route('POST /saisieBesoin', function () {
    $controller = new BesoinController();
    $result = $controller->addBesoin();

    $_SESSION['besoin_feedback'] = $result;

    $baseUrl = Flight::get('flight.base_url');
    Flight::redirect($baseUrl . '/saisieBesoin');
});

Flight::route('/',function(){
    $controller = new DashbordController();
    $bord = $controller->getbord();
    Flight::render('dashbord', ['dashboard' => $bord]);
});

Flight::route('GET /test', function () {
    echo "Route test OK !";
});

Flight::route('POST /saisie', function () {
    $controller = new BesoinController();
    $result = $controller->addBesoin();
    Flight::json($result);
});



