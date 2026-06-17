// ── INIT ──
document.addEventListener('DOMContentLoaded', async () => {
  await verifyAdmin();
  loadStoreSettings();
});

// ── STORE SETTINGS ──
async function loadStoreSettings() {
  const display = document.getElementById('storeDisplay');
  if (!display) return;
  try {
    const res = await api(`${API}/store.php`);
    const store = res.data.store || {};
    const html = `
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem">
        <div><strong>Store Name:</strong> ${esc(store.name) || 'BarberBus'}</div>
        <div><strong>Status:</strong> <span class="badge" style="background:${store.is_open ? 'rgba(64,200,120,0.15)' : 'rgba(255,255,255,0.06)'};color:${store.is_open ? 'var(--green)' : 'var(--gray)'}">${store.is_open ? 'Open' : 'Closed'}</span></div>
      </div>
      <div style="margin-bottom:1rem"><strong>Description:</strong><p style="color:var(--light-gray);margin-top:0.5rem">${esc(store.description) || 'No description set'}</p></div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
        <div><strong>Hours:</strong> ${esc(store.business_hours) || 'Not set'}</div>
        <div><strong>Location:</strong> ${esc(store.location) || 'Not set'}</div>
        <div><strong>Phone:</strong> ${esc(store.phone) || 'Not set'}</div>
        <div><strong>Profile Image:</strong> ${store.profile_image ? '<a href="' + store.profile_image + '" target="_blank" style="color:var(--gold)">View Image</a>' : 'Not set'}</div>
      </div>
    `;
    display.innerHTML = html;
  } catch(e) {
    display.innerHTML = '<div class="empty">Could not load store settings</div>';
  }
}

function openStoreModal() {
  const modal = document.getElementById('storeModal');
  if (modal) modal.classList.add('open');
  api(`${API}/store.php`).then(res => {
    const store = res.data.store || {};
    document.getElementById('store-name').value = store.name || '';
    document.getElementById('store-desc').value = store.description || '';
    document.getElementById('store-open').value = store.is_open ? '1' : '0';
    document.getElementById('store-image').value = store.profile_image || '';
    document.getElementById('store-hours').value = store.business_hours || '';
    document.getElementById('store-location').value = store.location || '';
    document.getElementById('store-phone').value = store.phone || '';
  }).catch(() => {});
}

async function saveStore() {
  const payload = {
    name: document.getElementById('store-name').value || 'BarberBus',
    description: document.getElementById('store-desc').value,
    is_open: parseInt(document.getElementById('store-open').value),
    profile_image: document.getElementById('store-image').value,
    business_hours: document.getElementById('store-hours').value,
    location: document.getElementById('store-location').value,
    phone: document.getElementById('store-phone').value,
  };
  try {
    const res = await api(`${API}/store.php`, 'POST', payload);
    if (res.success) {
      showToast('Store settings saved!', 'success');
      closeModal('storeModal');
      loadStoreSettings();
    } else showToast(res.message || 'Error saving store', 'error');
  } catch(e) { showToast('Save failed.', 'error'); }
}