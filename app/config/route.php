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
    $villeController = new VilleController();
    $articleController = new ArticleController($db);

    $feedback = $_SESSION['besoin_feedback'] ?? null;
    unset($_SESSION['besoin_feedback']);

    Flight::render('SaisieBesoin', [
        'villes'   => $villeController->getAllVilles(),
        'produits' => $articleController->getAllArticles(),
        'feedback' => $feedback,
    ]);
});

Flight::route('GET /', function () {
    $controller = new DashbordController();
    $bord = $controller->getbord();
    $data = isset($bord['success']) && $bord['success'] ? $bord['data'] : [];
    Flight::render('dashbord', ['dashboard' => $data]);
});

// Simulation du dispatch (affichage dans le dashboard)
Flight::route('POST /simulate', function(){
    $controller = new DashbordController();
    $bord = $controller->getbord();
    $data = isset($bord['success']) && $bord['success'] ? $bord['data'] : [];
    
    // Ajouter la simulation
    $dispatchController = new DispatchController();
    $simulationResult = $dispatchController->simuler();
    
    Flight::render('dashbord', [
        'dashboard' => $data,
        'simulation' => $simulationResult['success'] ? $simulationResult['simulation'] : null,
        'showSimulation' => true
    ]);
});

// Bouton Dispatch : lance le dispatch global puis redirige vers le dashboard
Flight::route('POST /dispatch', function(){
    $controller = new DispatchController();
    $controller->dispatchAll();
    Flight::redirect('/');
});


// Route pour afficher et traiter le formulaire de dons
Flight::route('GET|POST /form_dons', function() {

    $db = Flight::db();
    $donController = new \app\controllers\DonController();
    $articleController = new \app\controllers\ArticleController($db);

    $message = null;

    // Traitement du formulaire
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $donateur = trim($_POST['donateur'] ?? '');
        $article_id = $_POST['article_id'] ?? null;
        $quantite = $_POST['quantite'] ?? null;
        $date_saisie = $_POST['date_saisie'] ?? null;

        if (!$donateur || !$article_id || !$quantite || !$date_saisie) {
            $message = [
                'type' => 'danger',
                'text' => 'Tous les champs sont obligatoires.'
            ];
        } else {
            $result = $donController->addDon($article_id, $quantite, $donateur, $date_saisie);
            if ($result) {
                $message = [
                    'type' => 'success',
                    'text' => 'Don ajouté avec succès !'
                ];
            } else {
                $message = [
                    'type' => 'danger',
                    'text' => 'Erreur lors de l\'ajout du don.'
                ];
            }
        }
    }

    // Récupération des articles pour le dropdown
    $articles = $articleController->getAllArticles();

    // Récupération des dons pour le tableau
    $dons = $donController->getAllDon();

    Flight::render('form_dons', [
        'articles' => $articles,
        'dons' => $dons,
        'message' => $message
    ]);
});

Flight::route('POST /saisie', function () {

    $controller = new BesoinController();
    $result = $controller->addBesoin();

    $_SESSION['besoin_feedback'] = [
        'success' => $result['success'],
        'message' => $result['message']
    ];

    // 🔥 TRÈS IMPORTANT : redirection
    Flight::redirect('/saisieBesoin');
});
Flight::route('GET /recap',function(){
    Flight::render('recap_besoin'); 
});

Flight::route('GET /resumeBesoinsAjax', function () {
    $controller = new BesoinController();
    $result = $controller->getRecapBesoin();
    Flight::json($result);  // ✅ juste les données
}); 



