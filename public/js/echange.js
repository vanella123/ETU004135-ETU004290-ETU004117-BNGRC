
const ECFG = window.ECHANGE_CONFIG || {};
const userId = (typeof ECFG.userId !== 'undefined') ? ECFG.userId : (window.USER_ID || null);
const BASE_URL = (typeof ECFG.BASE_URL !== 'undefined') ? ECFG.BASE_URL : (window.BASE_URL || '');
let currentFilter = 'tous';

// Charger les échanges selon le filtre
function filterEchanges(filter) {
    currentFilter = filter;
    
    // Mettre à jour les boutons actifs (matching by data-filter attribute)
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.filter === filter);
    });
    
    let url = `/api/echanges/user/${userId}`;
    
    switch(filter) {
        case 'envoyees':
            url = `/api/echanges/user/${userId}/envoyees`;
            break;
        case 'recues':
            url = `/api/echanges/user/${userId}/recues`;
            break;
        case 'attente':
            url = `/api/echanges/user/${userId}/status/1`;
            break;
        case 'refuse':
            url = `/api/echanges/user/${userId}/status/2`;
            break;
        case 'accepte':
            url = `/api/echanges/user/${userId}/status/3`;
            break;
    }
    
    fetch(url)
        .then(res => res.json())
        .then(data => {
            displayEchanges(data);
        })
        .catch(err => {
            console.error('Erreur:', err);
            showMessage('Erreur lors du chargement des échanges', 'error');
        });
}

// Afficher les échanges dans le tableau
function displayEchanges(echanges) {
    const tbody = document.getElementById('echangeTable');
    
    if (!echanges || echanges.length === 0) {
        tbody.innerHTML = '<tr><td colspan="9" class="empty-message text-dark fw-bold">Aucun échange trouvé</td></tr>';
        return;
    }
    
    let html = "";
    echanges.forEach(e => {
        const statusClass = e.status_id == 1 ? 'status-attente' : (e.status_id == 2 ? 'status-refuse' : 'status-accepte');
        const canAction = e.status_id == 1;
        
        html += `
            <tr>
                <td class="text-dark fw-bold">${e.id}</td>
                <td class="text-dark"><strong>${e.produit1 || 'N/A'}</strong></td>
                <td class="text-dark"><strong>${e.produit2 || 'N/A'}</strong></td>
                <td class="text-dark">${e.user1 || 'N/A'}</td>
                <td class="text-dark">${e.user2 || 'N/A'}</td>
                <td><span class="status ${statusClass}">${e.etat || 'N/A'}</span></td>
                <td class="text-dark">${e.date_envoie || '-'}</td>
                <td class="text-dark">${e.date_acceptation || '-'}</td>
                <td class="actions-cell">
                    ${canAction ? `
                        <div class="d-flex gap-2 justify-content-center">
                            <button class="btn btn-success btn-sm accept" onclick="updateStatus(${e.id}, 3)">
                                <i class="bi bi-check"></i> Accepter
                            </button>
                            <button class="btn btn-danger btn-sm refuse" onclick="updateStatus(${e.id}, 2)">
                                <i class="bi bi-x"></i> Refuser
                            </button>
                        </div>
                    ` : '<span class="text-muted">---</span>'}
                </td>
            </tr>
        `;
    });
    tbody.innerHTML = html;
}

// Mettre à jour le statut d'un échange
function updateStatus(echangeId, statusId) {
    if (!confirm('Confirmer : ' + (statusId === 3 ? 'Accepter' : 'Refuser') + ' cet échange ?')) {
        return;
    }
    
    fetch(`/api/echanges/${echangeId}/status`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `status_id=${statusId}`
    })
    .then(res => res.json())
    .then(res => {
        if(res.success){
            filterEchanges(currentFilter);
        } else {
            alert('Erreur !');
        }
    })
    .catch(err => {
        console.error('Erreur:', err);
        alert('Erreur serveur');
    });
}

// Charger les produits
function loadProduits() {
    fetch(`/api/produits`)
        .then(res => res.json())
        .then(data => {
            // Remplir les selects
            const select1 = document.getElementById('produit1_id');
            const select2 = document.getElementById('produit2_id');
            
            data.forEach(p => {
                const option1 = new Option(`${p.nom} (${p.categorie || 'N/A'}) - User: ${p.username || p.user_id}`, p.id);
                const option2 = option1.cloneNode(true);
                select1.add(option1);
                select2.add(option2);
            });
            
            // Afficher la grille
            displayProduitsGrid(data);
        })
        .catch(err => console.error('Erreur produits:', err));
}

// Afficher la grille des produits
function displayProduitsGrid(produits) {
    const grid = document.getElementById('produitsGrid');
    let html = '';
    
    produits.forEach(p => {
        html += `
            <div class="produit-card">
                <h4>${p.nom}</h4>
                <p><strong>Catégorie:</strong> ${p.categorie || 'N/A'}</p>
                <p><strong>Propriétaire:</strong> ${p.username || 'User #' + p.user_id}</p>
                <p><strong>ID:</strong> ${p.id}</p>
                <p style="font-size: 12px; color: #999;">${p.description || 'Pas de description'}</p>
            </div>
        `;
    });
    
    grid.innerHTML = html || '<p class="empty-message">Aucun produit disponible</p>';
}

// Créer un nouvel échange
function createEchange() {
    const produit1_id = document.getElementById('produit1_id').value;
    const produit2_id = document.getElementById('produit2_id').value;
    const user2_id = document.getElementById('user2_id').value;
    
    if (!produit1_id || !produit2_id || !user2_id) {
        showMessage('Veuillez remplir tous les champs', 'error');
        return;
    }
    
    const params = new URLSearchParams();
    params.append('produit1_id', produit1_id);
    params.append('produit2_id', produit2_id);
    params.append('user1_id', userId);
    params.append('user2_id', user2_id);
    params.append('status_id', 1); // statut "en attente"

    fetch(`/api/echanges`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: params.toString()
    })
    .then(res => res.json())
    .then(res => {
        if(res && res.success){
            showMessage("Demande d'échange créée avec succès !", 'success');
            // Reset form
            document.getElementById('produit1_id').value = '';
            document.getElementById('produit2_id').value = '';
            document.getElementById('user2_id').value = '';
            // Recharger
            filterEchanges(currentFilter);
        } else {
            showMessage("Erreur lors de la création de l'échange", 'error');
        }
    })
    .catch(err => {
        console.error('Erreur:', err);
        showMessage('Erreur serveur', 'error');
    });
}

// Afficher un message
function showMessage(text, type) {
    const container = document.getElementById('messageContainer');
    const div = document.createElement('div');
    div.className = `message message-${type}`;
    div.textContent = text;
    container.innerHTML = '';
    container.appendChild(div);
    
    setTimeout(() => {
        div.remove();
    }, 5000);
}

// Chargement initial
document.addEventListener('DOMContentLoaded', () => {
    filterEchanges('tous');
    loadProduits();
});
