<?php
$pageTitle = 'BarberBus – Choose Date & Time';
$currentPage = 'booking';
require_once '../api/config.php';
$pdo = getDB();

$serviceId = intval($_POST['service_id'] ?? 0);
$barberId  = intval($_POST['barber_id'] ?? 0);
$clientName  = trim($_POST['clientName'] ?? '');
$clientEmail = trim($_POST['clientEmail'] ?? '');
$clientPhone = trim($_POST['clientPhone'] ?? '');
$token       = trim($_POST['token'] ?? '');

if (!$serviceId) {
    header('Location: booking.php');
    exit;
}

$service = $pdo->prepare('SELECT id, name, price, duration FROM services WHERE id = ? AND is_active = 1');
$service->execute([$serviceId]);
$service = $service->fetch();
if (!$service) {
    header('Location: booking.php');
    exit;
}

$barber = null;
if ($barberId) {
    $barberStmt = $pdo->prepare('SELECT id, name FROM barbers WHERE id = ? AND is_active = 1');
    $barberStmt->execute([$barberId]);
    $barber = $barberStmt->fetch();
}

$today = date('Y-m-d');
$times = [
    '10:00:00' => '10:00 AM', '10:30:00' => '10:30 AM',
    '11:00:00' => '11:00 AM', '11:30:00' => '11:30 AM',
    '12:00:00' => '12:00 PM', '12:30:00' => '12:30 PM',
    '14:00:00' => '02:00 PM', '14:30:00' => '02:30 PM',
    '15:00:00' => '03:00 PM', '15:30:00' => '03:30 PM',
    '16:00:00' => '04:00 PM', '16:30:00' => '04:30 PM',
    '17:00:00' => '05:00 PM', '17:30:00' => '05:30 PM',
    '18:00:00' => '06:00 PM', '18:30:00' => '06:30 PM',
    '19:00:00' => '07:00 PM', '19:30:00' => '07:30 PM',
    '20:00:00' => '08:00 PM'
];
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'includes/user_head.php'; ?>
<body class="user-dashboard-body">

  <?php include 'includes/user_navbar.php'; ?>

  <main class="page-container">
    <section class="welcome-section">
      <p class="section-label">Step 2 of 4</p>
      <h1>Choose Date & Time</h1>
      <p>Select the appointment date and a time slot that suits you.</p>
    </section>

    <div class="form-card">
      <div class="summary-box">
        <h3>Selected Service</h3>
        <p><strong><?php echo htmlspecialchars($service['name']); ?></strong></p>
        <p>Price: RM <?php echo htmlspecialchars(number_format($service['price'], 0)); ?></p>
        <p>Duration: <?php echo htmlspecialchars($service['duration']); ?> min</p>
        <p>Barber: <strong><?php echo htmlspecialchars($barber['name'] ?? 'Any Available'); ?></strong></p>
      </div>

      <form action="booking_payment.php" method="post">
        <input type="hidden" name="service_id" value="<?php echo htmlspecialchars($serviceId); ?>" />
        <input type="hidden" name="barber_id" value="<?php echo htmlspecialchars($barberId); ?>" />
        <input type="hidden" name="clientName" value="<?php echo htmlspecialchars($clientName); ?>" />
        <input type="hidden" name="clientEmail" value="<?php echo htmlspecialchars($clientEmail); ?>" />
        <input type="hidden" name="clientPhone" value="<?php echo htmlspecialchars($clientPhone); ?>" />
        <input type="hidden" name="token" id="authToken" value="<?php echo htmlspecialchars($token); ?>" />

        <div class="form-group">
          <label for="booking_date">Appointment Date *</label>
          <input type="date" id="booking_date" name="booking_date" min="<?php echo $today; ?>" required />
        </div>

        <div class="form-group">
          <label for="booking_time">Appointment Time *</label>
          <select id="booking_time" name="booking_time" required>
            <option value="">Select a time slot</option>
            <?php foreach ($times as $value => $label): ?>
              <option value="<?php echo $value; ?>"><?php echo $label; ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-actions">
          <a href="booking.php" class="btn btn-outline">← Back</a>
          <button type="submit" class="btn btn-primary">Next: Payment →</button>
        </div>
      </form>
    </div>
  </main>

  <?php include 'includes/user_footer.php'; ?>
  <script src="../js/user-booking-wizard.js"></script>
</body>
</html>
