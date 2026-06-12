<?php
$pageTitle = "BarberBus – Admin Barbers";
$currentPage = "admin";
$currentPanel = "barbers";
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

    <!-- ════════════════════════ BARBERS ════════════════════════ -->
    <div id="panel-barbers" class="panel active">
      <div class="topbar">
        <h1>Barber <span style="color:var(--gold);font-style:italic">Profiles</span></h1>
        <div class="topbar-actions">
          <button class="btn btn-gold" onclick="openBarberModal()"><i class="fas fa-plus"></i> Add Barber</button>
        </div>
      </div>
      <div class="box" style="overflow-x:auto">
        <table class="data-table">
          <thead><tr><th>#</th><th>Name</th><th>Specialty</th><th>Experience</th><th>Rating</th><th>Officer Account</th><th>Status</th><th>Actions</th></tr></thead>
          <tbody id="barbersTable"><tr><td colspan="8" class="loading"><i class="fas fa-spinner fa-spin"></i></td></tr></tbody>
        </table>
      </div>
    </div>

  </main>
</div>

<!-- ── BARBER MODAL ── -->
<div class="modal-overlay" id="barberModal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title" id="barberModalTitle">Add Barber</div>
      <button class="modal-close" onclick="closeModal('barberModal')"><i class="fas fa-times"></i></button>
    </div>
    <div class="form-grid">
      <input type="hidden" id="b-id"/>
      <div class="form-group"><label>Full Name *</label><input type="text" id="b-name" placeholder="Barber name"/></div>
      <div class="form-group"><label>Specialty</label><input type="text" id="b-specialty" placeholder="e.g. Fade &amp; Taper"/></div>
      <div class="form-group"><label>Experience (years)</label><input type="number" id="b-exp" min="0" value="0"/></div>
      <div class="form-group"><label>Rating (1-5)</label><input type="number" id="b-rating" min="1" max="5" step="0.1" value="5.0"/></div>
      <div class="form-group"><label>Status</label>
        <select id="b-active"><option value="1">Active</option><option value="0">Inactive</option></select>
      </div>
      <div class="form-group full"><label>Bio</label><textarea id="b-bio" placeholder="Short biography..."></textarea></div>
    </div>

    <div class="officer-section">
      <div class="officer-section-title"><i class="fas fa-user-shield"></i>&nbsp; Officer Dashboard Access</div>
      <p style="font-size:0.78rem;color:var(--light-gray);margin-bottom:1rem">Set login credentials so this barber can access the Officer Dashboard. Leave blank to skip.</p>
      <div class="form-grid">
        <div class="form-group"><label>Officer Login Email</label><input type="email" id="b-officer-email" placeholder="barber@barberbus.com"/></div>
        <div class="form-group"><label>Password <span id="b-pass-hint" style="color:var(--gray);font-style:italic;display:none">(leave blank to keep)</span></label><input type="password" id="b-officer-pass" placeholder="Min. 6 characters"/></div>
      </div>
    </div>

    <div class="modal-actions">
      <button class="btn btn-outline" onclick="closeModal('barberModal')">Cancel</button>
      <button class="btn btn-gold" onclick="saveBarber()"><i class="fas fa-save"></i> Save Barber</button>
    </div>
  </div>
</div>

<!-- ── TOAST ── -->
<div id="toast" style="position:fixed;bottom:1.5rem;right:1.5rem;z-index:9999;padding:0.85rem 1.4rem;border-radius:8px;font-size:0.85rem;font-weight:500;opacity:0;transform:translateY(10px);transition:all 0.3s;pointer-events:none;max-width:320px"></div>

<script src="../js/admin-common.js"></script>
<script src="../js/admin-barbers.js"></script>
</body>
</html>