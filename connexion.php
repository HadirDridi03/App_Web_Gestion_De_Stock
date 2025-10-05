<?php
session_start();

// Redirection si déjà connecté
if (isset($_SESSION['user'])) {
    header('Location: acceuil.php');
    exit();
}

require 'db_config.php';
$error_message = '';
$success_message = isset($_SESSION['inscription_success']) ? $_SESSION['inscription_success'] : '';
unset($_SESSION['inscription_success']);

// Generate CSRF token using fallback method
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = md5(uniqid(mt_rand(), true));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['csrf_token'])) {
    if (hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        try {
            $conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($_POST['password'], $user['password'])) {
                $_SESSION['user'] = $user;
                unset($_SESSION['csrf_token']);
                header('Location: acceuil.php');
                exit();
            } else {
                $error_message = "Identifiants incorrects";
            }
        } catch(PDOException $e) {
            $error_message = "Erreur système : " . htmlspecialchars($e->getMessage());
        }
    } else {
        $error_message = "Erreur de validation du formulaire";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PharmaStock - Connexion</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css?v=1698777600">
</head>
<body>
    <div class="container">
        <img src="logo.png" alt="PharmaStock Logo" class="logo1">
        <div class="card">
            <h2 class="card-title">Connexion</h2>
            <?php if (!empty($success_message)): ?>
                <div class="success"><?php echo htmlspecialchars($success_message); ?></div>
            <?php endif; ?>
            <?php if (!empty($error_message)): ?>
                <div class="error"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>
            <form action="connexion.php" method="POST" class="form-content">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Email" required aria-label="Adresse email">
                </div>
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" placeholder="Mot de passe" required aria-label="Mot de passe">
                </div>
                <button type="submit" aria-label="Se connecter">Se connecter</button>
            </form>
            <div class="form-footer">
                <a href="#" class="link">Mot de passe oublié ?</a><br>
                <a href="register.php" class="link">Créer un compte</a>
            </div>
        </div>
    </div>
</body>
</html>