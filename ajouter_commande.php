<?php
header('Content-Type: application/json');
require_once 'db_config.php';

// Activer le rapport d'erreurs pour le débogage
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Méthode non autorisée", 405);
    }

    // Validation des données
    $required = ['numero', 'client', 'date', 'statut'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Le champ $field est requis", 400);
        }
    }

    // Nettoyage des données
    $numero = trim($_POST['numero']);
    $client = trim($_POST['client']);
    $date = trim($_POST['date']);
    $statut = trim($_POST['statut']);

    // Validation spécifique
    if (!preg_match('/^CMD-\d{3}$/', $numero)) {
        throw new Exception("Format de numéro invalide. Doit être CMD-001", 400);
    }

    if (!DateTime::createFromFormat('Y-m-d', $date)) {
        throw new Exception("Format de date invalide. Utilisez AAAA-MM-JJ", 400);
    }

    // Validation du statut
    $statutsValides = ['En attente', 'Livrée', 'Annulée'];
    if (!in_array($statut, $statutsValides)) {
        $statut = 'En attente'; // Valeur par défaut
    }

    // Connexion à la base de données
    $conn = new PDO(
        "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4", 
        DB_USER, 
        DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

    // Vérification de l'unicité
    $stmt = $conn->prepare("SELECT id FROM commandes WHERE numero = ? LIMIT 1");
    $stmt->execute([$numero]);
    if ($stmt->fetch()) {
        throw new Exception("Ce numéro de commande existe déjà", 409);
    }

    // Insertion
    $conn->beginTransaction();
    try {
        $stmt = $conn->prepare("
            INSERT INTO commandes (numero, client, date_commande, statut)
            VALUES (?, ?, ?, ?)
        ");
        
        $stmt->execute([$numero, $client, $date, $statut]);
        $id = $conn->lastInsertId();
        $conn->commit();
    } catch (Exception $e) {
        $conn->rollBack();
        throw $e;
    }

    // Récupération de la commande créée
    $stmt = $conn->prepare("
        SELECT id, numero, client, date_commande, statut
        FROM commandes WHERE id = ?
    ");
    $stmt->execute([$id]);
    $commande = $stmt->fetch();

    if (!$commande) {
        throw new Exception("Erreur lors de la récupération de la commande", 500);
    }

    // Réponse JSON
    echo json_encode([
        'success' => true,
        'message' => 'Commande ajoutée avec succès',
        'commande' => [
            'id' => $commande['id'],
            'numero' => $commande['numero'],
            'client' => $commande['client'],
            'date_commande' => $commande['date_commande'],
            'statut' => $commande['statut'],
            'date_formatee' => date('d/m/Y', strtotime($commande['date_commande']))
        ]
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur de base de données: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    http_response_code($e->getCode() ?: 400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>