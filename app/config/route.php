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

Flight::route('/', function () {
    Flight::render('login'); 
});

Flight::route('GET /logout', function () {
    // Détruire la session
    $_SESSION = [];
    session_destroy();
    
    // Rediriger vers la page de login (racine de l'application)
    header('Location: /');
    exit();
});

Flight::route('GET /home', function () {
    Flight::render('home');
}); 
Flight::route('GET /register', function () {
    Flight::render('FormInscription');
});

Flight::route('POST /logForm', function() {

    $controller = new UserController();
    $result = $controller->validate(true);

    $req = Flight::request();
    $isAjax = ($req->ajax ?? false) 
        || (strtolower($req->headers['X-Requested-With'] ?? '') === 'xmlhttprequest');

    // 🔥 On remplit la session AVANT
    if ($result['ok']) {
        $_SESSION['user'] = [
            'username' => $result['values']['username'],
            'email' => $result['values']['email'],
            'id' => $controller->getUserIdByEmail($result['values']['email']),
        ];
    }

    if ($isAjax) {
        Flight::json($result);
        return;
    }

    if ($result['ok']) {
        Flight::redirect('/home');
    } else {
        Flight::render('login', [
            'errors' => $result['errors'] ?? [],
            'values' => $result['values'] ?? [],
        ]);
    }
});


Flight::route('POST /inscription', function() {
    $controller = new UserController();
    $result = $controller->register(true);

    $req = Flight::request();
    $isAjax = ($req->ajax ?? false) || (strtolower($req->headers['X-Requested-With'] ?? '') === 'xmlhttprequest');
    if($result['ok']){
           // Flight::json($result);
           // $controller->createUser($result['values']['username'], $result['values']['email'], $result['values']['password']);
            $_SESSION['user'] = [
                'username' => $result['values']['username'],
                'email' => $result['values']['email'],
                'id' => $controller->getUserIdByEmail($result['values']['email']),
            ];
           // Flight::render('/home'); 
        } 
        if ($isAjax) {
        Flight::json($result);
        return;
    } 

    if($result['ok']){
        Flight::json($result);
        $controller->createUser($result['values']['username'], $result['values']['email'], $result['values']['password']);
        $_SESSION['user'] = [
            'username' => $result['values']['username'],
            'email' => $result['values']['email'],
            'id' => $controller->getUserIdByEmail($result['values']['email']),
        ];
        // Flight::render('header',[
        //     'user' => $_SESSION['user']
        // ]);
        Flight::redirect('/home'); 
    } else { 
        Flight::json($result); 
        Flight::render('FormInscription',[
            'errors' => $result['errors'] ?? [],
            'values' => $result['values'] ?? [],
        ]); 
    } 
});
if(isset($_SESSION['user'])){


// Page catégories (HTML)
Flight::route('/categories', function () {
    $controller = new CategorieController();
    $result = $controller->getAllCategorie();
    Flight::render('categorie', ['categories' => $result]);
});

// API catégories (JSON)
Flight::route('GET /api/categories', function () {
    $controller = new CategorieController();
    Flight::json($controller->getAllCategorie());
});

// Ajouter une catégorie
Flight::route('POST /api/categories', function () {
    $data = Flight::request()->data;
    $controller = new CategorieController();

    $result = $controller->addCategorie(
        $data->nom ?? '',
        $data->icon ?? ''
    );

    Flight::json(['success' => (bool)$result]);
});

// Mettre à jour une catégorie
Flight::route('PUT /api/categories/@id', function ($id) {
    $data = Flight::request()->data;
    $controller = new CategorieController();

    $result = $controller->updateCategorie(
        (int)$id,
        $data->nom ?? '',
        $data->icon ?? ''
    );

    Flight::json(['success' => (bool)$result]);
});

// Supprimer une catégorie
Flight::route('DELETE /api/categories/@id', function ($id) {
    $controller = new CategorieController();
    $result = $controller->deleteCategorie((int)$id);
    Flight::json(['success' => (bool)$result]);
});

Flight::route('/produits', function () {
    $produitController = new ProduitController();
    $userController = new UserController();
    $categorieController = new CategorieController();

    $categories = $categorieController->getAllCategorie();
   // Flight::render('sidebar', [ 'liste' => $result ]);
    $produits = $produitController->listProduitsDisponibles();

    // Sécurité
    if (!isset($_SESSION['user'])) {
        Flight::render('login');
        return;
    } 

    // Ajouter user et categorie pour chaque produit
    foreach ($produits as &$produit) {
        $produit['user'] = $userController->getUserById($produit['user_id']);
        $produit['categorie'] = $categorieController->getCategorie($produit['categorie_id']);
    } 

    Flight::render('produits', [
        'produits' => $produits,
        'categories' => $categories
    ]);

});


// ============ detaille produit ================
Flight::route('/produit/@id', function ($id) {
    $produitController = new ProduitController();
    $userController = new UserController();
    $categorieController = new CategorieController();
    $echangeController = new EchangeController();

    $product_selected = $produitController->getProduitById($id);

    if (!$product_selected) {
        Flight::notFound();
        return;
    }

    // Ajouter infos user + catégorie
    $product_selected['user'] = $userController->getUserById($product_selected['user_id']);
    $product_selected['categorie'] = $categorieController->getCategorie($product_selected['categorie_id']);
    $historique = $echangeController->getHistoriqueProduit($id);
    
    // Récupérer les produits de l'utilisateur connecté pour proposer un échange
    $mesProduits = [];
    $db = Flight::db();
    $echangeModel = new \app\model\EchangeModel($db);
    
    if (isset($_SESSION['user']) && $_SESSION['user']['id'] != $product_selected['user_id']) {
        $mesProduits = $produitController->listProduitsUtilisateur();
        
        // Pour chaque produit, vérifier s'il existe un échange
        foreach ($mesProduits as &$monProduit) {
            $monProduit['categorie'] = $categorieController->getCategorie($monProduit['categorie_id']);
            $echange = $echangeModel->getEchangeEntreDeuxProduits($monProduit['id'], $id);
            $monProduit['echange'] = $echange;
        }
    }

    Flight::render('produit_detail', [
        'produit_detail' => $product_selected,
        'historique' => $historique,
        'mesProduits' => $mesProduits
    ]);
});

//================ produits par categorie ================
Flight::route('GET /produitCategories/@id', function ($id_categorie) {
    $produitController = new ProduitController();
    $categorieController = new CategorieController();
    $userController = new UserController();

    $categories = $categorieController->getAllCategorie();
    
    $resultat = $produitController->getProduitsByCategorie(
        $id_categorie,
        $produitController->listProduitsDisponibles()
    );

    // Ajouter infos user et catégorie pour chaque produit
    foreach ($resultat as &$produit) {
        $produit['user'] = $userController->getUserById($produit['user_id']);
        $produit['categorie'] = $categorieController->getCategorie($produit['categorie_id']);
    }

    Flight::render('produits', [
        'produits' => $resultat,
        'categories' => $categories
    ]);
});

Flight::route('GET /recherche', function () {
    if (!isset($_SESSION['user'])) {
        Flight::redirect('/');
        return;
    }

    $req = Flight::request();
    $motCle = trim($req->query['q'] ?? '');
    $categorieId = $req->query['categorie'] ?? null;
    $categorieId = is_numeric($categorieId) ? (int) $categorieId : null;

    $produitController = new ProduitController();
    $categorieController = new CategorieController();
    $userController = new UserController();

    $categories = $categorieController->getAllCategorie();
    $results = $produitController->searchProduits($motCle, $categorieId);

    foreach ($results as &$produit) {
        $produit['user'] = $userController->getUserById($produit['user_id']);
        $produit['categorie'] = $categorieController->getCategorie($produit['categorie_id']);
    }

    Flight::render('recherche', [
        'produits' => $results,
        'categories' => $categories,
        'motCle' => $motCle,
        'categorieActive' => $categorieId
    ]);
});
} else {
    Flight::route('/produits', function () {
        Flight::render('login');
    });
}

Flight::route('GET /mes-produits', function () {
    if (!isset($_SESSION['user'])) {
        Flight::redirect('/');
        return;
    }

    $produitController = new ProduitController();
    $categorieController = new CategorieController();
    $userController = new UserController();
    $db = Flight::db();
    $echangeModel = new \app\model\EchangeModel($db);

    $mesProduits = $produitController->listProduitsUtilisateur();

    foreach ($mesProduits as &$produit) {
        $produit['categorie'] = $categorieController->getCategorie($produit['categorie_id']);
        
        // Récupérer les demandes d'échange entrantes (où quelqu'un veut MON produit)
        // produit1_id = le produit demandé (le mien)
        // user1_id = celui qui envoie la demande (l'autre personne)
        $sql = "SELECT e.*, s.etat, u.username as autre_user
                FROM echange e
                JOIN echange_status s ON e.status_id = s.id
                JOIN users u ON e.user1_id = u.id
                WHERE e.produit1_id = :produit_id
                AND e.user2_id = :user_id
                AND e.status_id = 1
                ORDER BY e.date_envoie DESC";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':produit_id' => $produit['id'],
            ':user_id' => $_SESSION['user']['id']
        ]);
        $produit['echanges_attente'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    Flight::render('mes_produits', [
        'produits' => $mesProduits,
        'user' => $_SESSION['user']
    ]);
});

// Route pour afficher le formulaire d'ajout de produit
Flight::route('GET /nouveau-produit', function () {
    if (!isset($_SESSION['user'])) {
        Flight::redirect('/');
        return;
    }

    $categorieController = new CategorieController();
    $categories = $categorieController->getAllCategorie();

    Flight::render('ajouter_produit', [
        'categories' => $categories
    ]);
});

// Route pour traiter l'ajout de produit
Flight::route('POST /api/produits', function () {
    if (!isset($_SESSION['user'])) {
        Flight::json(['success' => false, 'message' => 'Non connecté']);
        return;
    }

    $data = Flight::request()->data;
    $files = Flight::request()->files;
    
    // Validation basique
    $errors = [];
    if (empty($data->nom)) {
        $errors[] = "Le nom du produit est requis";
    }
    if (empty($data->description)) {
        $errors[] = "La description est requise";
    }
    if (empty($data->prix) || $data->prix <= 0) {
        $errors[] = "Le prix doit être supérieur à 0";
    }
    if (empty($data->categorie_id)) {
        $errors[] = "La catégorie est requise";
    }

    if (!empty($errors)) {
        Flight::json(['success' => false, 'errors' => $errors]);
        return;
    }

    // Gestion de l'upload d'image
    $imageName = 'default.jpg';
    if (isset($files['image']) && $files['image']['error'] === 0) {
        $uploadDir = __DIR__ . '/../../public/images/';
        
        // Créer le dossier s'il n'existe pas
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $extension = pathinfo($files['image']['name'], PATHINFO_EXTENSION);
        $imageName = uniqid('produit_') . '.' . $extension;
        $uploadPath = $uploadDir . $imageName;
        
        if (move_uploaded_file($files['image']['tmp_name'], $uploadPath)) {
            // Image uploadée avec succès
        } else {
            $imageName = 'default.jpg';
        }
    }

    $produitController = new ProduitController();
    $result = $produitController->addProduit(
        $data->nom,
        $data->description,
        $data->prix,
        $data->categorie_id,
        $_SESSION['user']['id'],
        $imageName
    );

    if ($result) {
        Flight::json(['success' => true, 'message' => 'Produit ajouté avec succès']);
    } else {
        Flight::json(['success' => false, 'message' => 'Erreur lors de l\'ajout du produit']);
    }
});

Flight::route('GET /admin/statistiques', function () {
    if (!isset($_SESSION['user'])) {
        Flight::redirect('/');
        return;
    }

    $userController = new UserController();
    $echangeController = new EchangeController();

    $totalUsers = count($userController->getAllUser());
    $echanges = $echangeController->getAllEchanges();
    $totalEchanges = 0;

    if (is_array($echanges)) {
        if (isset($echanges['count'])) {
            $totalEchanges = (int) $echanges['count'];
        } elseif (isset($echanges['rows']) && is_array($echanges['rows'])) {
            $totalEchanges = count($echanges['rows']);
        } else {
            $totalEchanges = count($echanges);
        }
    }

    Flight::render('admin_statistiques', [
        'totalUsers' => $totalUsers,
        'totalEchanges' => $totalEchanges
    ]);
});

Flight::route('/echanges', function () {
    Flight::render('echanges');
});
// API JSON
Flight::route('GET /api/echanges', function () {
    $controller = new EchangeController();
    Flight::json($controller->getAllEchanges());

});

// Update status
Flight::route('POST /api/echanges/@id/status', function ($id) {
    $data = Flight::request()->data;
    $controller = new EchangeController();

    $success = $controller->updateEchangeStatus($id, $data->status_id);

    Flight::json(['success' => $success]);
});

//====== echange user connecter 

Flight::route('GET /echange_user/@user_id', function($user_id) {
    Flight::render('echange_user', ['user_id' => $user_id]);
});

//=== tous les echanges d'un user
Flight::route('GET /api/echanges/user/@user_id', function($user_id) {
    $controller = new EchangeController();
    Flight::json($controller->getAllEchangesUsers($user_id));
});

//=== mes demandes envoyées
Flight::route('GET /api/echanges/user/@user_id/envoyees', function($user_id) {
    $controller = new EchangeController();
    Flight::json($controller->getMesDemandesEnvoyees($user_id));
});
//=== mes demandes reçues
Flight::route('GET /api/echanges/user/@user_id/recues', function($user_id) {
    $controller = new EchangeController();
    Flight::json($controller->getDemandesRecues($user_id));
});

// ===== mes échanges filtrés par statut
Flight::route('GET /api/echanges/user/@user_id/status/@status_id', function($user_id, $status_id) {
    $controller = new EchangeController();
    Flight::json($controller->getMesEchangesByStatus($user_id, $status_id));
});

//3️⃣ Routes API pour les produits (pour ton formulaire)
// Tous les produits
Flight::route('GET /api/produits', function () {
    $controller = new ProduitController();
    Flight::json($controller->getAllProduits());
});

// ===== ajout de echange
Flight::route('POST /api/echanges', function () {
    $data = Flight::request()->data;
    $controller = new EchangeController();
    
    $result = $controller->addEchange(
        $data->produit1_id,
        $data->produit2_id,
        $data->user1_id,
        $data->user2_id,
        $data->status_id
    );
    
    Flight::json($result);
});

// ===== annuler/supprimer un échange
Flight::route('DELETE /api/echanges/@id', function ($id) {
    $controller = new EchangeController();
    $result = $controller->deleteEchange((int)$id);
    Flight::json($result);
});

//================= statistiques admin =================
Flight::route('GET /admin/statistiques', function() {
    session_start();
    if(!isset($_SESSION['user'])) {
        Flight::halt(403, 'Accès refusé');
    }
    $db = Flight::db(); // ton PDO ou la connexion Flight
    $controller = new EchangeController();

    Flight::json($controller->getStats());
});

//========= produit par pourcentage similarité ==============
Flight::route('/produit/@id/similaires/@pourcentage', function($id, $pourcentage) {

    $produitController = new ProduitController();
    $echangeController = new EchangeController();
    $db = Flight::db();
    $echangeModel = new \app\model\EchangeModel($db);

    $produitsSimilaires = $produitController
                            ->getProduitByPourcentage($id, $pourcentage);
   // var_dump($produitsSimilaires);
   foreach ($produitsSimilaires as &$produit) {
        $produit['difference_pourcentage'] =
            $produitController->getPriceDifference($id, $produit['id']);
        
        // Vérifier l'état de l'échange entre ces deux produits
        $echange = $echangeModel->getEchangeEntreDeuxProduits($id, $produit['id']);
        $produit['echange'] = $echange;
    }
    
    // Filtrer les produits qui ont un échange accepté (status_id = 3)
    $produitsSimilaires = array_filter($produitsSimilaires, function($produit) {
        return !$produit['echange'] || $produit['echange']['status_id'] != 3;
    });

    $_SESSION['produit_id'] = $id;
   // $produitsSimilaires['difference_pourcentage'] = $produitController->getPriceDifference($id, $produitsSimilaires['id']);
    Flight::render('produits-similaires', [
        'produits' => $produitsSimilaires
    ]);
}); 

// ============ Profil utilisateur ================
Flight::route('GET /profil/@id', function ($id) {
    if (!isset($_SESSION['user'])) {
        Flight::redirect('/');
        return;
    }

    $userController = new UserController();
    $produitController = new ProduitController();
    $categorieController = new CategorieController();
    $db = Flight::db();
    $produitModel = new \app\model\ProduitModel($db);

    // Récupérer les informations de l'utilisateur
    $user = $userController->getUserById($id);
    
    if (!$user) {
        Flight::notFound();
        return;
    }

    // Récupérer tous les produits de cet utilisateur
    $produits = $produitModel->getProduitsByUserId($id);

    // Ajouter les informations de catégorie
    foreach ($produits as &$produit) {
        $produit['categorie'] = $categorieController->getCategorie($produit['categorie_id']);
    }

    Flight::render('profile', [
        'user' => $user,
        'produits' => $produits
    ]);
});