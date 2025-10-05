<?php
require_once 'db_config.php';

if (isset($_GET['id'])) {
    try {
        $conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
        
        // Vérifier que la commande existe
        $stmt = $conn->prepare("SELECT id FROM commandes WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        
        if ($stmt->fetch()) {
            $stmt = $conn->prepare("DELETE FROM commandes WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            header("Location: tableau_de_bord3.php?success=Commande supprimée avec succès");
        } else {
            header("Location: tableau_de_bord3.php?error=Commande non trouvée");
        }
        exit();
    } catch(PDOException $e) {
        header("Location: tableau_de_bord3.php?error=Erreur lors de la suppression");
        exit();
    }
}

header("Location: tableau_de_bord3.php");
exit();
?>