<?php
// Inclusion du fichier de configuration
require_once 'db_config.php';

// Connexion à la base de données
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Traitement de la désactivation
if (isset($_GET['desactiver'])) {
    $email = $_GET['desactiver'];
    $stmt = $conn->prepare("UPDATE utilisateurs SET statut='Inactif' WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->close();
}

// Traitement de l'activation
if (isset($_GET['activer'])) {
    $email = $_GET['activer'];
    $stmt = $conn->prepare("UPDATE utilisateurs SET statut='Actif' WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->close();
}

// Traitement de la modification
if (isset($_POST['update'])) {
    $original_email = $_POST['original_email'];
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $statut = $_POST['statut'];
    
    $stmt = $conn->prepare("UPDATE utilisateurs SET nom=?, email=?, role=?, statut=? WHERE email=?");
    $stmt->bind_param("sssss", $nom, $email, $role, $statut, $original_email);
    $stmt->execute();
    $stmt->close();
}

// Récupération des utilisateurs
$sql = "SELECT * FROM utilisateurs";
$result = $conn->query($sql);
?>


<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PharmaStock | Tableau de Bord</title>
  <link rel="stylesheet" href="tableau_de_bord4.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab&display=swap" rel="stylesheet">
</head>
<body>

  <header>
    <img src="logo.png" alt="logo" class="logo1">
    <nav>
      <a href="Acceuil.php">Accueil</a>
      <a href="tableau_de_bord1.php">Tableau de Board</a>
      <a href="tableau_de_bord2.php">Produits</a>
      <a href="tableau_de_bord3.php">Commandes</a>
      <a href="tableau_de_bord4.php" class="settings-icon">
        <i class="fas fa-cog"></i></a>
    </nav>
  </header>

  <main>
    <div class="search-export">
      <input type="text" id="searchInput" placeholder="Rechercher...">
      <button class="csv-btn">Exporter en CSV</button>
    </div>

    <table>
      <thead>
        <tr>
          <th>Nom</th>
          <th>Email</th>
          <th>Rôle</th>
          <th>Statut</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody id="utilisateurTable">
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?php echo $row['nom']; ?></td>
          <td><?php echo $row['email']; ?></td>
          <td>
            <span class="role <?php 
                echo $row['role'] == 'Pharmacien' ? 'blue' : 
                     ($row['role'] == 'Assistant' ? 'green' : 'red'); 
            ?>">
              <?php echo $row['role']; ?>
            </span>
          </td>
          <td>
            <span class="status <?php echo $row['statut'] == 'Actif' ? 'green' : 'yellow'; ?>">
              <?php echo $row['statut']; ?>
            </span>
          </td>
          <td>
            <a href="#" onclick="openEditModal(
                '<?php echo $row['nom']; ?>',
                '<?php echo $row['email']; ?>',
                '<?php echo $row['role']; ?>',
                '<?php echo $row['statut']; ?>'
            )">✏️</a>
            
            <?php if($row['statut'] == 'Actif'): ?>
              <a href="?desactiver=<?php echo $row['email']; ?>" title="Désactiver">🚫</a>
            <?php else: ?>
              <a href="?activer=<?php echo $row['email']; ?>" title="Activer">✅</a>
            <?php endif; ?>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </main>

  <!-- Modal de modification -->
  <div id="editModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeEditModal()">&times;</span>
      <h2>Modifier l'utilisateur</h2>
      <form method="POST">
        <input type="hidden" name="original_email" id="original_email">
        <div class="form-group">
          <label for="nom">Nom:</label>
          <input type="text" id="nom" name="nom" required>
        </div>
        <div class="form-group">
          <label for="email">Email:</label>
          <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
          <label for="role">Rôle:</label>
          <select id="role" name="role" required>
            <option value="Pharmacien">Pharmacien</option>
            <option value="Assistant">Assistant</option>
            <option value="Administrateur">Administrateur</option>
          </select>
        </div>
        <div class="form-group">
          <label for="statut">Statut:</label>
          <select id="statut" name="statut" required>
            <option value="Actif">Actif</option>
            <option value="Inactif">Inactif</option>
          </select>
        </div>
        <button type="submit" name="update">Enregistrer</button>
      </form>
    </div>
  </div>

  <footer>
    <div class="footer">
      <div>
        <i class="fas fa-envelope"></i>
        <a href="mailto:PharmaStock@gmail.com" class="footer_text2">PharmaStock@gmail.com</a>
      </div>
      <div>
        <i class="fas fa-phone-alt"></i>
        <p class="footer_text2">+216 26 212 530</p>
      </div>
      <div>
        <i class="fas fa-map-marker-alt"></i>
        <p class="footer_text2">Bizerte, Tunisie</p>
      </div>
    </div>
    <p class="footer_text3">© 2024 PharmaStock - Tous droits réservés</p>
  </footer>

  <script src="tableau_de_bord4.js"></script>
  <script>
    function openEditModal(nom, email, role, statut) {
      document.getElementById('original_email').value = email;
      document.getElementById('nom').value = nom;
      document.getElementById('email').value = email;
      document.getElementById('role').value = role;
      document.getElementById('statut').value = statut;
      document.getElementById('editModal').style.display = 'block';
    }

    function closeEditModal() {
      document.getElementById('editModal').style.display = 'none';
    }
  </script>
</body>
</html>