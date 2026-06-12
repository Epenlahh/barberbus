<?php
$pageTitle = "BarberBus – Admin Store Settings";
$currentPage = "admin";
$currentPanel = "store";
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

    <!-- ════════════════════════ STORE SETTINGS ════════════════════════ -->
    <div id="panel-store" class="panel active">
      <div class="topbar">
        <h1>Store <span style="color:var(--gold);font-style:italic">Configuration</span></h1>
        <div class="topbar-actions">
          <button class="btn btn-gold" onclick="openStoreModal()"><i class="fas fa-edit"></i> Edit Store Info</button>
        </div>
      </div>
      <div class="box" id="storeDisplay">
        <div class="loading"><i class="fas fa-spinner fa-spin"></i></div>
      </div>
    </div>

  </main>
</div>

<!-- ── STORE MODAL ── -->
<div class="modal-overlay" id="storeModal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">Edit Store Settings</div>
      <button class="modal-close" onclick="closeModal('storeModal')"><i class="fas fa-times"></i></button>
    </div>
    <div class="form-grid">
      <div class="form-group full"><label>Store Name</label><input type="text" id="store-name"/></div>
      <div class="form-group full"><label>Description</label><textarea id="store-desc"></textarea></div>
      <div class="form-group"><label>Store Status</label>
        <select id="store-open">
          <option value="1">Open</option>
          <option value="0">Closed</option>
        </select>
      </div>
      <div class="form-group"><label>Profile Image URL</label><input type="text" id="store-image"/></div>
      <div class="form-group"><label>Business Hours</label><input type="text" id="store-hours" placeholder="e.g. 10AM - 10PM"/></div>
      <div class="form-group"><label>Location</label><input type="text" id="store-location"/></div>
      <div class="form-group"><label>Phone</label><input type="text" id="store-phone"/></div>
    </div>
    <div class="modal-actions">
      <button class="btn btn-outline" onclick="closeModal('storeModal')">Cancel</button>
      <button class="btn btn-gold" onclick="saveStore()"><i class="fas fa-save"></i> Save Settings</button>
    </div>
  </div>
</div>

<!-- ── TOAST ── -->
<div id="toast" style="position:fixed;bottom:1.5rem;right:1.5rem;z-index:9999;padding:0.85rem 1.4rem;border-radius:8px;font-size:0.85rem;font-weight:500;opacity:0;transform:translateY(10px);transition:all 0.3s;pointer-events:none;max-width:320px"></div>

<script src="../js/admin-common.js"></script>
<script src="../js/admin-store.js"></script>
</body>
</html>