document.addEventListener('DOMContentLoaded', function() {
    const addForm = document.getElementById('addForm');
    if (addForm) {
        addForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const form = e.target;
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            try {
                // Désactiver le bouton pendant le traitement
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> En cours...';

                // Envoyer les données au serveur
                const response = await fetch('ajouter_commande.php', {
                    method: 'POST',
                    body: new FormData(form)
                });

                const data = await response.json();

                if (!data.success) {
                    throw new Error(data.message || 'Erreur lors de l\'ajout');
                }

                // Ajouter la nouvelle ligne au tableau
                const tbody = document.querySelector('tbody');
                if (tbody) {
                    const newRow = document.createElement('tr');
                    newRow.innerHTML = `
                        <td>${escapeHtml(data.commande.numero)}</td>
                        <td>${escapeHtml(data.commande.client)}</td>
                        <td>${data.commande.date_formatee}</td>
                        <td>
                            <span class="status ${getStatutClass(data.commande.statut)}">
                                ${escapeHtml(data.commande.statut)}
                            </span>
                        </td>
                        <td class="actions">
                            <a href="modifier_commande.php?id=${data.commande.id}" class="action-icon edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="supprimer_commande.php?id=${data.commande.id}" class="action-icon delete">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    `;
                    tbody.prepend(newRow);
                }

                // Réinitialiser le formulaire
                form.reset();
                
                // Afficher un message de succès
                showNotification(data.message || 'Commande ajoutée avec succès', 'success');

            } catch (error) {
                console.error('Erreur:', error);
                showNotification(error.message || 'Une erreur est survenue', 'error');
            } finally {
                // Réactiver le bouton
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });
    }

    // Fonction pour échapper les caractères HTML
    function escapeHtml(unsafe) {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // Fonction pour générer la classe CSS du statut
    function getStatutClass(statut) {
        const statutLower = statut.toLowerCase();
        return 'status-' + statutLower.replace(/[éèê ]/g, 'e');
    }

    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;
        document.body.appendChild(notification);
        
        // Animation d'apparition
        setTimeout(() => {
            notification.style.opacity = '1';
        }, 10);
        
        // Disparaît après 3 secondes (3000ms)
        setTimeout(() => {
            notification.style.opacity = '0';
            // Supprime après l'animation de disparition
            setTimeout(() => {
                notification.remove();
            }, 500); // Correspond à la durée de l'animation CSS
        }, 3000);
    }
});
