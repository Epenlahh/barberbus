document.addEventListener('DOMContentLoaded', () => {
  const user = JSON.parse(localStorage.getItem('bb_user') || 'null');
  const token = localStorage.getItem('bb_token') || '';

  if (!user || !token) {
    if (window.location.pathname.includes('/user/')) {
      window.location.href = '../login.php';
    }
    return;
  }

  document.querySelectorAll('[data-prefill="name"]').forEach(el => {
    if (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA') {
      el.value = user.name || '';
      if (el.name === 'clientName') el.readOnly = true;
    }
  });
  document.querySelectorAll('[data-prefill="email"]').forEach(el => {
    if (el.tagName === 'INPUT') {
      el.value = user.email || '';
      if (el.name === 'clientEmail') el.readOnly = true;
    }
  });
  document.querySelectorAll('[data-prefill="phone"]').forEach(el => {
    if (el.tagName === 'INPUT') {
      el.value = user.phone || '';
    }
  });
  const tokenInput = document.getElementById('authToken');
  if (tokenInput) tokenInput.value = token;
});
