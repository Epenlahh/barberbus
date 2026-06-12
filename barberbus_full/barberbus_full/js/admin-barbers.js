// ── INIT ──
document.addEventListener('DOMContentLoaded', async () => {
  await verifyAdmin();
  loadBarbers();
});

// ── BARBERS ──
async function loadBarbers() {
  try {
    const res = await api(`${API}/services.php?type=barbers&admin=1`);
    const barbers = res.data.barbers || [];
    const tbody = document.getElementById('barbersTable');
    if (!tbody) return;
    if (!barbers.length) { tbody.innerHTML = '<tr><td colspan="8" class="empty">No barbers found</td></tr>'; return; }
    tbody.innerHTML = barbers.map(b => `
      <tr>
        <td style="color:var(--light-gray)">#${b.id}</td>
        <td><strong>${esc(b.name)}</strong></td>
        <td style="font-size:0.82rem;color:var(--light-gray)">${esc(b.specialty) || '–'}</td>
        <td>${b.experience} yrs</td>
        <td><span style="color:var(--gold)">★ ${parseFloat(b.rating).toFixed(1)}</span></td>
        <td>
          ${b.officer_email
            ? `<span class="badge badge-officer"><i class="fas fa-user-shield" style="margin-right:3px"></i>${esc(b.officer_email)}</span>`
            : `<span style="color:var(--gray);font-size:0.78rem">No account</span>`}
        </td>
        <td><span class="badge" style="background:${b.is_active ? 'rgba(64,200,120,0.15)' : 'rgba(255,255,255,0.06)'};color:${b.is_active ? 'var(--green)' : 'var(--gray)'}">${b.is_active ? 'Active' : 'Inactive'}</span></td>
        <td style="display:flex;gap:0.5rem">
          <button class="btn btn-outline btn-sm" onclick="openBarberModal(${JSON.stringify(b).replace(/"/g,'&quot;')})"><i class="fas fa-edit"></i></button>
          <button class="btn btn-danger btn-sm" onclick="deactivateBarber(${b.id})" title="Deactivate"><i class="fas fa-user-slash"></i></button>
        </td>
      </tr>
    `).join('');

    // Ensure header is correct
    const thead = tbody.closest('table').querySelector('thead tr');
    if (thead && thead.children.length === 7) {
      thead.innerHTML = '<th>#</th><th>Name</th><th>Specialty</th><th>Experience</th><th>Rating</th><th>Officer Account</th><th>Status</th><th>Actions</th>';
    }
  } catch(e) { showToast('Failed to load barbers.', 'error'); }
}

function openBarberModal(barber = null) {
  const isEdit = !!barber;
  const titleEl = document.getElementById('barberModalTitle');
  if (titleEl) titleEl.textContent = isEdit ? 'Edit Barber' : 'Add Barber';
  
  document.getElementById('b-id').value          = barber?.id || '';
  document.getElementById('b-name').value        = barber?.name || '';
  document.getElementById('b-specialty').value   = barber?.specialty || '';
  document.getElementById('b-exp').value         = barber?.experience || 0;
  document.getElementById('b-rating').value      = barber?.rating || 5.0;
  document.getElementById('b-bio').value         = barber?.bio || '';
  document.getElementById('b-active').value      = barber?.is_active ?? 1;
  document.getElementById('b-officer-email').value = barber?.officer_email || '';
  document.getElementById('b-officer-pass').value  = '';
  
  const hintEl = document.getElementById('b-pass-hint');
  if (hintEl) hintEl.style.display = isEdit ? 'inline' : 'none';
  
  const modal = document.getElementById('barberModal');
  if (modal) modal.classList.add('open');
}

async function saveBarber() {
  const id       = document.getElementById('b-id').value;
  const name     = document.getElementById('b-name').value.trim();
  if (!name) { showToast('Name is required.', 'error'); return; }

  const officerEmail = document.getElementById('b-officer-email').value.trim();
  const officerPass  = document.getElementById('b-officer-pass').value;

  if (officerPass && officerPass.length < 6) {
    showToast('Officer password must be at least 6 characters.', 'error'); return;
  }

  const payload = {
    name,
    specialty:      document.getElementById('b-specialty').value,
    experience:     parseInt(document.getElementById('b-exp').value) || 0,
    rating:         parseFloat(document.getElementById('b-rating').value) || 5.0,
    bio:            document.getElementById('b-bio').value,
    is_active:      parseInt(document.getElementById('b-active').value),
    officer_email:  officerEmail,
    officer_password: officerPass,
  };

  try {
    const res = id
      ? await api(`${API}/services.php?type=barbers&id=${id}`, 'PUT', payload)
      : await api(`${API}/services.php?type=barbers`, 'POST', payload);
    if (res.success) {
      showToast(id ? 'Barber updated!' : 'Barber added!', 'success');
      closeModal('barberModal');
      loadBarbers();
    } else {
      showToast(res.message, 'error');
    }
  } catch(e) { showToast('Save failed.', 'error'); }
}

async function deactivateBarber(id) {
  if (!confirm('Deactivate this barber? They will no longer appear on the site.')) return;
  try {
    const res = await api(`${API}/services.php?type=barbers&id=${id}`, 'DELETE');
    if (res.success) { showToast('Barber deactivated.', 'success'); loadBarbers(); }
  } catch(e) {}
}