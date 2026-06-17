// ========================
// BARBERBUS – AUTH JS
// ========================

// Toggle between Login / Register tabs
const loginTab = document.getElementById('loginTab');
const registerTab = document.getElementById('registerTab');
const loginForm = document.getElementById('loginForm');
const registerForm = document.getElementById('registerForm');

if (loginTab && registerTab) {
  loginTab.addEventListener('click', () => {
    loginTab.classList.add('active');
    registerTab.classList.remove('active');
    loginForm.classList.remove('hidden');
    registerForm.classList.add('hidden');
  });

  registerTab.addEventListener('click', () => {
    registerTab.classList.add('active');
    loginTab.classList.remove('active');
    registerForm.classList.remove('hidden');
    loginForm.classList.add('hidden');
  });
}

// Toggle password visibility
function togglePass(id, icon) {
  const input = document.getElementById(id);
  if (!input) return;
  if (input.type === 'password') {
    input.type = 'text';
    icon.classList.replace('fa-eye', 'fa-eye-slash');
  } else {
    input.type = 'password';
    icon.classList.replace('fa-eye-slash', 'fa-eye');
  }
}

// Handle Login
function handleLogin(e) {
  e.preventDefault();
  const emailEl = document.querySelector('#loginForm input[type="email"]');
  const passEl = document.getElementById('loginPass');
  if (!emailEl || !passEl) return;

  const email = emailEl.value.trim();
  const pass = passEl.value.trim();

  if (!email || !pass) {
    showToast('Please fill in all fields.', 'error');
    return;
  }
  if (!validateEmail(email)) {
    showToast('Please enter a valid email.', 'error');
    return;
  }

  // Simulate login success (in real app, POST to PHP backend)
  showToast('Login successful! Redirecting...', 'success');
  setTimeout(() => {
    window.location.href = 'index.html';
  }, 1500);
}

// Handle Register
function handleRegister(e) {
  e.preventDefault();
  const inputs = document.querySelectorAll('#registerForm input');
  let allFilled = true;
  inputs.forEach(i => { if (!i.value.trim()) allFilled = false; });

  if (!allFilled) {
    showToast('Please fill in all fields.', 'error');
    return;
  }

  const email = document.querySelector('#registerForm input[type="email"]').value;
  if (!validateEmail(email)) {
    showToast('Please enter a valid email.', 'error');
    return;
  }

  const pass = document.getElementById('regPass').value;
  if (pass.length < 6) {
    showToast('Password must be at least 6 characters.', 'error');
    return;
  }

  // Simulate registration (in real app, POST to PHP backend)
  showToast('Account created! Please log in.', 'success');
  setTimeout(() => {
    loginTab.click();
  }, 1500);
}

function validateEmail(email) {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}
