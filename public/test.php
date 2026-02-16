<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=echange_db', 'root', '');
    echo "PDO MySQL fonctionne !";
} catch (PDOException $e) {
    echo "Erreur PDO : " . $e->getMessage();
}
