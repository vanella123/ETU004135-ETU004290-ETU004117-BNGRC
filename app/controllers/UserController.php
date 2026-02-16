<?php 

namespace app\controllers; 

use app\model\UserModel;
use app\services\UserService;
use Flight;
use Throwable;

Class UserController{
    
    public function getAllUser(){
        $db = Flight::db(); 

        $user = new UserModel($db);
        $AllUser = $user->getAllUser(); 
        return $AllUser ; 
    } 

    public function getUserByEmail($id){
        $db = Flight::db(); 
        $user = new UserModel($db) ; 
        $UserByEmail = $user->getUserById($id) ; 
        return $UserByEmail ; 
    } 

    public function getUserById($id){
        $db = Flight::db(); 
        $user = new UserModel($db) ; 
        $UserById = $user->getUserById($id) ; 
        return $UserById ; 
    }
    public function CheckUser($email, $password){
        $db = Flight::db(); 

        $user = new UserModel($db) ; 
        $CheckUser = $user->CheckUser($email, $password) ; 
        return $CheckUser ; 
 
    }
    public function checkUsername($username){
        $db = Flight::db(); 

        $user = new UserModel($db) ; 
        $checkUsername = $user->checkUsername($username) ; 
        return $checkUsername ; 
 
    }

    public function CheckUserByEmail($email){
        $db = Flight::db(); 

        $user = new UserModel($db) ; 
        $CheckUserByEmail = $user->checkEmail($email) ; 
        return $CheckUserByEmail ; 
 
    }

    public function createUser($username, $email, $password){
        $db = Flight::db(); 

        $user = new UserModel($db) ; 
        $CreateUser = $user->createUser($username, $email, $password) ; 
        return $CreateUser ; 
 
    }
    public function getUserIdByEmail($email){
        $db = Flight::db(); 

        $user = new UserModel($db) ; 
        $getUserIdByEmail = $user->getUserIdByEmail($email) ; 
        return $getUserIdByEmail ; 
 
    }
    public function getUserByIdProduit($idProduit){
        $db = Flight::db(); 

        $user = new UserModel($db) ; 
        $getUserByIdProduit = $user->getUserByIdProduit($idProduit) ; 
        return $getUserByIdProduit ; 
 
    }

    public function validate_form($input , $pdo=null){
        $user = new UserModel(Flight::db());
        return $user->validate_form($input, $pdo);
    }

    public function validate_registration_form($input, $pdo = null) {
        $user = new UserModel(Flight::db());
        return $user->validate_registration_input($input, $pdo);
    }

    public function validate(bool $returnArray = false)
    {
        $db = Flight::db();
        // 405 si pas POST 
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            $payload = ['ok' => false, 'errors' => ['_global' => 'Méthode non autorisée.'], 'values' => []];
            if ($returnArray) {
                return $payload;
            } 
            Flight::json($payload);
            return;
        }   
        try { 
            $input = [ 
                'username' => trim($_POST['username'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'password' => $_POST['password'] ?? '',
            ];

            // Appel du service
            $result = $this->validate_form($input, $db);
            if ($returnArray) {
                return $result;
            }

            Flight::json([
                'ok' => $result['ok'],
                'errors' => $result['errors'],
                'values' => $result['values'],
            ]);
            return;
        } catch (Throwable $e) {
            http_response_code(500);
            $payload = [
                'ok' => false,
                'errors' => ['_global' => 'Erreur serveur lors de la validation.', 'detail' => $e->getMessage()],
                'values' => []
            ];

            if ($returnArray) {
                return $payload;
            }

            Flight::json($payload);
            return;
        } 
    } 

    public function register(bool $returnArray = false)
    {
        $db = Flight::db();
        // 405 si pas POST 
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            Flight::json(['ok' => false, 'errors' => ['_global' => 'Méthode non autorisée.'], 'values' => []]);
            return;
        }   
        try { 
            $input = [ 
                'username' => trim($_POST['username'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'password' => $_POST['password'] ?? '',
                'confirm_password' => $_POST['confirm_password'] ?? '',
            ];

            // Appel du service
            $result = $this->validate_registration_form($input, $db);
            if($returnArray) { return $result; }    
            Flight::json([
                'ok' => $result['ok'],
                'errors' => $result['errors'],
                'values' => $result['values'],
            ]);
            return;
        } catch (Throwable $e) {
            http_response_code(500);
            Flight::json([
                'ok' => false,
                'errors' => ['_global' => 'Erreur serveur lors de la validation.', 'detail' => $e->getMessage()],
                'values' => []
            ]);
            return;
        } 
    }
}  
