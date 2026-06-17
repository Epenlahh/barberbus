// ============================================
// BARBERBUS – REAL AUTH JS (connects to PHP API)
// ============================================

const API_BASE = 'api';

const loginTab    = document.getElementById('loginTab');
const registerTab = document.getElementById('registerTab');
const loginForm   = document.getElementById('loginForm');
const registerForm= document.getElementById('registerForm');

if (loginTab && registerTab) {
  loginTab.addEventListener('click', () => {
    loginTab.classList.add('active');    registerTab.classList.remove('active');
    loginForm.classList.remove('hidden'); registerForm.classList.add('hidden');
  });
  registerTab.addEventListener('click', () => {
    registerTab.classList.add('active');  loginTab.classList.remove('active');
    registerForm.classList.remove('hidden'); loginForm.classList.add('hidden');
  });
}

function togglePass(id, icon) {
  const input = document.getElementById(id);
  if (!input) return;
  if (input.type === 'password') {
    input.type = 'text'; icon.classList.replace('fa-eye','fa-eye-slash');
  } else {
    input.type = 'password'; icon.classList.replace('fa-eye-slash','fa-eye');
  }
}

async function handleLogin(e) {
  e.preventDefault();
  const email = document.querySelector('#loginForm input[type="email"]')?.value.trim();
  const pass  = document.getElementById('loginPass')?.value;
  if (!email || !pass) { showToast('Please fill in all fields.','error'); return; }
  const btn = e.target.closest('form')?.querySelector('.btn-primary') || document.querySelector('#loginForm .btn-primary');
  if(btn){ btn.textContent='Logging in...'; btn.disabled=true; }
  try {
    const res  = await fetch(`${API_BASE}/auth.php?action=login`,{ method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({email,password:pass}) });
    const data = await res.json();
    if (data.success) {
      localStorage.setItem('bb_token', data.data.token);
      localStorage.setItem('bb_user', JSON.stringify(data.data.user));
      showToast('Login successful!', 'success');
      setTimeout(() => { window.location.href = data.data.user.role==='admin' ? 'admin/index.php' : 'user/my_bookings.php'; }, 1200);
    } else {
      showToast(data.message || 'Login failed.','error');
      if(btn){ btn.textContent='Login'; btn.disabled=false; }
    }
  } catch(err) {
    showToast('Connection error. Ensure PHP server is running.','error');
    if(btn){ btn.textContent='Login'; btn.disabled=false; }
  }
}

async function handleRegister(e) {
  e.preventDefault();
  const name  = document.querySelector('#registerForm input[type="text"]')?.value.trim();
  const phone = document.querySelector('#registerForm input[type="tel"]')?.value.trim();
  const email = document.querySelector('#registerForm input[type="email"]')?.value.trim();
  const pass  = document.getElementById('regPass')?.value;
  if (!name||!email||!pass){ showToast('Please fill in all fields.','error'); return; }
  if (pass.length < 6){ showToast('Password must be at least 6 characters.','error'); return; }
  const btn = e.target.closest('form')?.querySelector('.btn-primary') || document.querySelector('#registerForm .btn-primary');
  if(btn){ btn.textContent='Creating...'; btn.disabled=true; }
  try {
    const res  = await fetch(`${API_BASE}/auth.php?action=register`,{ method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({name,email,phone,password:pass}) });
    const data = await res.json();
    if (data.success) { showToast('Account created! Please log in.','success'); setTimeout(()=>{ loginTab?.click(); },1500); }
    else showToast(data.message||'Registration failed.','error');
  } catch(err) { showToast('Connection error.','error'); }
  finally { if(btn){ btn.textContent='Create Account'; btn.disabled=false; } }
}
