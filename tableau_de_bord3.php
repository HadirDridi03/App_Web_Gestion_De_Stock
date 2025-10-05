<?php
require_once 'db_config.php';

// Connexion à la base de données
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Traitement de la suppression
if (isset($_GET['supprimer'])) {
    $id = $_GET['supprimer'];
    $stmt = $conn->prepare("DELETE FROM commandes WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: tableau_de_bord3.php");
    exit();
}

// Traitement de l'ajout
if (isset($_POST['ajouter'])) {
    $numero = $_POST['numero'];
    $client = $_POST['client'];
    $date = $_POST['date'];
    $statut = $_POST['statut'];
    
    $stmt = $conn->prepare("INSERT INTO commandes (numero_commande, client, date_commande, statut) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $numero, $client, $date, $statut);
    $stmt->execute();
    $stmt->close();
    header("Location: tableau_de_bord3.php");
    exit();
}

// Traitement de la modification
if (isset($_POST['modifier'])) {
    $id = $_POST['id'];
    $numero = $_POST['numero'];
    $client = $_POST['client'];
    $date = $_POST['date'];
    $statut = $_POST['statut'];
    
    $stmt = $conn->prepare("UPDATE commandes SET numero_commande=?, client=?, date_commande=?, statut=? WHERE id=?");
    $stmt->bind_param("ssssi", $numero, $client, $date, $statut, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: tableau_de_bord3.php");
    exit();
}

// Récupération des commandes
$sql = "SELECT * FROM commandes ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PharmaStock | Commandes</title>
  <link rel="stylesheet" href="tableau_de_bord3.css">
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
    <i class="fas fa-cog"></i>
  </a>
    </nav>
  </header>

  <main>
    <div class="search-bar">
      <input type="text" id="searchInput" placeholder="Rechercher une commande...">
      <i class="fas fa-search"></i>

    </div>

    <table>
      <thead>
        <tr>
          <th>N commande</th>
          <th>Client</th>
          <th>Date</th>
          <th>Statut</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody id="commandeTable">
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['numero_commande']) ?></td>
          <td><?= htmlspecialchars($row['client']) ?></td>
          <td><?= date('d/m/Y', strtotime($row['date_commande'])) ?></td>
          <td>
  <span class="status <?php 
      echo $row['statut'] == 'Livrée' ? 'green' : 
           ($row['statut'] == 'En attente' ? 'blue' : 'red'); 
  ?>">
    <?= htmlspecialchars($row['statut']) ?>
  </span>
</td>
          <td>
            <a href="#" onclick="openEditModal(
              <?= $row['id'] ?>,
              '<?= htmlspecialchars($row['numero_commande'], ENT_QUOTES) ?>',
              '<?= htmlspecialchars($row['client'], ENT_QUOTES) ?>',
              '<?= date('Y-m-d', strtotime($row['date_commande'])) ?>',
              '<?= htmlspecialchars($row['statut'], ENT_QUOTES) ?>'
            )">✏️</a>
            <a href="?supprimer=<?= $row['id'] ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette commande?')">🗑️</a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>

    <div class="container">
      <div class="login-box">
        <h3 class="title2">Ajouter une commande</h3>
        <form method="POST">
          <label for="numero">N commande</label>
          <input type="text" id="numero" name="numero" required>

          <label for="client">Client</label>
          <input type="text" id="client" name="client" required>

          <label for="date">Date</label>
          <input type="date" id="date" name="date" required>

          <label for="statut">Statut</label>
          <select id="statut" name="statut" required>
            <option value="Livrée">Livrée</option>
            <option value="En attente">En attente</option>
            <option value="Annulée">Annulée</option>
          </select>

          <button type="submit" name="ajouter">Ajouter</button>
        </form>
      </div>
    </div>
  </main>

  <!-- Modal de modification -->
  <div id="editModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeEditModal()">&times;</span>
      <h2>Modifier la commande</h2>
      <form method="POST">
        <input type="hidden" name="id" id="edit_id">
        <div class="form-group">
          <label for="edit_numero">N commande:</label>
          <input type="text" id="edit_numero" name="numero" required>
        </div>
        <div class="form-group">
          <label for="edit_client">Client:</label>
          <input type="text" id="edit_client" name="client" required>
        </div>
        <div class="form-group">
          <label for="edit_date">Date:</label>
          <input type="date" id="edit_date" name="date" required>
        </div>
        <div class="form-group">
          <label for="edit_statut">Statut:</label>
          <select id="edit_statut" name="statut" required>
            <option value="Livrée">Livrée</option>
            <option value="En attente">En attente</option>
            <option value="Annulée">Annulée</option>
          </select>
        </div>
        <button type="submit" name="modifier">Enregistrer</button>
      </form>
    </div>
  </div>

  
  <footer>
    <div class="footer">
      <div><i class="fas fa-envelope"></i> <a href="mailto:PharmaStock@gmail.com">PharmaStock@gmail.com</a></div>
      <div><i class="fas fa-phone-alt"></i> <p>+216 26 212 530</p></div>
      <div><i class="fas fa-map-marker-alt"></i> <p>Bizerte, Tunisie</p></div>
    </div>
    <p>© <?= date('Y') ?> PharmaStock - Tous droits réservés</p>
  </footer>


  <script src="tableau_de_bord3.js"></script>
  <script>
    function openEditModal(id, numero, client, date, statut) {
      document.getElementById('edit_id').value = id;
      document.getElementById('edit_numero').value = numero;
      document.getElementById('edit_client').value = client;
      document.getElementById('edit_date').value = date;
      document.getElementById('edit_statut').value = statut;
      document.getElementById('editModal').style.display = 'block';
    }

    function closeEditModal() {
      document.getElementById('editModal').style.display = 'none';
    }

    // Recherche en direct
    document.getElementById('searchInput').addEventListener('input', function () {
      let filter = this.value.toLowerCase();
      let rows = document.querySelectorAll("#commandeTable tr");
      
      rows.forEach(row => {
        let text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? "" : "none";
      });
    });
  </script>
  
</body>
</html>