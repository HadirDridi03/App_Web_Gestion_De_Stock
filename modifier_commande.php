<?php
require_once 'db_config.php';

// Mode GET - Afficher le formulaire
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
    try {
        $conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
        $stmt = $conn->prepare("SELECT * FROM commandes WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $commande = $stmt->fetch();
        
        if (!$commande) {
            header("Location: tableau_de_bord3.php?error=Commande non trouvée");
            exit();
        }
        ?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <title>Modifier Commande</title>
            <link rel="stylesheet" href="tableau_de_bord3.css">
        </head>
        <body>
            <div class="form-container">
                <form method="post" class="command-form">
                    <h3><i class="fas fa-edit"></i> Modifier la commande</h3>
                    <input type="hidden" name="id" value="<?= $commande['id'] ?>">
                    
                    <div class="form-group">
                        <label>N° commande:</label>
                        <input type="text" name="numero" value="<?= htmlspecialchars($commande['numero']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Client:</label>
                        <input type="text" name="client" value="<?= htmlspecialchars($commande['client']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Date:</label>
                        <input type="date" name="date" value="<?= $commande['date_commande'] ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Statut:</label>
                        <select name="statut" required>
                            <option value="En attente" <?= $commande['statut'] == 'En attente' ? 'selected' : '' ?>>En attente</option>
                            <option value="Livrée" <?= $commande['statut'] == 'Livrée' ? 'selected' : '' ?>>Livrée</option>
                            <option value="Annulée" <?= $commande['statut'] == 'Annulée' ? 'selected' : '' ?>>Annulée</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-save"></i> Enregistrer
                    </button>
                    <a href="tableau_de_bord3.php" class="btn-cancel">Annuler</a>
                </form>
            </div>
        </body>
        </html>
        <?php
        exit();
    } catch(PDOException $e) {
        header("Location: tableau_de_bord3.php?error=Erreur de base de données");
        exit();
    }
}

// Mode POST - Traiter la modification
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
        
        // Vérifier que la commande existe
        $stmt = $conn->prepare("SELECT id FROM commandes WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        
        if (!$stmt->fetch()) {
            header("Location: tableau_de_bord3.php?error=Commande non trouvée");
            exit();
        }
        
        // Mettre à jour
        $stmt = $conn->prepare("UPDATE commandes SET 
            numero = ?, 
            client = ?, 
            date_commande = ?, 
            statut = ? 
            WHERE id = ?");
        
        $stmt->execute([
            $_POST['numero'],
            $_POST['client'],
            $_POST['date'],
            $_POST['statut'],
            $_POST['id']
        ]);
        
        header("Location: tableau_de_bord3.php?success=Commande modifiée avec succès");
        exit();
        
    } catch(PDOException $e) {
        header("Location: tableau_de_bord3.php?error=Erreur lors de la modification");
        exit();
    }
}

header("Location: tableau_de_bord3.php");
exit();
?>