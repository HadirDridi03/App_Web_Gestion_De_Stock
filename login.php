<?php
session_start();
require 'db_config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conn = new PDO(
            "mysql:host=".DB_HOST.";dbname=".DB_NAME,
            DB_USER, 
            DB_PASS
        );
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $email = $_POST['email'];
        $password = $_POST['password'];

        $stmt = $conn->prepare("SELECT id, email, password, nom, prenom FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'email' => $user['email'],
                'nom' => $user['nom'],
                'prenom' => $user['prenom']
            ];
            
            header('Location: acceuil.php');
            exit();
        } else {
            $error = "Email ou mot de passe incorrect";
        }
    } catch(PDOException $e) {
        $error = "Erreur de connexion : " . $e->getMessage();
    }
}

// Si on arrive ici, afficher le formulaire
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - PharmaStock</title>
    <link rel="stylesheet" href="connexion.css">
</head>
<body>
    <div class="container">
        <?php if (!empty($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="POST" class="login-box">
            <h2>Connexion</h2>
            
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            
            <label for="password">Mot de passe:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Se connecter</button>
        </form>
    </div>
</body>
</html>