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
    $villes =$controllerVille->getAllVilles(); // retourne un tableau ex: [['id'=>1,'nom'=>'Antananarivo'],...]

    // TODO: Adapter pour utiliser les nouveaux contrôleurs (Besoin/Don au lieu de Produit)
    Flight::render('SaisieBesoin', ['villes' => $villes]);
});

Flight::route('/', function(){
    $controller = new DashbordController();
    $bord = $controller->getbord();
    
    // Vérifier si la récupération a réussi
    if ($bord['success']) {
        Flight::render('dashbord', ['dashboard' => $bord['data']]);
    } else {
        // Gérer l'erreur
        echo "Erreur : " . $bord['message'];
    }
});

// =====================================================================
// API REST - Don (avec dispatch automatique)
// =====================================================================

// Lister tous les dons
Flight::route('GET /api/don', function(){
    $controller = new DonController();
    $data = $controller->getAllDon();
    Flight::json($data);
});

// Ajouter un don (dispatch automatique inclus)
Flight::route('POST /api/don', function(){
    $controller = new DonController();
    $result = $controller->addDon();
    Flight::json($result);
});

// =====================================================================
// API REST - Besoin
// =====================================================================

// Lister tous les besoins
Flight::route('GET /api/besoin', function(){
    $controller = new BesoinController();
    $data = $controller->getAllBesoin();
    Flight::json($data);
});

// Ajouter un besoin
Flight::route('POST /api/besoin', function(){
    $controller = new BesoinController();
    $result = $controller->addBesoin();
    Flight::json($result);
});

// =====================================================================
// API REST - Dashboard
// =====================================================================

// Dashboard complet
Flight::route('GET /api/dashboard', function(){
    $controller = new DashbordController();
    $result = $controller->getbord();
    Flight::json($result);
});

// Résumé par ville
Flight::route('GET /api/dashboard/villes', function(){
    $controller = new DashbordController();
    $result = $controller->resumeVille();
    Flight::json($result);
});

// =====================================================================
// API REST - Dispatch
// =====================================================================

// Dispatch GLOBAL : répartir tous les dons non encore distribués
Flight::route('POST /api/dispatch', function(){
    $controller = new DispatchController();
    $result = $controller->dispatchAll();
    Flight::json($result);
});

// Dispatch d'un don spécifique
Flight::route('POST /api/dispatch/@don_id', function($don_id){
    $controller = new DispatchController();
    $result = $controller->executerDispatch($don_id);
    Flight::json($result);
});

// =====================================================================
// API REST - Ville
// =====================================================================

Flight::route('GET /api/ville', function(){
    $controller = new VilleController();
    $data = $controller->getAllVilles();
    Flight::json($data);
});



