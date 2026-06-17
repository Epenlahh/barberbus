// ── INIT ──
document.addEventListener('DOMContentLoaded', async () => {
  await verifyAdmin();
  loadUsers();
});

// ── USERS ──
async function loadUsers() {
  try {
    const res = await api(`${API}/users.php`);
    const users = res.data.users || [];
    const tbody = document.getElementById('usersTable');
    if (!tbody) return;
    if (!users.length) { tbody.innerHTML = '<tr><td colspan="8" class="empty">No users found</td></tr>'; return; }
    tbody.innerHTML = users.map(u => `
      <tr>
        <td style="color:var(--light-gray)">#${u.id}</td>
        <td><strong>${esc(u.name)}</strong></td>
        <td style="font-size:0.82rem">${esc(u.email)}</td>
        <td style="font-size:0.82rem">${u.phone || '–'}</td>
        <td>
          <select class="role-select" onchange="changeUserRole(${u.id}, this.value)" title="Change role">
            <option value="user"    ${u.role==='user'    ? 'selected' : ''}>User</option>
            <option value="officer" ${u.role==='officer' ? 'selected' : ''}>Officer</option>
            <option value="admin"   ${u.role==='admin'   ? 'selected' : ''}>Admin</option>
          </select>
        </td>
        <td style="font-size:0.78rem;color:var(--light-gray)">${fmtDate(u.created_at)}</td>
        <td style="color:var(--gold);font-weight:600">${u.booking_count || 0}</td>
        <td>
          <button class="btn btn-danger btn-sm" onclick="deleteUser(${u.id}, '${esc(u.name)}')" title="Delete user"><i class="fas fa-trash"></i></button>
        </td>
      </tr>
    `).join('');
  } catch(e) { showToast('Failed to load users.', 'error'); }
}

async function changeUserRole(id, role) {
  try {
    const res = await api(`${API}/users.php?id=${id}`, 'PUT', { role });
    if (res.success) showToast(`Role updated to ${role}!`, 'success');
    else { showToast(res.message || 'Failed to update role.', 'error'); loadUsers(); }
  } catch(e) { showToast('Network error.', 'error'); loadUsers(); }
}

async function deleteUser(id, name) {
  if (!confirm(`Delete user "${name}" permanently? This cannot be undone.`)) return;
  try {
    const res = await api(`${API}/users.php?id=${id}`, 'DELETE');
    if (res.success) { showToast('User deleted.', 'success'); loadUsers(); }
    else showToast(res.message, 'error');
  } catch(e) { showToast('Delete failed.', 'error'); }
}