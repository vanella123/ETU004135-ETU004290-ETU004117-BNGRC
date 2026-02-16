<?php 
namespace app\services; 

use app\model\UserModel;
use Flight;
use PDO;

Class UserService{
    private $db; 
    public function __construct($db){
        $this->db=$db; 
    }
    public function validate_form($input , $pdo=null){
        $errors = [
            'nom' =>'', 
            'prenom'=> '',
            'email'=>'',
            'password'=>'',
        ] ; 
        $values = [
            'nom' =>trim($input['nom'] ?? '') ,
            'prenom'=> trim($input['prenom'] ?? '') ,
            'email'=> trim($input['email'] ?? '') ,
            'password'=> trim($input['password'] ?? '') ,
        ] ; 


        if (mb_strlen($values['nom']) < 2) {
            $errors['nom'] = "Le nom doit contenir au moins 2 caractères.";
        } 

        if (mb_strlen($values['prenom']) < 2) {
            $errors['prenom'] = "Le prénom doit contenir au moins 2 caractères.";
        }

        if ($values['email'] === '') {
            $errors['email'] = "L'email est obligatoire.";
        } elseif (!filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "L'email n'est pas valide (ex: nom@domaine.com).";
        }

        if (strlen($values['password']) < 8) {
            $errors['password'] = "Le mot de passe doit contenir au moins 8 caractères.";
        } 

        if (!array_filter($errors)) {
            $userModel = new UserModel($this->db);
            if(!$userModel->CheckUser($values['email'], $values['password'])){
                $errors['email'] = "Utilisateur ou mot de passe incorrect.";
            }
        }

        $ok = empty(array_filter($errors));
        return ['ok' => $ok, 'errors' => $errors, 'values' => $values];
    }   
}
?>