<?php
$pageTitle = "BarberBus – My Bookings";
$currentPage = "my_bookings";
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'includes/user_head.php'; ?>
<body class="user-dashboard-body">

  <?php include 'includes/user_navbar.php'; ?>

  <main class="dashboard-container">
    <section class="welcome-section">
      <p class="section-label">Manage Appointments</p>
      <h1>My Bookings</h1>
      <p>Track your appointments, view history, and manage upcoming visits.</p>
    </section>

    <!-- Stats Grid -->
    <section class="stats-grid" style="margin-bottom: 2rem;">
      <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
        <div class="stat-info">
          <h4>Upcoming</h4>
          <div class="stat-value" id="stat-upcoming-bookings">0</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-history"></i></div>
        <div class="stat-info">
          <h4>Total Cuts</h4>
          <div class="stat-value" id="stat-total-bookings">0</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-star"></i></div>
        <div class="stat-info">
          <h4>Loyalty Points</h4>
          <div class="stat-value">120</div>
        </div>
      </div>
    </section>

    <div class="bookings-grid" id="bookings-container">
      <!-- Populated by JS -->
      <div style="text-align:center; padding:3rem;">
        <div class="spinner" style="margin:0 auto; width:40px; height:40px;"></div>
        <p style="margin-top:1rem; color:var(--text-muted);">Loading your bookings...</p>
      </div>
    </div>
  </main>

  <?php include 'includes/user_footer.php'; ?>
  <script src="../js/user-dashboard.js"></script>
  <script src="../js/user-bookings.js"></script>
</body>
</html>
