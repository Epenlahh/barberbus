<?php
$pageTitle = "BarberBus – Admin Pages";
$currentPage = "admin";
$currentPanel = "pages";
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

    <!-- ════════════════════════ PAGES ════════════════════════ -->
    <div id="panel-pages" class="panel active">
      <div class="topbar">
        <h1>Pages & <span style="color:var(--gold);font-style:italic">Content</span></h1>
      </div>
      <div class="box">
        <div class="tabs">
          <button class="tab-btn active" onclick="switchContentPage('dashboard', this)">Dashboard</button>
          <button class="tab-btn" onclick="switchContentPage('booking', this)">Booking Page</button>
          <button class="tab-btn" onclick="switchContentPage('fashion', this)">Fashion Page</button>
          <button class="tab-btn" onclick="switchContentPage('tryon', this)">Try-on Page</button>
        </div>
        <div id="pageContent" style="padding-top:1.5rem">
          <div class="loading"><i class="fas fa-spinner fa-spin"></i></div>
        </div>
      </div>
    </div>

  </main>
</div>

<!-- ── PAGE CONTENT MODAL ── -->
<div class="modal-overlay" id="pageModal">
  <div class="modal" style="max-width:700px">
    <div class="modal-header">
      <div class="modal-title" id="pageModalTitle">Edit Page</div>
      <button class="modal-close" onclick="closeModal('pageModal')"><i class="fas fa-times"></i></button>
    </div>
    <div class="form-grid">
      <input type="hidden" id="page-name"/>
      <div class="form-group full"><label>Page Title</label><input type="text" id="page-title"/></div>
      <div class="form-group full"><label>Hero Text</label><input type="text" id="page-hero"/></div>
      <div class="form-group full"><label>Main Content</label><textarea id="page-content" style="height:200px"></textarea></div>
      <div class="form-group full"><label>Footer Note</label><input type="text" id="page-footer"/></div>
    </div>
    <div class="modal-actions">
      <button class="btn btn-outline" onclick="closeModal('pageModal')">Cancel</button>
      <button class="btn btn-gold" onclick="savePageContent()"><i class="fas fa-save"></i> Save Content</button>
    </div>
  </div>
</div>

<!-- ── TOAST ── -->
<div id="toast" style="position:fixed;bottom:1.5rem;right:1.5rem;z-index:9999;padding:0.85rem 1.4rem;border-radius:8px;font-size:0.85rem;font-weight:500;opacity:0;transform:translateY(10px);transition:all 0.3s;pointer-events:none;max-width:320px"></div>

<script src="../js/admin-common.js"></script>
<script src="../js/admin-pages.js"></script>
</body>
</html>