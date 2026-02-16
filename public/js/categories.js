document.addEventListener('DOMContentLoaded', () => {
  const ECFG = window.CATEGORIES_CONFIG || {};
  const BASE_URL = (typeof ECFG.BASE_URL !== 'undefined') ? ECFG.BASE_URL : (window.BASE_URL || '');

  const form = document.getElementById('categorieForm');
  const catTableBody = document.getElementById('catTableBody');
  const cancelBtn = document.getElementById('cancelEdit');
  const saveBtn = document.getElementById('saveCat');
  const publicGrid = document.getElementById('publicCategoryGrid');

  async function fetchCategories() {
    const res = await fetch(`${BASE_URL}/api/categories`);
    return res.ok ? res.json() : [];
  }

  function renderPublicGrid(categories) {
    if (!publicGrid) return;
    publicGrid.innerHTML = categories.map(cat => `
      <article class="cat-card" data-id="${cat.id}">
        <a class="cat-link" href="${BASE_URL}/produits/${cat.id}">
          <div class="cat-icon-wrap">
            ${cat.icon ? `<img class=\"cat-icon\" src=\"${BASE_URL}/public/images/${cat.icon}\" alt=\"${escapeHtml(cat.nom)}\">` : `<div class=\"cat-fallback\">${escapeHtml(cat.nom.charAt(0).toUpperCase())}</div>`}
          </div>
          <div class="cat-body">
            <h2 class="cat-title">${escapeHtml(cat.nom)}</h2>
            <p class="cat-meta">Voir les produits</p>
          </div>
        </a>
      </article>
    `).join('');
  }

  function renderTable(categories) {
    catTableBody.innerHTML = categories.map(cat => `
      <tr data-id="${cat.id}">
        <td>${cat.id}</td>
        <td>${escapeHtml(cat.nom)}</td>
        <td>${cat.icon ? `<img src=\"${BASE_URL}/public/images/${cat.icon}\" style=\"height:28px;object-fit:contain\">` : ''}</td>
        <td>
          <button class="editBtn">Éditer</button>
          <button class="deleteBtn">Supprimer</button>
        </td>
      </tr>
    `).join('');
  }

  function escapeHtml(s){ return String(s||'').replace(/[&<>\"']/g, c=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":"&#39;" }[c])); }

  async function loadAndRender(){
    const cats = await fetchCategories();
    renderTable(cats);
    renderPublicGrid(cats);
  }

  async function addCategory(payload){
    const res = await fetch(`${BASE_URL}/api/categories`, {method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(payload)});
    return res.json();
  }

  async function updateCategory(id, payload){
    const res = await fetch(`${BASE_URL}/api/categories/${id}`, {method:'PUT',headers:{'Content-Type':'application/json'},body:JSON.stringify(payload)});
    return res.json();
  }

  async function deleteCategory(id){
    const res = await fetch(`${BASE_URL}/api/categories/${id}`, {method:'DELETE'});
    return res.json();
  }

  catTableBody.addEventListener('click', async (e) => {
    const tr = e.target.closest('tr');
    if (!tr) return;
    const id = tr.dataset.id;

    if (e.target.classList.contains('editBtn')){
      // load into form
      const cells = tr.querySelectorAll('td');
      document.getElementById('catId').value = id;
      document.getElementById('catNom').value = cells[1].textContent.trim();
      const img = tr.querySelector('td img');
      document.getElementById('catIcon').value = img ? img.src.split('/').pop() : '';
      saveBtn.textContent = 'Mettre à jour';
      cancelBtn.style.display = 'inline-block';
      window.scrollTo({top:0,behavior:'smooth'});
    }

    if (e.target.classList.contains('deleteBtn')){
      if (!confirm('Supprimer cette catégorie ?')) return;
      const result = await deleteCategory(id);
      if (result.success) await loadAndRender();
      else alert('Erreur lors de la suppression');
    }
  });

  cancelBtn.addEventListener('click', () => {
    form.reset();
    document.getElementById('catId').value = '';
    saveBtn.textContent = 'Ajouter';
    cancelBtn.style.display = 'none';
  });

  form.addEventListener('submit', async (ev) => {
    ev.preventDefault();
    const id = document.getElementById('catId').value;
    const payload = {
      nom: document.getElementById('catNom').value.trim(),
      icon: document.getElementById('catIcon').value.trim()
    };

    if (!payload.nom) { alert('Nom requis'); return; }

    if (id) {
      const res = await updateCategory(id, payload);
      if (res.success){
        form.reset();
        document.getElementById('catId').value = '';
        saveBtn.textContent = 'Ajouter';
        cancelBtn.style.display = 'none';
        await loadAndRender();
      } else alert('Erreur mise à jour');
    } else {
      const res = await addCategory(payload);
      if (res.success){
        form.reset();
        await loadAndRender();
      } else alert('Erreur ajout');
    }
  });

  // initial load
  loadAndRender();
});