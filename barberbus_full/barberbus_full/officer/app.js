// ================================================
// BARBERBUS – OFFICER DASHBOARD JS
// Real-time polling, queue management, walk-ins
// ================================================

const API = 'api.php';
let token = localStorage.getItem('bb_officer_token');
let officer = null;
let pollTimer = null;
let notifTimer = null;
let clockTimer = null;
let lastNotifTs = Math.floor(Date.now() / 1000);
let servicesCache = [];
let barbersCache  = [];
let queueCache    = [];
let notifCache    = [];
let sidebarCollapsed = false;
let currentPanel = 'overview';

// ── INIT ──
document.addEventListener('DOMContentLoaded', () => {
  if (!token) { showLogin(); return; }
  initOfficer(window.defaultOfficerPanel || 'overview');
});

async function initOfficer(defaultPanel = 'overview') {
  try {
    // Verify token by fetching stats
    await Promise.all([loadStats(), loadQueue(), loadServicesBarbers()]);
    // Read stored officer info
    const raw = localStorage.getItem('bb_officer');
    if (raw) {
      officer = JSON.parse(raw);
      document.getElementById('officerName').textContent   = officer.name || 'Officer';
      document.getElementById('officerAvatar').textContent = (officer.name || 'O').charAt(0).toUpperCase();
      document.getElementById('officerRole').textContent   = officer.role === 'admin' ? 'Admin Officer' : 'Officer';
    }
    startClock();
    startPolling();
    startNotifPolling();
    showPanel(defaultPanel);
    hideLogin();
  } catch(e) {
    console.error(e);
    showLogin();
  }
}

// ════════════════════════════════════════════
// LOGIN
// ════════════════════════════════════════════
function showLogin() {
  document.getElementById('loginScreen').style.display = 'flex';
  document.getElementById('app').style.display = 'none';
}
function hideLogin() {
  document.getElementById('loginScreen').style.display = 'none';
  document.getElementById('app').style.display = 'flex';
}

document.getElementById('loginForm')?.addEventListener('submit', async (e) => {
  e.preventDefault();
  const email = document.getElementById('loginEmail').value.trim();
  const pass  = document.getElementById('loginPass').value;
  const btn   = document.getElementById('loginBtn');
  btn.textContent = 'Signing in...'; btn.disabled = true;
  try {
    const res = await fetch(`${API}?action=login`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ email, password: pass })
    });
    const data = await res.json();
    if (data.success) {
      token   = data.data.token;
      officer = data.data.user;
      localStorage.setItem('bb_officer_token', token);
      localStorage.setItem('bb_officer', JSON.stringify(officer));
      initDashboard();
    } else {
      document.getElementById('loginError').textContent = data.message;
      document.getElementById('loginError').style.display = 'block';
    }
  } catch(err) {
    document.getElementById('loginError').textContent = 'Connection failed. Check server.';
    document.getElementById('loginError').style.display = 'block';
  } finally {
    btn.textContent = 'Sign In'; btn.disabled = false;
  }
});

function logout() {
  clearInterval(pollTimer); clearInterval(notifTimer); clearInterval(clockTimer);
  localStorage.removeItem('bb_officer_token');
  localStorage.removeItem('bb_officer');
  token = null; officer = null;
  showLogin();
}

// ════════════════════════════════════════════
// API CALL HELPER
// ════════════════════════════════════════════
async function apiFetch(action, method = 'GET', body = null) {
  const opts = {
    method,
    headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${token}` }
  };
  if (body) opts.body = JSON.stringify(body);
  const res  = await fetch(`${API}?action=${action}`, opts);
  const data = await res.json();
  return data;
}

// ════════════════════════════════════════════
// CLOCK
// ════════════════════════════════════════════
function startClock() {
  function tick() {
    const now  = new Date();
    const hms  = now.toLocaleTimeString('en-MY', { hour:'2-digit', minute:'2-digit', second:'2-digit', hour12: false });
    const date = now.toLocaleDateString('en-MY', { weekday:'long', day:'numeric', month:'long', year:'numeric' });
    document.querySelectorAll('.clock-display').forEach(el => el.textContent = hms);
    document.querySelectorAll('.date-display').forEach(el => el.textContent = date);
  }
  tick();
  clockTimer = setInterval(tick, 1000);
}

// ════════════════════════════════════════════
// REAL-TIME POLLING  (every 12s)
// ════════════════════════════════════════════
function startPolling() {
  pollTimer = setInterval(async () => {
    await Promise.all([loadStats(), loadQueue()]);
    if (currentPanel === 'schedule') loadSchedule();
  }, 12000);
}

function startNotifPolling() {
  notifTimer = setInterval(checkNotifications, 15000);
}

async function manualRefresh() {
  const btn = document.getElementById('refreshBtn');
  btn?.classList.add('spinning');
  await Promise.all([loadStats(), loadQueue()]);
  if (currentPanel === 'schedule') loadSchedule();
  setTimeout(() => btn?.classList.remove('spinning'), 600);
  toast('Refreshed', 'info');
}

// ════════════════════════════════════════════
// STATS
// ════════════════════════════════════════════
async function loadStats() {
  const res = await apiFetch('stats');
  if (!res.success) return;
  const d = res.data;

  setText('statTotal',    d.today_total);
  setText('statAllTime',  d.total_bookings);
  setText('statDone',     d.today_done);
  setText('statRevenue',  'RM ' + parseFloat(d.today_revenue).toFixed(0));
  setText('statPending',  d.pending);
  setText('statConfirmed',d.confirmed);
  setText('statRemaining',d.remaining);

  // Next customer banner
  const nb = document.getElementById('nextBanner');
  if (d.next_customer && nb) {
    nb.style.display = 'flex';
    setText('nextName',    d.next_customer.customer_name);
    setText('nextService', d.next_customer.service_name);
    setText('nextBarber',  d.next_customer.barber_name || 'Any barber');
    setText('nextTime',    fmtTime(d.next_customer.booking_time));
  } else if (nb) {
    nb.style.display = 'none';
  }

  // Barber utilisation
  renderBarberUtil(d.barber_util || []);
}

function renderBarberUtil(barbers) {
  const el = document.getElementById('barberUtil');
  if (!el) return;
  if (!barbers.length) { el.innerHTML = '<div class="empty-state"><i class="fas fa-user-slash"></i><p>No barbers found</p></div>'; return; }
  const max = Math.max(...barbers.map(b => parseInt(b.jobs_today) || 0), 1);
  el.innerHTML = barbers.map(b => {
    const pct = Math.round(((b.jobs_today || 0) / max) * 100);
    return `<div class="barber-util-card">
      <div class="b-avatar">${b.name.charAt(0)}</div>
      <div style="flex:1;min-width:0">
        <div class="b-name">${b.name}</div>
        <div class="b-stats">${b.done_today || 0}/${b.jobs_today || 0} done today</div>
        <div class="b-bar" style="margin-top:4px"><div class="b-fill" style="width:${pct}%"></div></div>
      </div>
      <div class="b-count">${b.jobs_today || 0}</div>
    </div>`;
  }).join('');
}

// ════════════════════════════════════════════
// QUEUE
// ════════════════════════════════════════════
async function loadQueue() {
  const res = await apiFetch('queue');
  if (!res.success) return;
  queueCache = res.data.queue || [];

  // Update queue badge
  const badge = document.getElementById('queueBadge');
  if (badge) { badge.textContent = queueCache.length; badge.style.display = queueCache.length ? 'inline' : 'none'; }

  renderQueue(queueCache);
  renderQueueTable(queueCache);
}

function renderQueue(list) {
  const el = document.getElementById('liveQueue');
  if (!el) return;
  if (!list.length) {
    el.innerHTML = `<div class="empty-state"><i class="fas fa-calendar-check"></i><p>Queue is clear for now</p></div>`;
    return;
  }
  el.innerHTML = list.map((b, i) => {
    const isNext = i === 0;
    const cls    = isNext ? 'next' : (b.status === 'confirmed' ? 'in-progress' : '');
    return `<div class="queue-item ${cls}">
      <div class="q-num">${i+1}</div>
      <div class="q-time">${fmtTime(b.booking_time)}</div>
      <div class="q-info">
        <div class="q-name">${esc(b.customer_name)}</div>
        <div class="q-service">${esc(b.service_name)} · ${b.duration}min</div>
        ${b.barber_name ? `<div class="q-barber">✂ ${esc(b.barber_name)}</div>` : ''}
      </div>
      <div class="q-price">RM${parseFloat(b.total_price).toFixed(0)}</div>
      <div class="q-actions">
        ${b.status === 'pending'   ? `<button class="q-btn confirm" onclick="updateStatus(${b.id},'confirmed')">Confirm</button>` : ''}
        ${b.status === 'confirmed' ? `<button class="q-btn done"    onclick="updateStatus(${b.id},'completed')">Done ✓</button>` : ''}
        <button class="q-btn cancel" onclick="updateStatus(${b.id},'cancelled')">✕</button>
      </div>
    </div>`;
  }).join('');
}

function renderQueueTable(list) {
  const tbody = document.getElementById('allBookingsBody');
  if (!tbody) return;
  if (!list.length) {
    tbody.innerHTML = `<tr><td colspan="8" class="empty-state" style="text-align:center;padding:2rem;color:var(--muted)">No active bookings today</td></tr>`;
    return;
  }
  tbody.innerHTML = list.map((b, i) => `
    <tr>
      <td class="mono text-muted">#${b.id}</td>
      <td>
        <div class="fw6 text-white">${esc(b.customer_name)}</div>
        <div class="text-muted" style="font-size:0.7rem">${esc(b.customer_phone || '–')}</div>
      </td>
      <td>${esc(b.service_name)}<br/><span class="text-muted" style="font-size:0.7rem">${b.duration}min</span></td>
      <td>${b.barber_name ? esc(b.barber_name) : '<span class="text-muted">Any</span>'}</td>
      <td class="mono">${fmtTime(b.booking_time)}</td>
      <td class="mono text-gold">RM${parseFloat(b.total_price).toFixed(0)}</td>
      <td><span class="badge badge-${b.status}">${b.status}</span></td>
      <td>
        <div style="display:flex;gap:0.4rem">
          ${b.status==='pending'   ? `<button class="btn btn-ghost btn-sm" onclick="updateStatus(${b.id},'confirmed')">Confirm</button>` : ''}
          ${b.status==='confirmed' ? `<button class="btn btn-green btn-sm" onclick="updateStatus(${b.id},'completed')">Done</button>` : ''}
          ${['pending','confirmed'].includes(b.status) ? `<button class="btn btn-danger btn-sm" onclick="updateStatus(${b.id},'cancelled')">Cancel</button>` : ''}
        </div>
      </td>
    </tr>
  `).join('');
}

async function updateStatus(id, status) {
  const res = await apiFetch('update-status', 'POST', { booking_id: id, status });
  if (res.success) {
    toast(res.message, 'success');
    await Promise.all([loadQueue(), loadStats()]);
  } else {
    toast(res.message, 'error');
  }
}

// ════════════════════════════════════════════
// SCHEDULE (today timeline)
// ════════════════════════════════════════════
async function loadSchedule() {
  const res = await apiFetch('today');
  if (!res.success) return;
  const schedule = res.data.schedule || [];
  renderScheduleTimeline(schedule);
  renderScheduleTable(schedule);
}

function renderScheduleTimeline(list) {
  const el = document.getElementById('timelineView');
  if (!el) return;
  if (!list.length) { el.innerHTML = '<div class="empty-state"><i class="fas fa-calendar"></i><p>No bookings today</p></div>'; return; }
  const now = new Date();
  el.innerHTML = '<div class="timeline">' + list.map(b => {
    const bTime = new Date(`${b.booking_date}T${b.booking_time}`);
    const isPast  = b.status === 'completed' || bTime < now;
    const isNext  = b.status === 'confirmed' && bTime >= now;
    const cls = b.status === 'completed' ? 'done' : (isNext ? 'next' : 'future');
    return `<div class="tl-item ${cls}">
      <div class="tl-time">${fmtTime(b.booking_time)}</div>
      <div class="tl-name">${esc(b.customer_name)} <span style="font-weight:400;color:var(--muted)">– ${esc(b.service_name)}</span></div>
      <div class="tl-meta">${b.barber_name ? '✂ '+esc(b.barber_name)+' · ' : ''}${b.duration}min · RM${parseFloat(b.total_price).toFixed(0)} · <span class="badge badge-${b.status}">${b.status}</span></div>
    </div>`;
  }).join('') + '</div>';
}

function renderScheduleTable(list) {
  const tbody = document.getElementById('scheduleBody');
  if (!tbody) return;
  if (!list.length) { tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:2rem;color:var(--muted)">No bookings today</td></tr>'; return; }
  tbody.innerHTML = list.map(b => `
    <tr>
      <td class="mono fw6">${fmtTime(b.booking_time)}</td>
      <td><div class="fw6 text-white">${esc(b.customer_name)}</div><div class="text-muted" style="font-size:0.7rem">${esc(b.customer_phone||'–')}</div></td>
      <td>${esc(b.service_name)}</td>
      <td>${b.barber_name ? esc(b.barber_name) : '<span class="text-muted">Any</span>'}</td>
      <td class="mono">${b.duration} min</td>
      <td class="mono text-gold">RM${parseFloat(b.total_price).toFixed(0)}</td>
      <td><span class="badge badge-${b.status}">${b.status}</span></td>
      <td>
        <div style="display:flex;gap:0.4rem">
          ${b.status==='pending'   ? `<button class="btn btn-ghost btn-sm" onclick="updateStatus(${b.id},'confirmed')">Confirm</button>` : ''}
          ${b.status==='confirmed' ? `<button class="btn btn-green btn-sm" onclick="updateStatus(${b.id},'completed')">Done</button>` : ''}
          ${['pending','confirmed'].includes(b.status) ? `<button class="btn btn-danger btn-sm" onclick="updateStatus(${b.id},'cancelled')">Cancel</button>` : ''}
        </div>
      </td>
    </tr>
  `).join('');
}

// ════════════════════════════════════════════
// WALK-IN
// ════════════════════════════════════════════
async function loadServicesBarbers() {
  const [sRes, bRes] = await Promise.all([
    fetch('../api/services.php', { headers: { Authorization: `Bearer ${token}` } }).then(r=>r.json()),
    fetch('../api/services.php?type=barbers', { headers: { Authorization: `Bearer ${token}` } }).then(r=>r.json()),
  ]);
  servicesCache = sRes.data?.services || [];
  barbersCache  = bRes.data?.barbers  || [];

  // Populate walk-in form
  const sSel = document.getElementById('wiService');
  const bSel = document.getElementById('wiBarber');
  if (sSel) sSel.innerHTML = servicesCache.map(s => `<option value="${s.id}" data-price="${s.price}">${s.name} – RM${s.price}</option>`).join('');
  if (bSel) bSel.innerHTML = '<option value="">Any Available</option>' + barbersCache.map(b => `<option value="${b.id}">${b.name}</option>`).join('');
}

function openWalkIn() { document.getElementById('walkInModal').classList.add('open'); }
function closeWalkIn() { document.getElementById('walkInModal').classList.remove('open'); }

document.getElementById('walkInForm')?.addEventListener('submit', async (e) => {
  e.preventDefault();
  const btn = document.getElementById('wiSubmitBtn');
  btn.textContent = 'Adding...'; btn.disabled = true;

  const now = new Date();
  const time = now.toTimeString().slice(0,8);

  const res = await apiFetch('walkin', 'POST', {
    customer_name:  document.getElementById('wiName').value.trim(),
    customer_phone: document.getElementById('wiPhone').value.trim(),
    service_id:     parseInt(document.getElementById('wiService').value),
    barber_id:      parseInt(document.getElementById('wiBarber').value) || null,
    booking_time:   time,
    pay_method:     document.getElementById('wiPay').value,
    notes:          document.getElementById('wiNotes').value,
  });

  btn.textContent = 'Add to Queue'; btn.disabled = false;

  if (res.success) {
    toast('Walk-in added to queue! 🎉', 'success');
    closeWalkIn();
    document.getElementById('walkInForm').reset();
    await Promise.all([loadQueue(), loadStats()]);
  } else {
    toast(res.message, 'error');
  }
});

// ════════════════════════════════════════════
// CUSTOMER SEARCH & HISTORY
// ════════════════════════════════════════════
let searchTimer = null;
document.getElementById('customerSearch')?.addEventListener('input', (e) => {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(() => searchCustomers(e.target.value), 400);
});

async function searchCustomers(q) {
  const el = document.getElementById('searchResults');
  if (!el) return;
  if (q.length < 2) { el.innerHTML = ''; return; }
  el.innerHTML = '<div class="text-muted" style="padding:0.5rem;font-size:0.8rem">Searching...</div>';
  const res = await apiFetch(`search&q=${encodeURIComponent(q)}`);
  if (!res.success || !res.data.results.length) {
    el.innerHTML = '<div class="text-muted" style="padding:0.5rem;font-size:0.8rem">No customers found</div>';
    return;
  }
  el.innerHTML = '<div class="search-results">' + res.data.results.map(c => `
    <div class="search-result-item" onclick="loadCustomerHistory(${c.id}, '${esc(c.name)}')">
      <div>
        <div class="sri-name">${esc(c.name)}</div>
        <div class="sri-meta">${esc(c.phone||'–')} · ${esc(c.email)}</div>
      </div>
      <div class="sri-visits">${c.total_bookings} visits</div>
    </div>
  `).join('') + '</div>';
}

async function loadCustomerHistory(userId, name) {
  const res = await apiFetch(`customer-history&user_id=${userId}`);
  if (!res.success) return;
  const el = document.getElementById('customerHistory');
  if (!el) return;
  const { history, customer } = res.data;

  el.innerHTML = `
    <div class="history-header">
      <div class="b-avatar">${(customer.name||'?').charAt(0)}</div>
      <div>
        <div class="hist-name">${esc(customer.name)}</div>
        <div class="hist-meta">${esc(customer.phone||'–')} · ${esc(customer.email)} · Member since ${fmtDate(customer.created_at)}</div>
      </div>
    </div>
    ${history.length ? `
    <div class="table-wrap">
      <table>
        <thead><tr><th>Date</th><th>Service</th><th>Barber</th><th>Price</th><th>Status</th></tr></thead>
        <tbody>${history.map(h => `
          <tr>
            <td class="mono">${fmtDate(h.booking_date)} ${fmtTime(h.booking_time)}</td>
            <td>${esc(h.service_name)}</td>
            <td>${h.barber_name ? esc(h.barber_name) : '<span class="text-muted">–</span>'}</td>
            <td class="mono text-gold">RM${parseFloat(h.total_price).toFixed(0)}</td>
            <td><span class="badge badge-${h.status}">${h.status}</span></td>
          </tr>`).join('')}
        </tbody>
      </table>
    </div>` : '<div class="empty-state"><i class="fas fa-history"></i><p>No booking history</p></div>'}
  `;
}

// ── USERS ──
async function loadUsers() {
  const tbody = document.getElementById('usersTable');
  if (!tbody) return;
  tbody.innerHTML = '<tr><td colspan="7" class="loading"><i class="fas fa-spinner fa-spin"></i> Loading users...</td></tr>';
  try {
    const res = await apiFetch('../api/users.php');
    if (!res.success) throw new Error(res.message);
    const users = res.data.users || [];
    if (!users.length) {
      tbody.innerHTML = '<tr><td colspan="7" class="empty">No users found</td></tr>';
      return;
    }
    tbody.innerHTML = users.map(u => `
      <tr>
        <td class="mono">#${u.id}</td>
        <td><strong>${esc(u.name)}</strong></td>
        <td class="mono">${esc(u.email)}</td>
        <td class="mono">${esc(u.phone || '–')}</td>
        <td><span class="badge badge-${u.role}">${u.role}</span></td>
        <td class="mono">${fmtDate(u.created_at)}</td>
        <td class="mono">${u.booking_count}</td>
      </tr>
    `).join('');
  } catch(e) {
    tbody.innerHTML = '<tr><td colspan="7" class="error">Failed to load users</td></tr>';
  }
}

function filterUsers() {
  const q = document.getElementById('userSearch').value.toLowerCase();
  const rows = document.querySelectorAll('#usersTable tr');
  rows.forEach(row => {
    if (row.cells.length < 7) return; // Skip header or loading
    const text = row.textContent.toLowerCase();
    row.style.display = text.includes(q) ? '' : 'none';
  });
}

// ════════════════════════════════════════════
// NOTIFICATIONS
// ════════════════════════════════════════════
async function checkNotifications() {
  const res = await apiFetch(`notifications&since=30`);
  if (!res.success) return;
  const notifs = res.data.notifications || [];
  notifCache   = notifs;

  // Badge
  const cnt = document.getElementById('notifCount');
  if (cnt) { cnt.textContent = notifs.length; cnt.style.display = notifs.length ? 'flex' : 'none'; }

  renderNotifPanel(notifs);

  // Auto-toast for truly new ones
  if (notifs.length > 0 && notifs[0]) {
    const n = notifs[0];
    if (!window._lastNotifId || window._lastNotifId !== n.id) {
      window._lastNotifId = n.id;
      toast(`New booking: ${n.customer_name} – ${n.service_name}`, 'info');
    }
  }
}

function renderNotifPanel(notifs) {
  const el = document.getElementById('notifList');
  if (!el) return;
  if (!notifs.length) { el.innerHTML = '<div class="notif-empty">No new notifications</div>'; return; }
  el.innerHTML = notifs.map(n => `
    <div class="notif-item new">
      <div class="notif-icon"><i class="fas fa-calendar-plus"></i></div>
      <div>
        <div class="notif-text"><strong>${esc(n.customer_name)}</strong> booked <strong>${esc(n.service_name)}</strong></div>
        <div class="notif-time">${fmtDate(n.booking_date)} ${fmtTime(n.booking_time)} · <span class="badge badge-${n.status}">${n.status}</span></div>
      </div>
    </div>
  `).join('');
}

function toggleNotifPanel() {
  document.getElementById('notifPanel').classList.toggle('open');
}
document.addEventListener('click', (e) => {
  if (!e.target.closest('#notifBtn') && !e.target.closest('#notifPanel')) {
    document.getElementById('notifPanel')?.classList.remove('open');
  }
});

// ════════════════════════════════════════════
// PANEL NAVIGATION
// ════════════════════════════════════════════
function showPanel(name) {
  currentPanel = name;
  document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));
  document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
  const panel = document.getElementById('panel-' + name);
  if (panel) panel.classList.add('active');
  document.querySelectorAll(`.nav-link[data-panel="${name}"]`).forEach(l => l.classList.add('active'));

  // Lazy load panel data
  if (name === 'schedule') loadSchedule();
  if (name === 'customers') { /* ready */ }
  if (name === 'users') loadUsers();

  // Update topbar title
  const titles = {
    overview:  ['Live Overview',   'Real-time operations dashboard'],
    queue:     ['Live Queue',      'Today\'s customer queue'],
    schedule:  ['Today\'s Schedule','Full booking timeline'],
    customers: ['Customer Lookup', 'Search & view customer history'],
    users:     ['User Accounts',   'View registered users'],
    walkins:   ['Walk-In Entry',   'Add unscheduled customers'],
  };
  const t = titles[name] || ['Dashboard', ''];
  setText('panelTitle', t[0]); setText('panelSub', t[1]);
}

// ════════════════════════════════════════════
// SIDEBAR COLLAPSE
// ════════════════════════════════════════════
function toggleSidebar() {
  sidebarCollapsed = !sidebarCollapsed;
  document.getElementById('sidebar').classList.toggle('collapsed', sidebarCollapsed);
  const icon = document.getElementById('collapseIcon');
  if (icon) icon.className = sidebarCollapsed ? 'fas fa-chevron-right' : 'fas fa-chevron-left';
}

// ════════════════════════════════════════════
// TOAST
// ════════════════════════════════════════════
let toastTimer = null;
function toast(msg, type = 'info') {
  const el   = document.getElementById('toast');
  const icon = document.getElementById('toastIcon');
  const text = document.getElementById('toastText');
  const icons = { success: 'fa-check-circle', error: 'fa-times-circle', info: 'fa-info-circle' };
  if (icon) icon.className = `fas ${icons[type] || 'fa-info-circle'} toast-icon`;
  if (text) text.textContent = msg;
  el.className = `toast ${type} show`;
  clearTimeout(toastTimer);
  toastTimer = setTimeout(() => el.classList.remove('show'), 3500);
}

// ════════════════════════════════════════════
// HELPERS
// ════════════════════════════════════════════
function fmtTime(t) {
  if (!t) return '–';
  const [h, m] = t.split(':');
  const hr = parseInt(h), ap = hr >= 12 ? 'PM' : 'AM';
  return `${hr > 12 ? hr-12 : hr || 12}:${m} ${ap}`;
}
function fmtDate(d) {
  if (!d) return '–';
  return new Date(d).toLocaleDateString('en-MY', { day:'numeric', month:'short', year:'numeric' });
}
function setText(id, val) {
  const el = document.getElementById(id);
  if (el) el.textContent = val;
}
function esc(str) {
  if (!str) return '';
  return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
