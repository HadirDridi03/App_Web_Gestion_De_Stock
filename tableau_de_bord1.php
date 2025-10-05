<?php
if (!function_exists('random_bytes')) {
  function random_bytes($length) {
      $bytes = '';
      for ($i = 0; $i < $length; $i++) {
          $bytes .= chr(mt_rand(0, 255));
      }
      return $bytes;
  }
}
session_start();
require_once 'db_config.php';

// Connexion à la base de données
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Génération token CSRF
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Traitement suppression
if (isset($_GET['delete_id']) && isset($_GET['csrf_token'])) {
    if (hash_equals($_SESSION['csrf_token'], $_GET['csrf_token'])) {
        $id = $_GET['delete_id'];
        $stmt = $conn->prepare("DELETE FROM produits WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Produit supprimé avec succès";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Erreur lors de la suppression";
            $_SESSION['message_type'] = "error";
        }
    }
    header("Location: tableau_de_bord1.php");
    exit();
}

// Traitement modification
if (isset($_POST['update']) && isset($_POST['csrf_token'])) {
    if (hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $id = $_POST['id'];
        $nom = $_POST['nom'];
        $fournisseur = $_POST['fournisseur'];
        $quantite = $_POST['quantite'];
        $date_expiration = $_POST['date_expiration'];
        
        $stmt = $conn->prepare("UPDATE produits SET nom=?, fournisseur=?, quantite=?, date_expiration=? WHERE id=?");
        $stmt->bind_param("ssisi", $nom, $fournisseur, $quantite, $date_expiration, $id);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Produit modifié avec succès";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Erreur lors de la modification";
            $_SESSION['message_type'] = "error";
        }
    }
    header("Location: tableau_de_bord1.php");
    exit();
}

// Traitement création
if (isset($_POST['create']) && isset($_POST['csrf_token'])) {
    if (hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $nom = $_POST['nom'];
        $fournisseur = $_POST['fournisseur'];
        $quantite = $_POST['quantite'];
        $date_expiration = $_POST['date_expiration'];
        
        $stmt = $conn->prepare("INSERT INTO produits (nom, fournisseur, quantite, date_expiration) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssis", $nom, $fournisseur, $quantite, $date_expiration);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Produit ajouté avec succès";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Erreur lors de l'ajout";
            $_SESSION['message_type'] = "error";
        }
    }
    header("Location: tableau_de_bord1.php");
    exit();
}

// Récupération produits
$sql = "SELECT * FROM produits";
$result = $conn->query($sql);

// Calcul des statistiques
$stats = [
    'total' => 0,
    'critique' => 0,
    'proche' => 0
];

if ($result->num_rows > 0) {
    $stats['total'] = $result->num_rows;
    $result->data_seek(0);
    
    while($row = $result->fetch_assoc()) {
        if ($row['quantite'] < 10) $stats['critique']++;
        if (strtotime($row['date_expiration']) < strtotime('+1 month')) {
            $stats['proche']++;
        }
    }
    $result->data_seek(0);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PharmaStock | Tableau de Bord</title>
  <link rel="stylesheet" href="tableau_de_bord1.css">
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
      <div class="search-bar">
        <input type="text" id="searchInput" placeholder="Rechercher un produit...">
        <i class="fas fa-search"></i>
      </div>
    </div>
  </header>

  <main>
    <?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-<?= $_SESSION['message_type'] ?>">
      <?= $_SESSION['message'] ?>
      <span class="close-alert">&times;</span>
    </div>
    <?php 
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
    endif; ?>

    <h1 class="dashboard-title">Tableau de Bord</h1>

    <div class="stats-cards">
      <div class="card total">Total: <span><?= $stats['total'] ?></span></div>
      <div class="card critique">Stock Critique<br><span><?= $stats['critique'] ?> produits</span></div>
      <div class="card proche">Péremption Proche<br><span><?= $stats['proche'] ?> produits</span></div>
    </div>

    <div class="table-header">
      <h2>Produits à réapprovisionner</h2>
      <button class="add-btn" onclick="openCreateModal()">
        <i class="fas fa-plus"></i> Ajouter
      </button>
    </div>

    <table>
      <thead>
        <tr>
          <th>Nom</th>
          <th>Fournisseur</th>
          <th>Quantité</th>
          <th>Date Expiration</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="produitTable">
        <?php if ($result->num_rows > 0): ?>
          <?php while($row = $result->fetch_assoc()): ?>
            <?php
              $isCritique = $row['quantite'] < 10;
              $isPerime = strtotime($row['date_expiration']) < time();
              $isPeremptionProche = !$isPerime && (strtotime($row['date_expiration']) < strtotime('+1 month'));
            ?>
            <tr class="<?= $isCritique ? 'row-critique' : '' ?> <?= $isPerime ? 'row-perime' : '' ?> <?= $isPeremptionProche ? 'row-peremption-proche' : '' ?>">
              <td><?= htmlspecialchars($row['nom']) ?></td>
              <td><?= htmlspecialchars($row['fournisseur']) ?></td>
              <td><?= htmlspecialchars($row['quantite']) ?></td>
              <td><?= date('d/m/Y', strtotime($row['date_expiration'])) ?></td>
              <td>
                <a href="#" onclick="openEditModal(
                  <?= $row['id'] ?>,
                  '<?= htmlspecialchars($row['nom'], ENT_QUOTES) ?>',
                  '<?= htmlspecialchars($row['fournisseur'], ENT_QUOTES) ?>',
                  '<?= htmlspecialchars($row['quantite']) ?>',
                  '<?= htmlspecialchars($row['date_expiration']) ?>'
                )">✏️</a>
                <a href="?delete_id=<?= $row['id'] ?>&csrf_token=<?= $_SESSION['csrf_token'] ?>" onclick="return confirm('Supprimer ce produit?')">🗑️</a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="5" class="text-center">Aucun produit trouvé</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>

    <!-- Modal Modification -->
    <div id="editModal" class="modal">
      <div class="modal-content">
        <span class="close" onclick="closeEditModal()">&times;</span>
        <h2>Modifier Produit</h2>
        <form method="POST">
          <input type="hidden" name="id" id="edit_id">
          <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
          <div class="form-group">
            <label>Nom:</label>
            <input type="text" name="nom" id="edit_nom" required>
          </div>
          <div class="form-group">
            <label>Fournisseur:</label>
            <input type="text" name="fournisseur" id="edit_fournisseur" required>
          </div>
          <div class="form-group">
            <label>Quantité:</label>
            <input type="number" name="quantite" id="edit_quantite" min="0" required>
          </div>
          <div class="form-group">
            <label>Date Expiration:</label>
            <input type="date" name="date_expiration" id="edit_date_expiration" required>
          </div>
          <button type="submit" name="update">Enregistrer</button>
        </form>
      </div>
    </div>

    <!-- Modal Création -->
    <div id="createModal" class="modal">
      <div class="modal-content">
        <span class="close" onclick="closeCreateModal()">&times;</span>
        <h2>Ajouter Produit</h2>
        <form method="POST">
          <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
          <div class="form-group">
            <label>Nom:</label>
            <input type="text" name="nom" required>
          </div>
          <div class="form-group">
            <label>Fournisseur:</label>
            <input type="text" name="fournisseur" required>
          </div>
          <div class="form-group">
            <label>Quantité:</label>
            <input type="number" name="quantite" min="0" required>
          </div>
          <div class="form-group">
            <label>Date Expiration:</label>
            <input type="date" name="date_expiration" required>
          </div>
          <button type="submit" name="create">Ajouter</button>
        </form>
      </div>
    </div>

  </main>

  <footer>
    <div class="footer">
      <div><i class="fas fa-envelope"></i> <a href="mailto:PharmaStock@gmail.com" class="footer_text2">PharmaStock@gmail.com</a></div>
      <div><i class="fas fa-phone-alt"></i> <p class="footer_text2">+216 26 212 530</p></div>
      <div><i class="fas fa-map-marker-alt"></i> <p class="footer_text2">Bizerte, Tunisie</p></div>
    </div>
    <p class="footer_text3">© 2025 PharmaStock - Tous droits réservés</p>
  </footer>

  <script>
    // Gestion des modals
    function openEditModal(id, nom, fournisseur, quantite, date_expiration) {
      document.getElementById('edit_id').value = id;
      document.getElementById('edit_nom').value = nom;
      document.getElementById('edit_fournisseur').value = fournisseur;
      document.getElementById('edit_quantite').value = quantite;
      document.getElementById('edit_date_expiration').value = date_expiration;
      document.getElementById('editModal').style.display = 'block';
    }

    function closeEditModal() {
      document.getElementById('editModal').style.display = 'none';
    }

    function openCreateModal() {
      document.getElementById('createModal').style.display = 'block';
    }

    function closeCreateModal() {
      document.getElementById('createModal').style.display = 'none';
    }

    // Fermer les modals en cliquant à l'extérieur
    window.onclick = function(event) {
      if (event.target.className === 'modal') {
        event.target.style.display = 'none';
      }
    }

    // Fermer les alertes
    document.querySelectorAll('.close-alert').forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.target.parentElement.style.display = 'none';
      });
    });

    // Recherche
    document.getElementById('searchInput').addEventListener('input', function() {
      const filter = this.value.toLowerCase();
      const rows = document.querySelectorAll("#produitTable tr");
      
      rows.forEach(row => {
        const cells = row.getElementsByTagName('td');
        let showRow = false;
        
        for (let i = 0; i < cells.length - 1; i++) {
          if (cells[i].textContent.toLowerCase().includes(filter)) {
            showRow = true;
            break;
          }
        }
        
        row.style.display = showRow ? '' : 'none';
      });
    });
  </script>
</body>
</html>