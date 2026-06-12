<?php
$pageTitle = "BarberBus – Admin Dashboard";
$currentPage = "admin";
$currentPanel = "dashboard";
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'includes/admin_head.php'; ?>
<body>

<div class="admin-wrapper">

  <!-- ── SIDEBAR ── -->
  <?php include 'includes/admin_sidebar.php'; ?>

  <!-- ── MAIN ── -->
  <main class="admin-main">

    <!-- ════════════════════════ DASHBOARD ════════════════════════ -->
    <div id="panel-dashboard" class="panel active">
      <div class="topbar">
        <div>
          <h1>Dashboard <span style="color:var(--gold);font-style:italic">Overview</span></h1>
          <p style="color:var(--light-gray);font-size:0.82rem;margin-top:0.2rem">Real-time BarberBus statistics</p>
        </div>
        <div class="topbar-actions">
          <span style="font-size:0.78rem;color:var(--light-gray)" id="lastUpdated"></span>
          <button class="btn btn-outline" onclick="loadDashboard()"><i class="fas fa-sync-alt"></i> Refresh</button>
        </div>
      </div>

      <div class="kpi-grid">
        <div class="kpi-card gold"><div class="kpi-icon"><i class="fas fa-receipt"></i></div><div class="kpi-val" id="kpiRevenue">–</div><div class="kpi-lbl">Total Revenue (RM)</div></div>
        <div class="kpi-card blue"><div class="kpi-icon"><i class="fas fa-calendar-check"></i></div><div class="kpi-val" id="kpiBookings">–</div><div class="kpi-lbl">Total Bookings</div></div>
        <div class="kpi-card green"><div class="kpi-icon"><i class="fas fa-users"></i></div><div class="kpi-val" id="kpiUsers">–</div><div class="kpi-lbl">Registered Users</div></div>
        <div class="kpi-card orange"><div class="kpi-icon"><i class="fas fa-calendar-day"></i></div><div class="kpi-val" id="kpiToday">–</div><div class="kpi-lbl">Today's Bookings</div></div>
        <div class="kpi-card red"><div class="kpi-icon"><i class="fas fa-hourglass-half"></i></div><div class="kpi-val" id="kpiPending">–</div><div class="kpi-lbl">Pending Approval</div></div>
        <div class="kpi-card gold"><div class="kpi-icon"><i class="fas fa-user-tie"></i></div><div class="kpi-val" id="kpiBarbers">–</div><div class="kpi-lbl">Active Barbers</div></div>
      </div>

      <div style="display:grid;grid-template-columns:1.6fr 1fr;gap:1.4rem">
        <!-- Revenue Chart -->
        <div class="box">
          <div class="box-header">
            <div class="box-title">Monthly Revenue</div>
            <span style="font-size:0.75rem;color:var(--light-gray)">Last 6 months</span>
          </div>
          <div class="chart-wrap" id="revenueChart">
            <div class="loading"><i class="fas fa-spinner fa-spin"></i></div>
          </div>
        </div>

        <!-- Popular Services -->
        <div class="box">
          <div class="box-header"><div class="box-title">Top Services</div></div>
          <div id="topServices"><div class="loading"><i class="fas fa-spinner fa-spin"></i></div></div>
        </div>
      </div>

      <!-- Recent Bookings -->
      <div class="box">
        <div class="box-header">
          <div class="box-title">Recent Bookings</div>
          <button class="btn btn-outline btn-sm" onclick="window.location.href='bookings.php'">View All</button>
        </div>
        <div style="overflow-x:auto">
          <table class="data-table">
            <thead><tr><th>#</th><th>Client</th><th>Service</th><th>Barber</th><th>Date & Time</th><th>Amount</th><th>Status</th><th>Action</th></tr></thead>
            <tbody id="recentBookingsTable"><tr><td colspan="8" class="loading"><i class="fas fa-spinner fa-spin"></i></td></tr></tbody>
          </table>
        </div>
      </div>
    </div>

  </main>
</div>

<!-- ── TOAST ── -->
<div id="toast" style="position:fixed;bottom:1.5rem;right:1.5rem;z-index:9999;padding:0.85rem 1.4rem;border-radius:8px;font-size:0.85rem;font-weight:500;opacity:0;transform:translateY(10px);transition:all 0.3s;pointer-events:none;max-width:320px"></div>

<script src="../js/admin-common.js"></script>
<script src="../js/admin-dashboard.js"></script>
</body>
</html>