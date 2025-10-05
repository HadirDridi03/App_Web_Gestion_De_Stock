<?php
// Gestion des messages
$message = '';
if (isset($_GET['success'])) {
    if ($_GET['success'] == 'modif') {
        $message = '<div class="success">Produit modifié avec succès!</div>';
    } elseif ($_GET['success'] == 'suppr') {
        $message = '<div class="success">Produit supprimé avec succès!</div>';
    }
} elseif (isset($_GET['error'])) {
    $message = '<div class="error">Erreur lors de l\'opération</div>';
}

// Connexion à la base de données
require_once 'db_config.php';

try {
    $conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupération des produits avec PDO
    $produits = $conn->query("SELECT * FROM produits ORDER BY id DESC")->fetchAll();
} catch(PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PharmaStock | Produits</title>
  <link rel="stylesheet" href="tableau_de_bord2.css?v=<?= time() ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
      <input type="text" id="searchInput" placeholder="Rechercher un produit...">
      <i class="fas fa-search"></i>
    </div>

    <table>
      <thead>
        <tr>
          <th>Nom</th>
          <th>Fournisseur</th>
          <th>Quantité</th>
          <th>Date expiration</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="produitTable">
        <?php foreach($produits as $row): ?>
        <tr data-id="<?= $row['id'] ?>">
            <td><?= htmlspecialchars($row['nom']) ?></td>
            <td><?= htmlspecialchars($row['fournisseur']) ?></td>
            <td><?= htmlspecialchars($row['quantite']) ?></td>
            <td><?= date('d/m/Y', strtotime($row['date_expiration'])) ?></td>
            <td>
                <a href="#" class="action-icon edit-btn">✏️</a>
                <a href="#" class="action-icon delete-btn">🗑️</a>
            </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <div class="container">
      <form id="addForm" class="login-box">
        <h3>Ajouter un produit</h3>
        <label for="nom">Nom:</label>
        <input type="text" id="nom" name="nom" required>
        
        <label for="fournisseur">Fournisseur:</label>
        <input type="text" id="fournisseur" name="fournisseur" required>
        
        <label for="quantite">Quantité:</label>
        <input type="number" id="quantite" name="quantite" min="1" required>
        
        <label for="date">Date expiration:</label>
        <input type="date" id="date" name="date" required>
        
        <button type="submit">Ajouter</button>
      </form>
    </div>
  </main>

  <footer>
    <div class="footer">
      <div><i class="fas fa-envelope"></i> <a href="mailto:PharmaStock@gmail.com" class="footer_text2">PharmaStock@gmail.com</a></div>
      <div><i class="fas fa-phone-alt" class="footer_text2"></i> <p>+216 26 212 530</p></div>
      <div><i class="fas fa-map-marker-alt" class="footer_text2"></i> <p>Bizerte, Tunisie</p></div>
    </div>
    <p class="footer_text3">© <?= date('Y') ?> PharmaStock - Tous droits réservés</p>
  </footer>

  <div id="editModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeEditModal()">×</span>
      <h2>Modifier le produit</h2>
      <form id="editForm" method="POST" action="modifier.php">
        <input type="hidden" id="editId" name="id">
        <label for="editNom">Nom:</label>
        <input type="text" id="editNom" name="nom" required>
        
        <label for="editFournisseur">Fournisseur:</label>
        <input type="text" id="editFournisseur" name="fournisseur" required>
        
        <label for="editQuantite">Quantité:</label>
        <input type="number" id="editQuantite" name="quantite" min="1" required>
        
        <label for="editDate">Date expiration:</label>
        <input type="date" id="editDate" name="date_expiration" required>
        
        <button type="submit">Enregistrer</button>
      </form>
    </div>
  </div>

  <script>
  // Fonctions pour le modal
  function openEditModal(id, nom, fournisseur, quantite, date) {
      document.getElementById('editId').value = id;
      document.getElementById('editNom').value = nom;
      document.getElementById('editFournisseur').value = fournisseur;
      document.getElementById('editQuantite').value = quantite;
      document.getElementById('editDate').value = date;
      document.getElementById('editModal').style.display = 'block';
  }

  function closeEditModal() {
      document.getElementById('editModal').style.display = 'none';
  }

  // Formatage de la date pour l'input date
  function formatDateForInput(dateString) {
      const parts = dateString.split('/');
      return `${parts[2]}-${parts[1].padStart(2, '0')}-${parts[0].padStart(2, '0')}`;
  }

  // Formatage de la date pour l'affichage
  function formatDateForDisplay(dateString) {
      const date = new Date(dateString);
      const day = String(date.getDate()).padStart(2, '0');
      const month = String(date.getMonth() + 1).padStart(2, '0');
      const year = date.getFullYear();
      return `${day}/${month}/${year}`;
  }

  // Gestion des événements au chargement
  document.addEventListener('DOMContentLoaded', function() {
      // Boutons d'édition
      document.querySelectorAll('.edit-btn').forEach(btn => {
          btn.addEventListener('click', function(e) {
              e.preventDefault();
              const row = this.closest('tr');
              openEditModal(
                  row.getAttribute('data-id'),
                  row.cells[0].textContent,
                  row.cells[1].textContent,
                  row.cells[2].textContent,
                  formatDateForInput(row.cells[3].textContent)
              );
          });
      });

      // Boutons de suppression
      document.querySelectorAll('.delete-btn').forEach(btn => {
          btn.addEventListener('click', function(e) {
              e.preventDefault();
              if (confirm('Voulez-vous vraiment supprimer ce produit ?')) {
                  window.location.href = `supprimer.php?id=${this.closest('tr').getAttribute('data-id')}`;
              }
          });
      });

      // Fermer le modal en cliquant sur la croix
      document.querySelector('.close').addEventListener('click', closeEditModal);

      // Fermer en cliquant à l'extérieur
      window.addEventListener('click', function(e) {
          if (e.target === document.getElementById('editModal')) {
              closeEditModal();
          }
      });

      // Gestion de l'ajout
      document.getElementById('addForm').addEventListener('submit', async function(e) {
          e.preventDefault();

          // Validation côté client
          const nom = document.getElementById('nom').value.trim();
          const fournisseur = document.getElementById('fournisseur').value.trim();
          const quantite = document.getElementById('quantite').value;
          const date = document.getElementById('date').value;

          if (!nom || !fournisseur || !quantite || !date) {
              alert('Tous les champs sont obligatoires');
              return;
          }

          try {
              const response = await fetch('ajouter.php', {
                  method: 'POST',
                  headers: {
                      'Content-Type': 'application/x-www-form-urlencoded',
                  },
                  body: new URLSearchParams({
                      nom: nom,
                      fournisseur: fournisseur,
                      quantite: quantite,
                      date: date
                  })
              });

              const result = await response.json();

              if (result.success) {
                  // Ajouter le produit au tableau
                  const tableBody = document.getElementById('produitTable');
                  const newRow = document.createElement('tr');
                  newRow.setAttribute('data-id', result.id);
                  newRow.innerHTML = `
                      <td>${htmlspecialchars(nom)}</td>
                      <td>${htmlspecialchars(fournisseur)}</td>
                      <td>${quantite}</td>
                      <td>${formatDateForDisplay(date)}</td>
                      <td>
                          <a href="#" class="action-icon edit-btn">✏️</a>
                          <a href="#" class="action-icon delete-btn">🗑️</a>
                      </td>
                  `;
                  tableBody.prepend(newRow);

                  // Réinitialiser le formulaire
                  this.reset();

                  // Ajouter les événements aux nouveaux boutons
                  newRow.querySelector('.edit-btn').addEventListener('click', function(e) {
                      e.preventDefault();
                      openEditModal(
                          newRow.getAttribute('data-id'),
                          newRow.cells[0].textContent,
                          newRow.cells[1].textContent,
                          newRow.cells[2].textContent,
                          formatDateForInput(newRow.cells[3].textContent)
                      );
                  });

                  newRow.querySelector('.delete-btn').addEventListener('click', function(e) {
                      e.preventDefault();
                      if (confirm('Voulez-vous vraiment supprimer ce produit ?')) {
                          window.location.href = `supprimer.php?id=${newRow.getAttribute('data-id')}`;
                      }
                  });

                  alert('Produit ajouté avec succès!');
              } else {
                  throw new Error(result.message || 'Erreur lors de l\'ajout');
              }
          } catch (error) {
              console.error('Erreur:', error);
              alert('Erreur: ' + error.message);
          }
      });

      // Fonction de recherche
      document.getElementById('searchInput').addEventListener('input', function() {
          const searchQuery = this.value.toLowerCase();
          document.querySelectorAll('#produitTable tr').forEach(row => {
              const text = row.textContent.toLowerCase();
              row.style.display = text.includes(searchQuery) ? '' : 'none';
          });
      });

      // Fonction pour échapper les caractères HTML
      function htmlspecialchars(str) {
          const map = {
              '&': '&amp;',
              '<': '&lt;',
              '>': '&gt;',
              '"': '&quot;',
              "'": '&#039;'
          };
          return str.replace(/[&<>"']/g, function(m) { return map[m]; });
      }
  });
  </script>
</body>
</html>