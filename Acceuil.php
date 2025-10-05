<?php
// Démarrage de session pour gérer l'authentification
session_start();

// Redirection si l'utilisateur est déjà connecté
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

// Redirige si non connecté
//if (!isset($_SESSION['user'])) {
    //header('Location: connexion.php');
    //exit();
//}
//$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PharmaStock | Accueil</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="Acceuil.css">
    
</head>
<body>
<header>
    <img src="logo.png" alt="logo" class="logo1">
    <?php if (isset($_SESSION['user'])): ?>
        <div class="user-header">
            <div class="welcome">Bienvenue, <?= htmlspecialchars($_SESSION['user']['prenom']) ?> !</div>
            <a href="logout.php" class="logout">
                <i class="fas fa-sign-out-alt"></i> Déconnexion
            </a>
            <a href="tableau_de_bord4.php" class="settings-icon">
        <i class="fas fa-cog"></i></a>
        </div>
    <?php endif; ?>
</header>


    <main class="main">
        <section class="partie1">
            <img src="image3.jpg" alt="image1">
            <h1>Bienvenue sur PharmaStock !</h1>
            <div class="btn-container" style="position: absolute; top: 60%; left: 20%; transform: translate(-50%, -50%);">
    <a href="connexion.php" class="btn-simple">Se connecter</a>
</div>
        </section>

        <section class="partie2">
            <section class="sous_partie2">
                <img src="image1.jpg" alt="image2">
                <div class="overlay">
                    <a href="tableau_de_bord2.php"><span>Produits</span></a>
                </div>
            </section>
            <section class="sous_partie2">
                <img src="image2.jpg" alt="image3">
                <div class="overlay">
                    <a href="tableau_de_bord3.php"><span>Commandes</span></a>
                </div>
            </section>
        </section>

        <section class="partie3">
            <div class="cadre_partie3">
                <h1 class="titre">A propos</h1>
                <p class="details1"><span class="minititre">PharmaStock </span>est une solution innovante de gestion des stocks dédiée aux pharmacies et aux établissements de santé. Notre plateforme permet de suivre en temps réel l'état des stocks, d'optimiser la gestion des produits pharmaceutiques et d'anticiper les ruptures ou expirations de médicaments.
                <br><span class="minititre">PharmaStock</span> aide les professionnels de la santé à assurer une gestion efficace et sécurisée de leurs médicaments tout en minimisant les pertes et les erreurs de stock.
                <p class="details2">Simplifiez la gestion de votre pharmacie avec PharmaStock !</p>
            </div>
        </section>
    </main>

    <footer>
        <div class="footer">
            <div>
                <i class="fas fa-envelope"></i>
                <a href="mailto:PharmaStock@gmail.com" class="footer_text2">PharmaStock@gmail.com</a>
            </div>
            <div>
                <i class="fas fa-phone-alt"></i>
                <p class="footer_text2">+216 12 345 678</p>
            </div>
            <div>
                <i class="fas fa-map-marker-alt"></i>
                <p class="footer_text2">Bizerte, Tunisie</p>
            </div>
        </div>
        <p class="footer_text3">© <?php echo date('Y'); ?> PharmaStock - Tous droits réservés</p>
    </footer>
</body>
</html>

