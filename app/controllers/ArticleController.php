<?php
namespace app\controllers;

use app\model\ArticleModel;
use PDO;

class ArticleController {
    private $articleModel;

    public function __construct(PDO $db) {
        $this->articleModel = new ArticleModel($db);
    }

    // ======================
    // Afficher tous les articles
    // ======================
    public function getAllArticles() {
        return $this->articleModel->getAllProduits();
    }
    public function getArticleById($id) {
        return $this->articleModel->getProduitById($id);
    }
    public function getIdByNomArticle($nom) {
        return $this->articleModel->getIdByNomArticle($nom);
    }
    public function addArticle($nom, $type_besoin_id, $prix_unitaire) {
        return $this->articleModel->addProduit($nom, $type_besoin_id, $prix_unitaire);
    }
    public function updateArticle($id, $nom, $type_besoin_id, $prix_unitaire) {
        return $this->articleModel->updateProduit($id, $nom, $type_besoin_id, $prix_unitaire);
    }
    public function deleteArticle($id) {
        return $this->articleModel->deleteProduit($id);
    }   
}
