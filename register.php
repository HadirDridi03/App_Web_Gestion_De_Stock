<?php
session_start();
require 'db_config.php';

// Generate CSRF token using fallback method
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = md5(uniqid(mt_rand(), true));
}

// Function to validate password complexity
function validatePassword($password) {
    return preg_match('/^(?=.*[A-Za-z])(?=.*\d).{8,}$/', $password);
}

// Function to check if email exists
function emailExists($email, $conn) {
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    return $stmt->fetch() !== false;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['csrf_token'])) {
    if (hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        try {
            $conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $nom = htmlspecialchars(trim($_POST['nom']));
            $prenom = htmlspecialchars(trim($_POST['prenom']));
            $password = $_POST['password'];

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Adresse email invalide");
            }
            if (empty($nom) || empty($prenom)) {
                throw new Exception("Nom et prénom sont requis");
            }
            if (!validatePassword($password)) {
                throw new Exception("Le mot de passe doit contenir au moins 8 caractères, incluant des lettres et des chiffres");
            }

            if (emailExists($email, $conn)) {
                throw new Exception("Cet email est déjà utilisé");
            }

            $password_hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("
                INSERT INTO users (email, password, nom, prenom, date_creation) 
                VALUES (?, ?, ?, ?, NOW())
            ");

            if ($stmt->execute([$email, $password_hashed, $nom, $prenom])) {
                $_SESSION['inscription_success'] = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
                unset($_SESSION['csrf_token']);
                header('Location: connexion.php');
                exit();
            }
        } catch(PDOException $e) {
            $error = "Erreur : " . (strpos($e->getMessage(), '23000') !== false 
                ? "Cet email est déjà utilisé" 
                : "Une erreur technique est survenue");
        } catch(Exception $e) {
            $error = $e->getMessage();
        }
    } else {
        $error = "Erreur de validation du formulaire";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - PharmaStock</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css?v=1698777600">
</head>
<body>
    <div class="container">
        <img src="logo.png" alt="PharmaStock Logo" class="logo1">
        <div class="card">
            <h2 class="card-title">Création de compte</h2>
            <?php if (!empty($error)): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form method="POST" class="form-content">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                <div class="form-group">
                    <label for="nom">Nom</label>
                    <input type="text" id="nom" name="nom" required aria-label="Nom">
                </div>
                <div class="form-group">
                    <label for="prenom">Prénom</label>
                    <input type="text" id="prenom" name="prenom" required aria-label="Prénom">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required aria-label="Adresse email">
                </div>
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required minlength="8" aria-label="Mot de passe">
                </div>
                <button type="submit" aria-label="Valider l'inscription">Valider l'inscription</button>
            </form>
            <div class="form-footer">
                <a href="connexion.php" class="link">← Retour à la connexion</a>
            </div>
        </div>
    </div>
</body>
</html>