<?php
$pageTitle = "BarberBus – Admin Users";
$currentPage = "admin";
$currentPanel = "users";
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

    <!-- ════════════════════════ USERS ════════════════════════ -->
    <div id="panel-users" class="panel active">
      <div class="topbar">
        <h1>User <span style="color:var(--gold);font-style:italic">Management</span></h1>
        <div class="topbar-actions">
          <div class="search-box"><i class="fas fa-search"></i><input type="text" id="userSearch" placeholder="Search users..." oninput="filterTable('userSearch','usersTable')"/></div>
        </div>
      </div>
      <div class="box" style="overflow-x:auto">
        <table class="data-table">
          <thead><tr><th>#</th><th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th>Joined</th><th>Bookings</th><th>Actions</th></tr></thead>
          <tbody id="usersTable"><tr><td colspan="8" class="loading"><i class="fas fa-spinner fa-spin"></i></td></tr></tbody>
        </table>
      </div>
    </div>

  </main>
</div>

<!-- ── TOAST ── -->
<div id="toast" style="position:fixed;bottom:1.5rem;right:1.5rem;z-index:9999;padding:0.85rem 1.4rem;border-radius:8px;font-size:0.85rem;font-weight:500;opacity:0;transform:translateY(10px);transition:all 0.3s;pointer-events:none;max-width:320px"></div>

<script src="../js/admin-common.js"></script>
<script src="../js/admin-users.js"></script>
</body>
</html>