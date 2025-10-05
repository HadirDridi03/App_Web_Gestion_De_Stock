<?php
session_start();
define('BASE_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/Projet_AGL/projetAGL2/');
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . 'connexion.php');
    exit();
}
// Vérifier si l'utilisateur est connecté
/*if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit();
}*/

// Configuration de la base de données
$host = 'localhost';
$dbname = 'pharmastock';
$username = 'root';
$password = '';

try {
    // Connexion à la base de données
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupérer les informations de l'utilisateur
    $stmt = $conn->prepare("SELECT nom, prenom FROM utilisateurs WHERE id = :id");
    $stmt->bindParam(':id', $_SESSION['user_id']);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Erreur de connexion à la base de données: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>PharmaStock - Tableau de bord</title>
    <link rel="stylesheet" href="connexion.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <img src="logo.png" alt="PharmaStock Logo" class="logo">
        <h2 class="welcome">Bienvenue, <?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?> !</h2>
        
        <div class="dashboard">
            <p>Vous êtes maintenant connecté à PharmaStock.</p>
            <a href="logout.php" class="logout-link">Déconnexion</a>
        </div>
    </div>
</body>
</html>