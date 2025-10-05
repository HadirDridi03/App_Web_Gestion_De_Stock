document.addEventListener('DOMContentLoaded', function() {
    // Fonctions pour le modal d'édition
    window.openEditModal = function(id, nom, fournisseur, quantite, date) {
        document.getElementById('editId').value = id;
        document.getElementById('editNom').value = nom;
        document.getElementById('editFournisseur').value = fournisseur;
        document.getElementById('editQuantite').value = quantite;
        document.getElementById('editDate').value = date;
        document.getElementById('editModal').style.display = 'block';
    };

    window.closeEditModal = function() {
        document.getElementById('editModal').style.display = 'none';
    };

    // Gestion des clics
    document.addEventListener('click', function(e) {
        // Édition
        if (e.target.classList.contains('edit-btn')) {
            e.preventDefault();
            const row = e.target.closest('tr');
            openEditModal(
                row.getAttribute('data-id'),
                row.cells[0].textContent,
                row.cells[1].textContent,
                row.cells[2].textContent,
                formatDateForInput(row.cells[3].textContent)
            );
        }
        
        // Suppression
        if (e.target.classList.contains('delete-btn')) {
            e.preventDefault();
            if (confirm('Voulez-vous vraiment supprimer ce produit ?')) {
                window.location.href = `supprimer.php?id=${e.target.closest('tr').getAttribute('data-id')}`;
            }
        }
        
        // Fermeture modal
        if (e.target.classList.contains('close')) {
            closeEditModal();
        }
    });

    // Formatage de date
    function formatDateForInput(dateString) {
        const parts = dateString.split('/');
        return `${parts[2]}-${parts[1].padStart(2, '0')}-${parts[0].padStart(2, '0')}`;
    }
    document.getElementById("addForm").addEventListener("submit", function(e) {
        e.preventDefault();
        const formData = new FormData(this);
      
        fetch("ajouter.php", {
          method: "POST",
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert("Produit ajouté avec succès");
            this.reset();
            chargerProduits(); // assure-toi que cette fonction existe
          } else {
            alert("Erreur : " + data.message);
          }
        })
        .catch(error => {
          console.error("Erreur fetch :", error);
          alert("Erreur lors de l'ajout");
        });
      });
      
    

    // Gestion de l'ajout
   /* document.getElementById('addForm').addEventListener('submit', async function(e) {
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
            console.log('Réponse:', result);

            if (result.success) {
                alert('Produit ajouté avec succès!');
                location.reload();
            } else {
                throw new Error(result.message || 'Erreur lors de l\'ajout');
            }
        } catch (error) {
            console.error('Erreur:', error);
            alert('Erreur: ' + error.message);
        }
    });*/

    // Fonction de recherche
    document.getElementById('searchInput').addEventListener('input', function() {
        const searchQuery = this.value.toLowerCase();
        document.querySelectorAll('#produitTable tr').forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchQuery) ? '' : 'none';
        });
    });
});
function chargerProduits() {
    fetch('lister_produits.php')
        .then(response => response.json())
        .then(data => {
            const tableau = document.getElementById("tableauProduits");
            tableau.innerHTML = ""; // vide avant de remplir

            data.forEach(produit => {
                const ligne = document.createElement("tr");
                ligne.setAttribute("data-id", produit.id); // important pour édition/suppression
                ligne.innerHTML = `
                    <td>${produit.nom}</td>
                    <td>${produit.fournisseur}</td>
                    <td>${produit.quantite}</td>
                    <td>${produit.date_expiration}</td>
                    <td>
                        <button class="edit-btn">Modifier</button>
                        <button class="delete-btn">Supprimer</button>
                    </td>
                `;
                tableau.appendChild(ligne);
            });
        })
        .catch(error => {
            console.error("Erreur chargement :", error);
        });
}
