let allBookingsData = [];

// ── INIT ──
document.addEventListener('DOMContentLoaded', async () => {
  await verifyAdmin();
  loadAllBookings();
});

// ── ALL BOOKINGS ──
async function loadAllBookings() {
  try {
    const res = await api(`${API}/bookings.php?admin=1`);
    allBookingsData = res.data.bookings || [];
    renderBookingsTable(allBookingsData);
  } catch(e) { showToast('Failed to load bookings.', 'error'); }
}

function renderBookingsTable(data) {
  const tbody = document.getElementById('bookingsTable');
  if (!tbody) return;
  if (!data || !data.length) { tbody.innerHTML = '<tr><td colspan="10" class="empty">No bookings found</td></tr>'; return; }
  tbody.innerHTML = data.map(b => `
    <tr>
      <td style="color:var(--light-gray)">#${b.id}</td>
      <td><strong>${esc(b.user_name)}</strong></td>
      <td style="font-size:0.78rem;color:var(--light-gray)">${esc(b.user_email)}<br/>${b.user_phone || ''}</td>
      <td>${esc(b.service_name)}</td>
      <td>${esc(b.barber_name) || 'Any'}</td>
      <td><span style="font-size:0.8rem">${fmtDate(b.booking_date)}</span><br/><span style="color:var(--light-gray);font-size:0.72rem">${fmtTime(b.booking_time)}</span></td>
      <td style="color:var(--gold);font-weight:600">RM${parseFloat(b.total_price).toFixed(0)}</td>
      <td style="font-size:0.78rem;text-transform:capitalize">${b.pay_method?.replace('_',' ') || '–'}</td>
      <td><span class="badge badge-${b.status}">${b.status}</span></td>
      <td style="display:flex;gap:0.4rem;align-items:center;flex-wrap:wrap">
        <select class="status-select" onchange="updateBookingStatus(${b.id}, this.value)">
          <option value="">Status...</option>
          <option value="pending">Pending</option>
          <option value="confirmed">Confirmed</option>
          <option value="completed">Completed</option>
          <option value="cancelled">Cancelled</option>
        </select>
        <button class="btn btn-danger btn-sm" onclick="deleteBooking(${b.id})"><i class="fas fa-trash"></i></button>
      </td>
    </tr>
  `).join('');
}

function filterBookingStatus(status) {
  const filtered = status ? allBookingsData.filter(b => b.status === status) : allBookingsData;
  renderBookingsTable(filtered);
}

async function updateBookingStatus(id, status) {
  if (!status) return;
  try {
    const res = await api(`${API}/bookings.php?id=${id}&admin=1`, 'PUT', { status });
    if (res.success) { 
        showToast('Status updated!', 'success'); 
        loadAllBookings();
    }
    else showToast(res.message, 'error');
  } catch(e) { showToast('Failed to update.', 'error'); }
}

async function deleteBooking(id) {
  if (!confirm('Delete this booking permanently?')) return;
  try {
    const res = await api(`${API}/bookings.php?id=${id}`, 'DELETE');
    if (res.success) { showToast('Booking deleted.', 'success'); loadAllBookings(); }
    else showToast(res.message, 'error');
  } catch(e) { showToast('Delete failed.', 'error'); }
}