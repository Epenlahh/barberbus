<?php
$pageTitle = "BarberBus – Admin Services";
$currentPage = "admin";
$currentPanel = "services";
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

    <!-- ════════════════════════ SERVICES ════════════════════════ -->
    <div id="panel-services" class="panel active">
      <div class="topbar">
        <h1>Service <span style="color:var(--gold);font-style:italic">List</span></h1>
        <div class="topbar-actions">
          <button class="btn btn-gold" onclick="openServiceModal()"><i class="fas fa-plus"></i> Add Service</button>
        </div>
      </div>
      <div class="box" style="overflow-x:auto">
        <table class="data-table">
          <thead><tr><th>#</th><th>Service</th><th>Category</th><th>Duration</th><th>Price</th><th>Status</th><th>Actions</th></tr></thead>
          <tbody id="servicesTable"><tr><td colspan="7" class="loading"><i class="fas fa-spinner fa-spin"></i></td></tr></tbody>
        </table>
      </div>
    </div>

  </main>
</div>

<!-- ── SERVICE MODAL ── -->
<div class="modal-overlay" id="serviceModal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title" id="serviceModalTitle">Add Service</div>
      <button class="modal-close" onclick="closeModal('serviceModal')"><i class="fas fa-times"></i></button>
    </div>
    <div class="form-grid">
      <input type="hidden" id="s-id"/>
      <div class="form-group full"><label>Service Name *</label><input type="text" id="s-name" placeholder="e.g. Classic Haircut"/></div>
      <div class="form-group"><label>Price (RM) *</label><input type="number" id="s-price" min="0" step="0.50" placeholder="25.00"/></div>
      <div class="form-group"><label>Duration (mins) *</label><input type="number" id="s-duration" min="5" step="5" placeholder="30"/></div>
      <div class="form-group"><label>Category</label>
        <select id="s-category">
          <option value="haircut">Haircut</option>
          <option value="shave">Shave & Beard</option>
          <option value="treatment">Treatment</option>
          <option value="other">Other</option>
        </select>
      </div>
      <div class="form-group"><label>Status</label>
        <select id="s-active"><option value="1">Active</option><option value="0">Inactive</option></select>
      </div>
      <div class="form-group full"><label>Description</label><textarea id="s-desc" placeholder="Service details..."></textarea></div>
    </div>
    <div class="modal-actions">
      <button class="btn btn-outline" onclick="closeModal('serviceModal')">Cancel</button>
      <button class="btn btn-gold" onclick="saveService()"><i class="fas fa-save"></i> Save Service</button>
    </div>
  </div>
</div>

<!-- ── TOAST ── -->
<div id="toast" style="position:fixed;bottom:1.5rem;right:1.5rem;z-index:9999;padding:0.85rem 1.4rem;border-radius:8px;font-size:0.85rem;font-weight:500;opacity:0;transform:translateY(10px);transition:all 0.3s;pointer-events:none;max-width:320px"></div>

<script src="../js/admin-common.js"></script>
<script src="../js/admin-services.js"></script>
</body>
</html>