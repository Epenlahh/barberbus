<?php
$pageTitle = "BarberBus – Admin Promotions";
$currentPage = "admin";
$currentPanel = "promotions";
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

    <!-- ════════════════════════ PROMOTIONS ════════════════════════ -->
    <div id="panel-promotions" class="panel active">
      <div class="topbar">
        <h1>Site <span style="color:var(--gold);font-style:italic">Promotions</span></h1>
        <div class="topbar-actions">
          <button class="btn btn-gold" onclick="openPromotionModal()"><i class="fas fa-plus"></i> Add Promotion</button>
        </div>
      </div>
      <div class="box" style="overflow-x:auto">
        <table class="data-table">
          <thead><tr><th>#</th><th>Title</th><th>Description</th><th>Status</th><th>Starts</th><th>Ends</th><th>Actions</th></tr></thead>
          <tbody id="promotionsTable"><tr><td colspan="7" class="loading"><i class="fas fa-spinner fa-spin"></i></td></tr></tbody>
        </table>
      </div>
    </div>

  </main>
</div>

<!-- ── PROMOTION MODAL ── -->
<div class="modal-overlay" id="promotionModal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title" id="promotionModalTitle">Add Promotion</div>
      <button class="modal-close" onclick="closeModal('promotionModal')"><i class="fas fa-times"></i></button>
    </div>
    <div class="form-grid">
      <input type="hidden" id="promo-id"/>
      <div class="form-group full"><label>Promotion Title *</label><input type="text" id="promo-title" placeholder="e.g. 50% Off First Cut"/></div>
      <div class="form-group full"><label>Description</label><textarea id="promo-desc" placeholder="Promotion details..."></textarea></div>
      <div class="form-group full"><label>Image URL</label><input type="text" id="promo-image" placeholder="https://..."/></div>
      <div class="form-group"><label>Start Date</label><input type="date" id="promo-start"/></div>
      <div class="form-group"><label>End Date</label><input type="date" id="promo-end"/></div>
      <div class="form-group"><label>Status</label>
        <select id="promo-active">
          <option value="1">Active</option>
          <option value="0">Inactive</option>
        </select>
      </div>
      <div class="form-group full"><label>Promo Link (optional)</label><input type="text" id="promo-link" placeholder="https://..."/></div>
    </div>
    <div class="modal-actions">
      <button class="btn btn-outline" onclick="closeModal('promotionModal')">Cancel</button>
      <button class="btn btn-gold" onclick="savePromotion()"><i class="fas fa-save"></i> Save Promotion</button>
    </div>
  </div>
</div>

<!-- ── TOAST ── -->
<div id="toast" style="position:fixed;bottom:1.5rem;right:1.5rem;z-index:9999;padding:0.85rem 1.4rem;border-radius:8px;font-size:0.85rem;font-weight:500;opacity:0;transform:translateY(10px);transition:all 0.3s;pointer-events:none;max-width:320px"></div>

<script src="../js/admin-common.js"></script>
<script src="../js/admin-promotions.js"></script>
</body>
</html>