<?php
namespace app\controllers; 

class DonController {
    private $donModel;
    private $articleModel;

    public function __construct($donModel, $articleModel) {
        $this->donModel = $donModel;
        $this->articleModel = $articleModel;
    }

    // Retourne tous les dons pour le tableau
    public function getDons() {
        return $this->donModel->getAllDons();
    }

    // Insert un don
    public function addDon($article_id, $quantite, $donateur, $date_saisie) {
        return $this->donModel->insertDon($article_id, $quantite, $donateur, $date_saisie);
    }
}
?>
