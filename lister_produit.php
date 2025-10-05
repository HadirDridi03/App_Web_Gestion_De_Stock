<?php
require_once 'db_config.php';

header('Content-Type: application/json');

try {
    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->query("SELECT * FROM produits ORDER BY id DESC");
    $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($produits);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Erreur BDD: ' . $e->getMessage()]);
}
