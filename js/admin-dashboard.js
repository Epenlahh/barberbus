// ── INIT ──
document.addEventListener('DOMContentLoaded', async () => {
  // Double check with server to ensure token hasn't expired/revoked
  await verifyAdmin();
  loadDashboard();
  if (document.getElementById('lastUpdated')) {
    document.getElementById('lastUpdated').textContent = 'Updated: ' + new Date().toLocaleTimeString();
  }
});

// ── DASHBOARD ──
async function loadDashboard() {
  console.log('Loading Dashboard Data...');
  try {
    const res = await api(`${API}/stats.php`);
    if (!res.success) {
      console.error('Dashboard Stats API Error:', res.message);
      showToast(res.message || 'Could not load stats.', 'error');
      return;
    }

    console.log('Dashboard Data Received:', res.data);
    const { stats, monthly_revenue, popular_services, recent_bookings } = res.data;

    const kpis = {
        'kpiRevenue': parseFloat(stats.total_revenue).toFixed(0),
        'kpiBookings': stats.total_bookings,
        'kpiUsers': stats.total_users,
        'kpiToday': stats.today_bookings,
        'kpiPending': stats.pending,
        'kpiBarbers': stats.total_barbers
    };

    for (let id in kpis) {
        const el = document.getElementById(id);
        if (el) el.textContent = kpis[id];
        else console.warn(`KPI element not found: ${id}`);
    }

    if (stats.pending > 0) {
      const b = document.getElementById('pendingCount');
      if (b) { b.textContent = stats.pending; b.style.display = 'inline'; }
    }

    renderChart(monthly_revenue);
    renderTopServices(popular_services);
    renderRecentBookings(recent_bookings);
    
    const lastUpdateEl = document.getElementById('lastUpdated');
    if (lastUpdateEl) lastUpdateEl.textContent = 'Updated: ' + new Date().toLocaleTimeString();
    console.log('Dashboard Rendered Successfully.');
  } catch(e) {
    console.error('loadDashboard Exception:', e);
    showToast('Could not load dashboard data.', 'error');
  }
}

function renderChart(data) {
  const wrap = document.getElementById('revenueChart');
  if (!wrap) return;
  if (!data || !data.length) { wrap.innerHTML = '<div class="empty">No revenue data yet</div>'; return; }
  const max = Math.max(...data.map(d => parseFloat(d.revenue)), 1);
  wrap.innerHTML = data.map(d => {
    const pct = (parseFloat(d.revenue) / max * 100).toFixed(1);
    return `<div class="chart-bar-wrap">
      <div style="font-size:0.68rem;color:var(--gold);margin-bottom:4px">RM${parseFloat(d.revenue).toFixed(0)}</div>
      <div class="chart-bar" style="height:${pct}%"></div>
      <div class="chart-label">${d.month}</div>
    </div>`;
  }).join('');
}

function renderTopServices(data) {
  const el = document.getElementById('topServices');
  if (!el) return;
  if (!data || !data.length) { el.innerHTML = '<div class="empty">No data yet</div>'; return; }
  const max = Math.max(...data.map(d => parseInt(d.count)), 1);
  el.innerHTML = data.map(d => `
    <div style="margin-bottom:0.9rem">
      <div style="display:flex;justify-content:space-between;font-size:0.82rem;margin-bottom:0.3rem">
        <span>${d.name}</span><span style="color:var(--gold)">${d.count} bookings</span>
      </div>
      <div style="background:var(--dark3);border-radius:3px;height:5px;overflow:hidden">
        <div style="background:var(--gold);height:100%;width:${(parseInt(d.count)/max*100).toFixed(0)}%;border-radius:3px;transition:width 0.5s ease"></div>
      </div>
    </div>
  `).join('');
}

function renderRecentBookings(data) {
  const tbody = document.getElementById('recentBookingsTable');
  if (!tbody) return;
  if (!data || !data.length) { tbody.innerHTML = '<tr><td colspan="8" class="empty">No bookings yet</td></tr>'; return; }
  tbody.innerHTML = data.map(b => `
    <tr>
      <td style="color:var(--light-gray)">#${b.id}</td>
      <td><strong>${esc(b.user_name)}</strong></td>
      <td>${esc(b.service_name)}</td>
      <td>${esc(b.barber_name) || '–'}</td>
      <td><span style="font-size:0.8rem">${fmtDate(b.booking_date)}</span><br/><span style="color:var(--light-gray);font-size:0.75rem">${fmtTime(b.booking_time)}</span></td>
      <td style="color:var(--gold);font-weight:600">RM${parseFloat(b.total_price).toFixed(0)}</td>
      <td><span class="badge badge-${b.status}">${b.status}</span></td>
      <td>
        <select class="status-select" onchange="updateBookingStatus(${b.id}, this.value)" title="Change status">
          <option value="">Change...</option>
          <option value="pending">Pending</option>
          <option value="confirmed">Confirmed</option>
          <option value="completed">Completed</option>
          <option value="cancelled">Cancelled</option>
        </select>
      </td>
    </tr>
  `).join('');
}
