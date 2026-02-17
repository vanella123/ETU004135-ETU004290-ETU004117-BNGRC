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

    // Page spécifique
    $content = 'SaisieBesoin.php';
    
    Flight::render('model.php', [
        'villes'   => $villeController->getAllVilles(),
        'produits' => $articleController->getAllArticles(),
        'feedback' => $feedback,
        'content'  => $content,
        'title'    => 'Saisie Besoin'
    ]);
});


Flight::route('GET /', function () {
    $controller = new DashbordController();
    $bord = $controller->getbord();
    $data = isset($bord['success']) && $bord['success'] ? $bord['data'] : [];

    // Récupère aussi les totaux et les passe à la vue
    $db = Flight::db();
    $dashboardModel = new \app\model\DashboardModel($db);
    $totals = $dashboardModel->getTotals();

    $content = 'dashbord.php';

    $donsNonRepartis = $dashboardModel->getDonsNonRepartis();

    Flight::render('model.php', [
        'dashboard' => $data,
        'totals'    => $totals,
        'donsNonRepartis' => $donsNonRepartis,
        'content'   => $content,
        'title'     => 'Tableau de Bord'
    ]);
});

// Recharger : supprime toutes les répartitions et remet tout à zéro
Flight::route('POST /recharger', function(){
    $db = Flight::db();
    $dashboardModel = new \app\model\DashboardModel($db);
    $dashboardModel->resetRepartitions();
    Flight::redirect('/');
});


// Simulation du dispatch (affichage dans le dashboard)
Flight::route('POST /simulate', function(){
    $controller = new DashbordController();
    $bord = $controller->getbord();
    $data = isset($bord['success']) && $bord['success'] ? $bord['data'] : [];
    
    // Ajouter la simulation
    $dispatchController = new DispatchController();
    $simulationResult = $dispatchController->simuler();

    // Récupère aussi les totaux pour l'affichage en simulation
    $db = Flight::db();
    $dashboardModel = new \app\model\DashboardModel($db);
    $totals = $dashboardModel->getTotals();
    
    $simForView = $simulationResult['success'] ? $simulationResult['simulation'] : null;

    // n'affiche le mode "simulation" que s'il y a au moins une répartition simulée réelle
    $showSimulation = false;
    if ($simForView && !empty($simForView['repartitions_simulees'])) {
        $showSimulation = true;
    }

    Flight::render('dashbord', [
        'dashboard' => $data,
        'totals'    => $totals,
        'donsNonRepartis' => $dashboardModel->getDonsNonRepartis(),
        'simulation' => $simForView,
        'showSimulation' => $showSimulation
    ]);
});

// Page de simulation (aperçu détaillé) — form POST -> /simulation/preview
Flight::route('GET /simulation', function(){
    Flight::render('simulation', ['simulation' => null, 'title' => 'Simulation']);
});

Flight::route('POST /simulation/preview', function(){
    $dispatchController = new DispatchController();
    $simulationResult = $dispatchController->simuler();

    // `simulation.php` attend une liste de dons -> fournir `dons` si présent
    $simulationForView = [];
    if ($simulationResult['success']) {
        $sim = $simulationResult['simulation'];
        $simulationForView = $sim['dons'] ?? $sim;
    }

    Flight::render('simulation', [
        'simulation' => $simulationForView,
        'title' => 'Simulation — Aperçu'
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
    $donController = new DonController();
    $articleController = new ArticleController($db);

    $message = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $donateur = trim($_POST['donateur'] ?? '');
        $article_id = $_POST['article_id'] ?? null;
        $quantite = $_POST['quantite'] ?? null;
        $date_saisie = $_POST['date_saisie'] ?? null;

        if (!$donateur || !$article_id || !$quantite || !$date_saisie) {
            $message = ['type' => 'danger', 'text' => 'Tous les champs sont obligatoires.'];
        } else {
            $result = $donController->addDon($article_id, $quantite, $donateur, $date_saisie);
            $message = $result ? ['type'=>'success','text'=>'Don ajouté avec succès !'] : ['type'=>'danger','text'=>'Erreur lors de l\'ajout du don.'];
        }
    }

    $articles = $articleController->getAllArticles();
    $dons = $donController->getAllDon();

    $content = 'form_dons.php';

    Flight::render('model.php', compact('articles','dons','message','content') + ['title' => 'Saisie Don']);
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


Flight::route('GET|POST /achats', function(){
    $db = Flight::db();
    $achatController = new \app\controllers\AchatController();
    $articleController = new \app\controllers\ArticleController($db);
    $villeController = new \app\controllers\VilleController($db);

    $message = null;

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $ville_id = $_POST['ville_id'] ?? null;
        $article_id = $_POST['article_id'] ?? null;
        $quantite = $_POST['quantite'] ?? null;
        $frais = $_POST['frais'] ?? 10;
        $date = date('Y-m-d');

        if(!$ville_id || !$article_id || !$quantite){
            $message = ['type'=>'danger','text'=>'Tous les champs sont obligatoires'];
        } else {
            $res = $achatController->addAchat(
                $article_id,
                $ville_id,
                $quantite,
                $articleController->getArticleById($article_id)['prix_unitaire'],
                $frais,
                $date
            );
            $message = $res['success'] ? ['type'=>'success','text'=>$res['message']] : ['type'=>'danger','text'=>$res['message']];
        }
    }

    $articles = $articleController->getAllArticlesToBuy();
    $villes = $villeController->getAllVilles();

    $ville_filter = $_GET['ville_filter'] ?? null;
    $achats = $achatController->getAchats($ville_filter);

    $content = 'achats.php';

    Flight::render('model.php', compact('articles','villes','achats','message','content') + ['title'=>'Achats']);
});
