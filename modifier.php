<?php
require_once 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Préparation de la requête
        $stmt = $conn->prepare("UPDATE produits SET 
                               nom = :nom, 
                               fournisseur = :fournisseur, 
                               quantite = :quantite, 
                               date_expiration = :date_expiration 
                               WHERE id = :id");

        // Exécution avec les paramètres
        $stmt->execute([
            ':nom' => $_POST['nom'],
            ':fournisseur' => $_POST['fournisseur'],
            ':quantite' => $_POST['quantite'],
            ':date_expiration' => $_POST['date_expiration'],
            ':id' => $_POST['id']
        ]);

        // Redirection avec message de succès
        header('Location: tableau_de_bord2.php?success=modif');
        exit();
    } catch(PDOException $e) {
        // Redirection avec message d'erreur
        header('Location: tableau_de_bord2.php?error=1');
        exit();
    }
} else {
    // Si la méthode n'est pas POST, rediriger
    header('Location: tableau_de_bord2.php');
    exit();
}