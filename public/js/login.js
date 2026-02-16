document.addEventListener('DOMContentLoaded', () => {
  const form = document.querySelector('#loginForm');
  if (!form) return;

  const statusBox = document.querySelector('#formStatus');

  const map = {
    username: { input: '#username', err: '#usernameError' },
    email: { input: '#email', err: '#emailError' },
    password: { input: '#password', err: '#passwordError' },
  }; 

  function setStatus(type, msg) {
    if (!statusBox) return;
    if (!msg) {
      statusBox.className = 'alert d-none';
      statusBox.textContent = '';
      return;
    } 
    statusBox.className = `alert alert-${type}`;
    statusBox.textContent = msg;
  }

  function clearFeedback() {
    Object.keys(map).forEach((k) => {
      const input = document.querySelector(map[k].input);
      const err = document.querySelector(map[k].err);
      if (!input) return;
      input.classList.remove('is-invalid', 'is-valid');
      if (err) err.textContent = '';
    });
    setStatus(null, '');
  }

  function applyServerResult(data) {
    Object.keys(map).forEach((k) => {
      const input = document.querySelector(map[k].input);
      const err = document.querySelector(map[k].err);
      if (!input) return;
      const msg = data.errors?.[k] || '';

      if (msg) {
        input.classList.add('is-invalid');
        input.classList.remove('is-valid');
        if (err) err.textContent = msg;
      } else {
        input.classList.remove('is-invalid');
        input.classList.add('is-valid');
        if (err) err.textContent = '';
      }
    });

    if (data.errors?._global) {
      setStatus('warning', data.errors._global);
    }
  } 

  async function callValidate() {
    const fd = new FormData(form);

    const res = await fetch('/logForm', {
      method: 'POST',
      body: fd,
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
    }); 

    // On lit toujours la réponse
    const data = await res.json();

    // Si le serveur a répondu avec une erreur
    if (!res.ok) {
      throw new Error(data.message || 'Erreur serveur');
    }

    return data;
  }


  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    clearFeedback();

    try {
      const data = await callValidate();
      applyServerResult(data);

      if (data.ok) { 
        setStatus('success', 'Connexion réussie, redirection...');
        window.location.href = '/produits';
      } else { 
        setStatus('danger', 'Veuillez corriger les erreurs.');
      }
    } catch (err) {
      setStatus('warning', err.message || 'Une erreur est survenue.');
    }
  }); 

  // Validation au blur pour un retour rapide
  Object.keys(map).forEach((k) => {
    const input = document.querySelector(map[k].input);
    if (!input) return;
    input.addEventListener('blur', async () => {
      try {
        const data = await callValidate();
        applyServerResult(data);
      } catch (_) {
        /* ignore blur errors */
      }
    });
  });
});

