<?php 
namespace app\model; 
use Flight;
use PDO;

Class CategorieModel{
    private $db; 

    public function __construct($db){
        $this->db=$db; 
    }

    public function  getAllCategorie(){
        $sql = "SELECT * FROM categories"; 
        return $this->db->query($sql)->fetchAll();
    }

    public function getCategorieById($id){
        $sql = "SELECT * FROM categories WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addCategorie($nom, $icon){
        $sql = "INSERT INTO categories (nom, icon) VALUES (:nom, :icon)";
        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':icon', $icon);

        return $stmt->execute(); 
    }

    public function deleteCategorie($id){
        $sql = "DELETE FROM categories WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function updateCategorie($id, $nom, $icon){
        $sql = "UPDATE categories 
                SET nom = :nom, icon = :icon 
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':icon', $icon);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }
    
    
} 