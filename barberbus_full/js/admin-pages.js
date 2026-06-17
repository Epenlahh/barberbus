// ── INIT ──
document.addEventListener('DOMContentLoaded', async () => {
  await verifyAdmin();
  switchContentPage('dashboard');
});

// ── PAGE CONTENT ──
async function switchContentPage(pageName, trigger = null) {
  document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
  if (trigger && trigger.classList) {
    trigger.classList.add('active');
  } else {
    const defaultBtn = document.querySelector('.tab-btn');
    if (defaultBtn) defaultBtn.classList.add('active');
  }
  loadPageContent(pageName);
}

async function loadPageContent(pageName) {
  const display = document.getElementById('pageContent');
  if (!display) return;
  try {
    const res = await api(`${API}/pages.php?page=${pageName}`);
    const content = res.data.content || {};
    const html = `
      <div style="margin-bottom:1rem"><strong>Title:</strong> ${esc(content.title) || 'Not set'}</div>
      <div style="margin-bottom:1rem"><strong>Hero:</strong><p style="color:var(--light-gray);margin-top:0.3rem">${esc(content.hero) || 'Not set'}</p></div>
      <div style="margin-bottom:1rem"><strong>Main Content:</strong><p style="color:var(--light-gray);margin-top:0.3rem;white-space:pre-wrap">${esc(content.content) || 'Not set'}</p></div>
      <button class="btn btn-gold" onclick="editPageContent('${pageName}')"><i class="fas fa-edit"></i> Edit Content</button>
    `;
    display.innerHTML = html;
  } catch(e) {
    display.innerHTML = '<div class="empty">Could not load page content</div>';
  }
}

function editPageContent(pageName) {
  api(`${API}/pages.php?page=${pageName}`).then(res => {
    const content = res.data.content || {};
    document.getElementById('page-name').value = pageName;
    const titleEl = document.getElementById('pageModalTitle');
    if (titleEl) titleEl.textContent = 'Edit ' + pageName.charAt(0).toUpperCase() + pageName.slice(1);
    document.getElementById('page-title').value = content.title || '';
    document.getElementById('page-hero').value = content.hero || '';
    document.getElementById('page-content').value = content.content || '';
    document.getElementById('page-footer').value = content.footer || '';
    const modal = document.getElementById('pageModal');
    if (modal) modal.classList.add('open');
  }).catch(() => { showToast('Could not load page data', 'error'); });
}

async function savePageContent() {
  const pageName = document.getElementById('page-name').value;
  if (!pageName) { showToast('Page name missing', 'error'); return; }
  const payload = {
    page: pageName,
    title: document.getElementById('page-title').value,
    hero: document.getElementById('page-hero').value,
    content: document.getElementById('page-content').value,
    footer: document.getElementById('page-footer').value,
  };
  try {
    const res = await api(`${API}/pages.php`, 'POST', payload);
    if (res.success) {
      showToast('Page content saved!', 'success');
      closeModal('pageModal');
      loadPageContent(pageName);
    } else showToast(res.message || 'Error saving content', 'error');
  } catch(e) { showToast('Save failed.', 'error'); }
}