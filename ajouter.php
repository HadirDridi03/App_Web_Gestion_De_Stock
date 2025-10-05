<?php
header('Content-Type: application/json');

require_once 'db_config.php';

try {
    $conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupération et validation des données
    $nom = isset($_POST['nom']) ? trim($_POST['nom']) : '';
    $fournisseur = isset($_POST['fournisseur']) ? trim($_POST['fournisseur']) : '';
    $quantite = isset($_POST['quantite']) ? (int)$_POST['quantite'] : 0;
    $date_expiration = isset($_POST['date']) ? $_POST['date'] : '';

    // Validation
    if (empty($nom) || empty($fournisseur) || $quantite <= 0 || empty($date_expiration)) {
        echo json_encode([
            'success' => false,
            'message' => 'Tous les champs sont obligatoires et la quantité doit être positive'
        ]);
        exit;
    }

    // Préparation et exécution de la requête
    $stmt = $conn->prepare("INSERT INTO produits (nom, fournisseur, quantite, date_expiration) VALUES (:nom, :fournisseur, :quantite, :date_expiration)");
    $stmt->execute([
        'nom' => $nom,
        'fournisseur' => $fournisseur,
        'quantite' => $quantite,
        'date_expiration' => $date_expiration
    ]);

    // Récupération de l'ID du nouveau produit
    $newId = $conn->lastInsertId();

    echo json_encode([
        'success' => true,
        'id' => $newId
    ]);

} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur de base de données : ' . $e->getMessage()
    ]);
}
?>