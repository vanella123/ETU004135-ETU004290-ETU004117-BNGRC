document.addEventListener('DOMContentLoaded', () => {
  const form = document.querySelector('#registerForm');
  if (!form) return;
  const statusBox = document.querySelector('#formStatus');
  const map = {
    username: { input: '#username', err: '#usernameError' },
    email: { input: '#email', err: '#emailError' },
    password: { input: '#password', err: '#passwordError' },
    confirm_password: { input: '#confirm_password', err: '#confirmPasswordError' },
  };

  function setStatus(type, msg) {
    if (!statusBox) return;
``
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

  async function callRegister() {
    const fd = new FormData(form);

    const res = await fetch('/inscription', {
      method: 'POST',
      body: fd,
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
    });

    const data = await res.json();

    if (!res.ok) {
      throw new Error(data.message || 'Erreur serveur');
    }

    return data;
  }

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    clearFeedback();

    try {
      const data = await callRegister();
      applyServerResult(data);

      if (data.ok) {
        setStatus('success', 'Inscription réussie, redirection...');
        setTimeout(() => {
          window.location.href = '/produits';
        }, 1200);
      } else {
        setStatus('danger', 'Veuillez corriger les erreurs.');
      }

    } catch (err) {
      setStatus('warning', err.message || 'Une erreur est survenue.');
    } 
  });

  // Validation au blur
  Object.keys(map).forEach((k) => {
    const input = document.querySelector(map[k].input);
    if (!input) return;

    input.addEventListener('blur', async () => {
      try {
        const data = await callRegister();
        applyServerResult(data);
      } catch (_) {
        // ignore blur errors
      }
    });
  });
}); 