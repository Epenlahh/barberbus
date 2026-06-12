<?php
$pageTitle = 'BarberBus – Book Appointment';
$currentPage = 'booking';
require_once '../api/config.php';
$pdo = getDB();

$services = $pdo->query('SELECT * FROM services WHERE is_active = 1 ORDER BY id ASC')->fetchAll();
$barbers  = $pdo->query('SELECT * FROM barbers WHERE is_active = 1 ORDER BY id ASC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'includes/user_head.php'; ?>
<body class="user-dashboard-body">

  <?php include 'includes/user_navbar.php'; ?>

  <main class="page-container">
    <section class="welcome-section">
      <p class="section-label">Step 1 of 4</p>
      <h1>Book Appointment</h1>
      <p>Please confirm your details and choose a service and barber.</p>
    </section>

    <div class="form-card">
      <form action="booking_schedule.php" method="post">
        <input type="hidden" name="token" id="authToken" value="" />

        <div class="form-group">
          <label for="clientName">Full Name *</label>
          <input type="text" id="clientName" name="clientName" data-prefill="name" placeholder="Ahmad bin Ali" required />
        </div>

        <div class="form-group">
          <label for="clientPhone">Phone Number *</label>
          <input type="tel" id="clientPhone" name="clientPhone" data-prefill="phone" placeholder="+60 12-345 6789" required />
        </div>

        <div class="form-group">
          <label for="clientEmail">Email Address *</label>
          <input type="email" id="clientEmail" name="clientEmail" data-prefill="email" placeholder="your@email.com" required />
        </div>

        <div class="section-divider"></div>

        <div class="form-group">
          <label>Choose Service *</label>
          <div class="card-grid">
            <?php foreach ($services as $service): ?>
              <label class="select-card">
                <input type="radio" name="service_id" value="<?php echo htmlspecialchars($service['id']); ?>" required />
                <div>
                  <strong><?php echo htmlspecialchars($service['name']); ?></strong>
                  <p>RM <?php echo htmlspecialchars(number_format($service['price'], 0)); ?> · <?php echo htmlspecialchars($service['duration']); ?> min</p>
                </div>
              </label>
            <?php endforeach; ?>
          </div>
        </div>

        <div class="form-group">
          <label>Choose Barber</label>
          <div class="card-grid">
            <label class="select-card">
              <input type="radio" name="barber_id" value="0" checked />
              <div>
                <strong>Any Available</strong>
                <p>Fastest appointment assignment</p>
              </div>
            </label>
            <?php foreach ($barbers as $barber): ?>
              <label class="select-card">
                <input type="radio" name="barber_id" value="<?php echo htmlspecialchars($barber['id']); ?>" />
                <div>
                  <strong><?php echo htmlspecialchars($barber['name']); ?></strong>
                  <p><?php echo htmlspecialchars($barber['specialty'] ?? 'Barber'); ?></p>
                </div>
              </label>
            <?php endforeach; ?>
          </div>
        </div>

        <div class="form-actions">
          <button type="submit" class="btn btn-primary">Next: Choose Time →</button>
        </div>
      </form>
    </div>
  </main>

  <?php include 'includes/user_footer.php'; ?>
  <script src="../js/user-booking-wizard.js"></script>
</body>
</html>
