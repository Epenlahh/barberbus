// ── INIT ──
document.addEventListener('DOMContentLoaded', async () => {
  await verifyAdmin();
  loadServices();
});

// ── SERVICES ──
async function loadServices() {
  try {
    const res = await api(`${API}/services.php`);
    const services = res.data.services || [];
    const tbody = document.getElementById('servicesTable');
    if (!tbody) return;
    if (!services.length) { tbody.innerHTML = '<tr><td colspan="7" class="empty">No services found</td></tr>'; return; }
    tbody.innerHTML = services.map(s => `
      <tr>
        <td style="color:var(--light-gray)">#${s.id}</td>
        <td><strong>${esc(s.name)}</strong><br/><span style="font-size:0.75rem;color:var(--light-gray)">${esc(s.description?.substring(0,50)) || ''}</span></td>
        <td style="text-transform:capitalize">${s.category}</td>
        <td>${s.duration} min</td>
        <td style="color:var(--gold);font-weight:600">RM ${parseFloat(s.price).toFixed(2)}</td>
        <td><span class="badge" style="background:${s.is_active ? 'rgba(64,200,120,0.15)' : 'rgba(255,255,255,0.06)'};color:${s.is_active ? 'var(--green)' : 'var(--gray)'}">${s.is_active ? 'Active' : 'Inactive'}</span></td>
        <td style="display:flex;gap:0.5rem">
          <button class="btn btn-outline btn-sm" onclick="openServiceModal(${JSON.stringify(s).replace(/"/g,'&quot;')})"><i class="fas fa-edit"></i></button>
          <button class="btn btn-danger btn-sm" onclick="deactivateService(${s.id})"><i class="fas fa-trash"></i></button>
        </td>
      </tr>
    `).join('');
  } catch(e) { showToast('Failed to load services.', 'error'); }
}

function openServiceModal(service = null) {
  const titleEl = document.getElementById('serviceModalTitle');
  if (titleEl) titleEl.textContent = service ? 'Edit Service' : 'Add Service';
  
  document.getElementById('s-id').value       = service?.id || '';
  document.getElementById('s-name').value     = service?.name || '';
  document.getElementById('s-price').value    = service?.price || '';
  document.getElementById('s-duration').value = service?.duration || '';
  document.getElementById('s-category').value = service?.category || 'haircut';
  document.getElementById('s-active').value   = service?.is_active ?? 1;
  document.getElementById('s-desc').value     = service?.description || '';
  
  const modal = document.getElementById('serviceModal');
  if (modal) modal.classList.add('open');
}

async function saveService() {
  const id    = document.getElementById('s-id').value;
  const name  = document.getElementById('s-name').value.trim();
  const price = parseFloat(document.getElementById('s-price').value);
  const dur   = parseInt(document.getElementById('s-duration').value);
  if (!name || !price || !dur) { showToast('Name, price and duration are required.', 'error'); return; }
  const payload = {
    name, price, duration: dur,
    category: document.getElementById('s-category').value,
    is_active: parseInt(document.getElementById('s-active').value),
    description: document.getElementById('s-desc').value,
  };
  try {
    const res = id
      ? await api(`${API}/services.php?id=${id}`, 'PUT', payload)
      : await api(`${API}/services.php`, 'POST', payload);
    if (res.success) { showToast(id ? 'Service updated!' : 'Service added!', 'success'); closeModal('serviceModal'); loadServices(); }
    else showToast(res.message, 'error');
  } catch(e) { showToast('Save failed.', 'error'); }
}

async function deactivateService(id) {
  if (!confirm('Deactivate this service?')) return;
  try {
    const res = await api(`${API}/services.php?id=${id}`, 'DELETE');
    if (res.success) { showToast('Service deactivated.', 'success'); loadServices(); }
  } catch(e) {}
}