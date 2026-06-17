<?php
$pageTitle = "BarberBus – Admin Bookings";
$currentPage = "admin";
$currentPanel = "bookings";
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

    <!-- ════════════════════════ BOOKINGS ════════════════════════ -->
    <div id="panel-bookings" class="panel active">
      <div class="topbar">
        <h1>All <span style="color:var(--gold);font-style:italic">Bookings</span></h1>
        <div class="topbar-actions">
          <div class="search-box"><i class="fas fa-search"></i><input type="text" id="bookingSearch" placeholder="Search..." oninput="filterTable('bookingSearch','bookingsTable')"/></div>
          <select onchange="filterBookingStatus(this.value)" style="background:var(--dark3);border:1px solid rgba(255,255,255,0.08);color:var(--white);padding:0.5rem 0.8rem;border-radius:6px;font-size:0.82rem">
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="confirmed">Confirmed</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
          </select>
        </div>
      </div>
      <div class="box" style="overflow-x:auto">
        <table class="data-table">
          <thead><tr><th>#</th><th>Client</th><th>Contacts</th><th>Service</th><th>Barber</th><th>Date & Time</th><th>Amount</th><th>Payment</th><th>Status</th><th>Actions</th></tr></thead>
          <tbody id="bookingsTable"><tr><td colspan="10" class="loading"><i class="fas fa-spinner fa-spin"></i></td></tr></tbody>
        </table>
      </div>
    </div>

  </main>
</div>

<!-- ── TOAST ── -->
<div id="toast" style="position:fixed;bottom:1.5rem;right:1.5rem;z-index:9999;padding:0.85rem 1.4rem;border-radius:8px;font-size:0.85rem;font-weight:500;opacity:0;transform:translateY(10px);transition:all 0.3s;pointer-events:none;max-width:320px"></div>

<script src="../js/admin-common.js"></script>
<script src="../js/admin-bookings.js"></script>
</body>
</html>