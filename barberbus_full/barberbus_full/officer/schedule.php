<?php
$pageTitle = 'BarberBus – Today\'s Schedule';
$currentPanel = 'schedule';
$panelTitle = 'Today\'s Schedule';
$panelSub = 'Full booking timeline';
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
      <div id="panel-schedule" class="panel active">
        <div class="grid-4060">
          <div class="card">
            <div class="card-header">
              <div class="card-title"><i class="fas fa-timeline" style="color:var(--gold)"></i> Timeline View</div>
            </div>
            <div class="card-body" id="timelineView" style="max-height:70vh;overflow-y:auto;">
              <div class="empty-state"><i class="fas fa-spinner fa-spin"></i></div>
            </div>
          </div>

          <div class="card">
            <div class="card-header">
              <div class="card-title"><i class="fas fa-table-list" style="color:var(--gold)"></i> Full Schedule</div>
            </div>
            <div class="card-body no-pad" style="max-height:70vh;overflow-y:auto;">
              <div class="table-wrap">
                <table>
                  <thead>
                    <tr><th>Time</th><th>Customer</th><th>Service</th><th>Barber</th><th>Dur.</th><th>Price</th><th>Status</th><th>Action</th></tr>
                  </thead>
                  <tbody id="scheduleBody">
                    <tr><td colspan="8" style="text-align:center;padding:2rem;color:var(--muted)"><i class="fas fa-spinner fa-spin"></i></td></tr>
                  </tbody>
                </table>
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
<script>window.defaultOfficerPanel = 'schedule';</script>
<script src="app.js"></script>
</body>
</html>
