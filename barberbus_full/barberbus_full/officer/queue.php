<?php
$pageTitle = 'BarberBus – Live Queue';
$currentPanel = 'queue';
$panelTitle = 'Live Queue';
$panelSub = 'Today\'s customer queue';
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
      <div id="panel-queue" class="panel active">
        <div class="card">
          <div class="card-header">
            <div class="card-title"><span class="dot"></span> Live Queue — Today's Pending & Confirmed</div>
            <button class="btn btn-gold btn-sm" onclick="openWalkIn()"><i class="fas fa-plus"></i> Walk-In</button>
          </div>
          <div class="card-body no-pad">
            <div class="table-wrap">
              <table>
                <thead>
                  <tr>
                    <th>#ID</th><th>Customer</th><th>Service</th><th>Barber</th><th>Time</th><th>Price</th><th>Status</th><th>Actions</th>
                  </tr>
                </thead>
                <tbody id="allBookingsBody">
                  <tr><td colspan="8" style="text-align:center;padding:2rem;color:var(--muted)"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include 'includes/officer_walkin_modal.php'; ?>
<?php include 'includes/officer_toast.php'; ?>
<script>window.defaultOfficerPanel = 'queue';</script>
<script src="app.js"></script>
</body>
</html>
