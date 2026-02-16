<?php 
namespace app\config;
use flight\Engine;
use app\controllers\BesoinController;
use app\controllers\DonController;
use app\controllers\DispatchController;
use app\controllers\VilleController;
use app\controllers\ArticleController;

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

