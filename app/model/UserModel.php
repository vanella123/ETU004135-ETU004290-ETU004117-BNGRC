<?php 
namespace app\model; 
use Flight;
use PDO;

Class UserModel{
    private $db; 
    public function __construct($db){
        $this->db=$db; 
    }

    public function getAllUser(){
        $sql = "SELECT * FROM users"; 
        return $this->db->query($sql)->fetchAll();
    }
    
    public function getUserById($id){
        $sql = "SELECT * FROM users where id =:id";
        $stmt=$this->db->prepare($sql); 
        $stmt->bindparam(':id', $id); 
        $stmt->execute(); 
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } 
    public function getUserIdByEmail($email){
        $sql = "SELECT id FROM users where email =:email";
        $stmt=$this->db->prepare($sql); 
        $stmt->bindparam(':email', $email); 
        $stmt->execute(); 
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['id'] : null;
    } 

    public function getUserByIdProduit($idProduit){
        $sql = "SELECT u.id, u.username, u.email FROM users u JOIN products p ON u.id = p.user_id WHERE p.id = :idProduit";
        $stmt=$this->db->prepare($sql); 
        $stmt->bindparam(':idProduit', $idProduit); 
        $stmt->execute(); 
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
            
    public function CheckUser($email, $password ){
        $sql = "SELECT * FROM users where email =:email and mdp=:password";
        $stmt = $this->db->prepare($sql); 
        $stmt->bindParam(':email', $email); 
        $stmt->bindParam(':password', $password); 
        $stmt->execute();
        return (bool) $stmt->fetch(PDO::FETCH_ASSOC);
    } 

    public function checkUsername($username){
        $sql = "SELECT * FROM users where username =:username";
        $stmt = $this->db->prepare($sql); 
        $stmt->bindParam(':username', $username); 
        $stmt->execute();
        return (bool) $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function validate_form($input , $pdo=null){
        $errors = [
            'username' =>'', 
            'email'=>'',
            'password'=>'',
            'connect'=>'',
        ] ; 
        $values = [
            'username' =>trim($input['username'] ?? '') ,
            'email'=> trim($input['email'] ?? '') ,
            'password'=> trim($input['password'] ?? '') ,
        ] ; 


        if (empty($values['username'])) {
            $errors['username'] = "Le nom est obligatoire.";
        }
        else if (mb_strlen($values['username']) < 2) {
            $errors['username'] = "Le nom doit contenir au moins 2 caractères.";
        }

        // if($this->checkUsername($values['username'])){
        //     $errors['username'] = "Ce nom d'utilisateur est déjà pris.";
        // } 

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
            if(!$this->CheckUser($values['email'], $values['password'])){
                $errors['email'] = "Utilisateur ou mot de passe incorrect ou vous etes pas inscrit.";
               // $errors['connect'] = "L'email n'existe pas ou le mot de passe est incorrect.";
            } 
        } 

        $ok = empty(array_filter($errors));
        return ['ok' => $ok, 'errors' => $errors, 'values' => $values];
    } 

    public function createUser($username, $email, $password){
        $sql = "INSERT INTO users (username, email, mdp) VALUES (:username, :email, :password)";
        $stmt = $this->db->prepare($sql); 
        $stmt->bindParam(':username', $username); 
        $stmt->bindParam(':email', $email); 
        $stmt->bindParam(':password', $password); 
        return $stmt->execute();
    }
    public function checkEmail($email) {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch() !== false;
    }

    
    function validate_registration_input($input, $pdo = null) {
    // $input = ['nom'=>..., 'prenom'=>..., 'email'=>..., 'password'=>..., 'confirm_password'=>..., 'telephone'=>...]
    $errors = [
        'username' => '',
        'email' => '',
        'password' => '',
        'confirm_password' => '',
    ];

    $values = [
        'username' => trim($input['username'] ?? ''),
        'email' => trim($input['email'] ?? ''),
    ];

    $password = $input['password'] ?? '';
    $confirm  = $input['confirm_password'] ?? '';

    // --- mêmes règles + mêmes messages ---
    
    if (empty($values['username'])) {
        $errors['username'] = "Le nom est obligatoire.";
    }
    else if (mb_strlen($values['username']) < 2) {
        $errors['username'] = "Le nom doit contenir au moins 2 caractères.";
    }
    else if ($this->checkUsername($values['username'])) {
        $errors['username'] = "Ce nom d'utilisateur est déjà pris.";
    }


    if ($values['email'] === '') {
        $errors['email'] = "L'email est obligatoire.";
    } elseif (!filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "L'email n'est pas valide (ex: nom@domaine.com).";
    }

     // Vérif email unique (version pro)
    if (!array_filter($errors)) {
        $userModel = new UserModel($this->db);
        if($this->checkEmail($values['email'])){
            $errors['email'] = "L'email est déjà utilisé ou vous etes deja inscrit.";
            // $errors['connect'] = "L'email n'existe pas ou le mot de passe est incorrect.";
        } 
    } 

    if (strlen($password) < 8) {
        $errors['password'] = "Le mot de passe doit contenir au moins 8 caractères.";
    }

    if (strlen($confirm) < 8) {
        $errors['confirm_password'] = "Veuillez confirmer le mot de passe (min 8 caractères).";
    } elseif ($password !== $confirm) {
        $errors['confirm_password'] = "Les mots de passe ne correspondent pas.";
        if ($errors['password'] === '') {
            $errors['password'] = "Vérifiez le mot de passe et sa confirmation.";
        } 
    } 

    // // Téléphone : 8–15 chiffres, uniquement chiffres
    // if (strlen($values['telephone']) < 8 || strlen($values['telephone']) > 15) {
    //     $errors['telephone'] = "Le téléphone doit contenir entre 8 et 15 chiffres.";
    // } elseif (!preg_match('/^[0-9]+$/', $values['telephone'])) {
    //     $errors['telephone'] = "Le téléphone ne doit contenir que des chiffres.";
    // } 

   
    $ok = true;
    // if (!array_filter($errors)) {
    //     // Tout est bon, on peut créer l'utilisateur
    //     $this->createUser($values['username'], $values['email'], $password);
    // } else {
    //     $ok = false;
    // } 
    
    $ok = empty(array_filter($errors));
    return ['ok' => $ok, 'errors' => $errors, 'values' => $values];
}

// Nombre total d'utilisateurs
    public function getNombreUtilisateurs(): int {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM users");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) ($result['total'] ?? 0);
    }

    
    
} 