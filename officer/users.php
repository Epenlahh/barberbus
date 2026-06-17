<?php
$pageTitle = 'BarberBus – User Accounts';
$currentPanel = 'users';
$panelTitle = 'User Accounts';
$panelSub = 'View registered users';
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
      <div id="panel-users" class="panel active">
        <div class="card">
          <div class="card-header">
            <div class="card-title"><i class="fas fa-users" style="color:var(--gold)"></i> User Accounts</div>
            <div class="search-bar">
              <i class="fas fa-search"></i>
              <input type="text" id="userSearch" placeholder="Search users..." oninput="filterUsers()"/>
            </div>
          </div>
          <div class="card-body no-pad">
            <table class="data-table">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Phone</th>
                  <th>Role</th>
                  <th>Joined</th>
                  <th>Bookings</th>
                </tr>
              </thead>
              <tbody id="usersTable">
                <tr><td colspan="7" class="loading"><i class="fas fa-spinner fa-spin"></i> Loading users...</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include 'includes/officer_walkin_modal.php'; ?>
<?php include 'includes/officer_toast.php'; ?>
<script>window.defaultOfficerPanel = 'users';</script>
<script src="app.js"></script>
</body>
</html>
