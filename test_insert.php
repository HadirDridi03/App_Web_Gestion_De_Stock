<?php
require_once 'db_config.php';
header('Content-Type: text/plain');

try {
    $conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $testData = [
        'numero' => 'CMD-TEST'.rand(100,999),
        'client' => 'Client Test',
        'date' => date('Y-m-d'),
        'statut' => 'En attente'
    ];
    
    $stmt = $conn->prepare("INSERT INTO commandes (numero, client, date_commande, statut) 
                           VALUES (:numero, :client, :date, :statut)");
    
    if ($stmt->execute($testData)) {
        echo "SUCCÈS! ID: ".$conn->lastInsertId();
    } else {
        print_r($conn->errorInfo());
    }
} catch(PDOException $e) {
    echo "ÉCHEC: ".$e->getMessage()."\n";
    print_r($e->errorInfo);
}
?>