<?php
$pageTitle = 'BarberBus – Customer Lookup';
$currentPanel = 'customers';
$panelTitle = 'Customer Lookup';
$panelSub = 'Search & view customer history';
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'includes/officer_head.php'; ?>
<body>
<?php include 'includes/officer_login.php'; ?>
<div class="app" id="app" style="display:none;">
  <?php include 'includes/officer_sidebar.php'; ?>
  <div class="main">
    <?php include 'includes/officer_topbar.php'; ?>
    <div class="content">
      <div id="panel-customers" class="panel active">
        <div class="grid-2">
          <div class="card">
            <div class="card-header">
              <div class="card-title"><i class="fas fa-magnifying-glass" style="color:var(--gold)"></i> Search Customer</div>
            </div>
            <div class="card-body">
              <div class="search-bar" style="margin-bottom:1rem;">
                <i class="fas fa-search"></i>
                <input type="text" id="customerSearch" placeholder="Name or phone number..." autocomplete="off"/>
              </div>
              <div id="searchResults"></div>
            </div>
          </div>

          <div class="card">
            <div class="card-header">
              <div class="card-title"><i class="fas fa-clock-rotate-left" style="color:var(--gold)"></i> Booking History</div>
            </div>
            <div class="card-body" style="max-height:60vh;overflow-y:auto;">
              <div id="customerHistory">
                <div class="empty-state"><i class="fas fa-user"></i><p>Select a customer to view their history</p></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include 'includes/officer_walkin_modal.php'; ?>
<?php include 'includes/officer_toast.php'; ?>
<script>window.defaultOfficerPanel = 'customers';</script>
<script src="app.js"></script>
</body>
</html>
