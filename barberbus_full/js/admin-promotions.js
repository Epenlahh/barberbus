// ── INIT ──
document.addEventListener('DOMContentLoaded', async () => {
  await verifyAdmin();
  loadPromotions();
});

// ── PROMOTIONS ──
async function loadPromotions() {
  try {
    const res = await api(`${API}/promotions.php`);
    const promos = res.data.promotions || [];
    const tbody = document.getElementById('promotionsTable');
    if (!tbody) return;
    if (!promos.length) {
      tbody.innerHTML = '<tr><td colspan="7" class="empty">No promotions yet.</td></tr>';
      return;
    }
    tbody.innerHTML = promos.map(p => `
      <tr>
        <td style="color:var(--light-gray)">#${p.id}</td>
        <td><strong>${esc(p.title)}</strong></td>
        <td style="font-size:0.78rem;color:var(--light-gray)">${esc(p.description?.substring(0,50)) || '–'}</td>
        <td><span class="badge" style="background:${p.is_active ? 'rgba(64,200,120,0.15)' : 'rgba(255,255,255,0.06)'};color:${p.is_active ? 'var(--green)' : 'var(--gray)'}">${p.is_active ? 'Active' : 'Inactive'}</span></td>
        <td style="font-size:0.78rem">${fmtDate(p.start_date)}</td>
        <td style="font-size:0.78rem">${fmtDate(p.end_date)}</td>
        <td style="display:flex;gap:0.4rem">
          <button class="btn btn-outline btn-sm" onclick="editPromotion(${JSON.stringify(p).replace(/"/g,'&quot;')})"><i class="fas fa-edit"></i></button>
          <button class="btn btn-danger btn-sm" onclick="deletePromotion(${p.id})"><i class="fas fa-trash"></i></button>
        </td>
      </tr>
    `).join('');
  } catch(e) { showToast('Failed to load promotions.', 'error'); }
}

function openPromotionModal() {
  document.getElementById('promo-id').value = '';
  const titleEl = document.getElementById('promotionModalTitle');
  if (titleEl) titleEl.textContent = 'Add Promotion';
  document.getElementById('promo-title').value = '';
  document.getElementById('promo-desc').value = '';
  document.getElementById('promo-image').value = '';
  document.getElementById('promo-start').value = '';
  document.getElementById('promo-end').value = '';
  document.getElementById('promo-active').value = '1';
  document.getElementById('promo-link').value = '';
  const modal = document.getElementById('promotionModal');
  if (modal) modal.classList.add('open');
}

function editPromotion(promo) {
  document.getElementById('promo-id').value = promo.id;
  const titleEl = document.getElementById('promotionModalTitle');
  if (titleEl) titleEl.textContent = 'Edit Promotion';
  document.getElementById('promo-title').value = promo.title;
  document.getElementById('promo-desc').value = promo.description || '';
  document.getElementById('promo-image').value = promo.image || '';
  document.getElementById('promo-start').value = promo.start_date || '';
  document.getElementById('promo-end').value = promo.end_date || '';
  document.getElementById('promo-active').value = promo.is_active ? '1' : '0';
  document.getElementById('promo-link').value = promo.link || '';
  const modal = document.getElementById('promotionModal');
  if (modal) modal.classList.add('open');
}

async function savePromotion() {
  const id = document.getElementById('promo-id').value;
  const title = document.getElementById('promo-title').value.trim();
  if (!title) { showToast('Title is required.', 'error'); return; }
  const payload = {
    title,
    description: document.getElementById('promo-desc').value,
    image: document.getElementById('promo-image').value,
    start_date: document.getElementById('promo-start').value,
    end_date: document.getElementById('promo-end').value,
    is_active: parseInt(document.getElementById('promo-active').value),
    link: document.getElementById('promo-link').value,
  };
  try {
    const res = id
      ? await api(`${API}/promotions.php?id=${id}`, 'PUT', payload)
      : await api(`${API}/promotions.php`, 'POST', payload);
    if (res.success) {
      showToast(id ? 'Promotion updated!' : 'Promotion added!', 'success');
      closeModal('promotionModal');
      loadPromotions();
    } else showToast(res.message, 'error');
  } catch(e) { showToast('Save failed.', 'error'); }
}

async function deletePromotion(id) {
  if (!confirm('Delete this promotion?')) return;
  try {
    const res = await api(`${API}/promotions.php?id=${id}`, 'DELETE');
    if (res.success) {
      showToast('Promotion deleted.', 'success');
      loadPromotions();
    } else showToast(res.message, 'error');
  } catch(e) { showToast('Delete failed.', 'error'); }
}