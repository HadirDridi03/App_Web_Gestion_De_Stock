<?php
require_once 'db_config.php';

if (isset($_GET['id'])) {
    try {
        $conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
        $stmt = $conn->prepare("DELETE FROM produits WHERE id=?");
        $stmt->execute([$_GET['id']]);
        
        header("Location: tableau_de_bord2.php?success=suppr");
        exit();
    } catch(PDOException $e) {
        header("Location: tableau_de_bord2.php?error=db");
        exit();
    }
}

header("Location: tableau_de_bord2.php");
exit();
?>