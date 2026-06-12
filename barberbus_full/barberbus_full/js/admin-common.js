/**
 * BARBERBUS – ADMIN COMMON JS
 */

const API = '../api'; // Path to API folder
let token = localStorage.getItem('bb_token');

// Instant Auth Guard
checkAdminAuth();

function checkAdminAuth() {
  const user = JSON.parse(localStorage.getItem('bb_user'));
  if (!token || !user || user.role !== 'admin') {
    window.location.href = '../login.php';
    return;
  }
}

async function verifyAdmin() {
  try {
    const res = await api(`${API}/auth.php?action=me`);
    if (!res.success || res.data.user.role !== 'admin') {
      alert('Session Issue: ' + (res.message || 'Unauthorized role'));
      window.location.href = '../login.php';
      return;
    }
    const u = res.data.user;
    const nameEl = document.getElementById('adminName');
    const avatarEl = document.getElementById('adminAvatar');
    if (nameEl) nameEl.textContent = u.name;
    if (avatarEl) avatarEl.textContent = u.name.charAt(0).toUpperCase();
  } catch(e) { 
    console.error('VerifyAdmin Error:', e);
    window.location.href = '../login.php'; 
  }
}

// ── UTILITIES ──
async function api(url, method = 'GET', body = null) {
  const opts = { method, headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${token}` } };
  if (body) opts.body = JSON.stringify(body);
  
  try {
    const r = await fetch(url, opts);
    if (r.status === 401) { logout(); return {success:false, message:'Unauthorized'}; }
    
    const text = await r.text();
    try {
      return JSON.parse(text);
    } catch (e) {
      console.error('Non-JSON response:', text);
      return { success: false, message: 'Server error: Invalid response' };
    }
  } catch (err) {
    return { success: false, message: 'Network error' };
  }
}

function closeModal(id) { 
    const el = document.getElementById(id);
    if (el) el.classList.remove('open'); 
}

function filterTable(inputId, tableId) {
  const input = document.getElementById(inputId);
  if (!input) return;
  const q = input.value.toLowerCase();
  document.querySelectorAll(`#${tableId} tr`).forEach(row => {
    row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
  });
}

function fmtDate(d) {
  if (!d) return '–';
  return new Date(d).toLocaleDateString('en-MY', { day:'numeric', month:'short', year:'numeric' });
}
function fmtTime(t) {
  if (!t) return '–';
  const [h,m] = t.split(':');
  const hr = parseInt(h), ap = hr >= 12 ? 'PM' : 'AM';
  return `${hr > 12 ? hr-12 : hr || 12}:${m} ${ap}`;
}

function esc(str) {
  if (!str) return '';
  const p = document.createElement('p');
  p.textContent = str;
  return p.innerHTML;
}

function logout() {
  localStorage.removeItem('bb_token');
  localStorage.removeItem('bb_user');
  window.location.href = '../login.php';
}

function showToast(msg, type = 'success') {
  const el = document.getElementById('toast');
  if (!el) return;
  el.textContent = msg;
  el.style.background = type === 'success' ? 'rgba(64,200,120,0.9)' : 'rgba(224,82,82,0.9)';
  el.style.color = '#fff';
  el.style.opacity = '1';
  el.style.transform = 'translateY(0)';
  setTimeout(() => { el.style.opacity = '0'; el.style.transform = 'translateY(10px)'; }, 3000);
}