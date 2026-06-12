<?php
$pageTitle = 'BarberBus – Payment';
$currentPage = 'booking';
require_once '../api/config.php';
$pdo = getDB();

$serviceId   = intval($_POST['service_id'] ?? 0);
$barberId    = intval($_POST['barber_id'] ?? 0);
$bookingDate = trim($_POST['booking_date'] ?? '');
$bookingTime = trim($_POST['booking_time'] ?? '');
$clientName  = trim($_POST['clientName'] ?? '');
$clientEmail = trim($_POST['clientEmail'] ?? '');
$clientPhone = trim($_POST['clientPhone'] ?? '');
$token       = trim($_POST['token'] ?? '');

if (!$serviceId || !$bookingDate || !$bookingTime) {
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

$paymentMethods = [
    'cash' => 'Pay at Shop',
    'qr'   => 'DuitNow QR'
];
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'includes/user_head.php'; ?>
<body class="user-dashboard-body">

  <?php include 'includes/user_navbar.php'; ?>

  <main class="page-container">
    <section class="welcome-section">
      <p class="section-label">Step 3 of 4</p>
      <h1>Payment & Confirmation</h1>
      <p>Choose how you’d like to pay and review your appointment details.</p>
    </section>

    <div class="form-card">
      <div class="summary-box">
        <h3>Review Your Booking</h3>
        <p><strong><?php echo htmlspecialchars($service['name']); ?></strong></p>
        <p>Barber: <?php echo htmlspecialchars($barber['name'] ?? 'Any Available'); ?></p>
        <p>Date: <?php echo htmlspecialchars($bookingDate); ?></p>
        <p>Time: <?php echo htmlspecialchars($bookingTime); ?></p>
        <p>Price: RM <?php echo htmlspecialchars(number_format($service['price'], 0)); ?></p>
      </div>

      <form action="booking_receipt.php" method="post">
        <input type="hidden" name="service_id" value="<?php echo htmlspecialchars($serviceId); ?>" />
        <input type="hidden" name="barber_id" value="<?php echo htmlspecialchars($barberId); ?>" />
        <input type="hidden" name="booking_date" value="<?php echo htmlspecialchars($bookingDate); ?>" />
        <input type="hidden" name="booking_time" value="<?php echo htmlspecialchars($bookingTime); ?>" />
        <input type="hidden" name="clientName" value="<?php echo htmlspecialchars($clientName); ?>" />
        <input type="hidden" name="clientEmail" value="<?php echo htmlspecialchars($clientEmail); ?>" />
        <input type="hidden" name="clientPhone" value="<?php echo htmlspecialchars($clientPhone); ?>" />
        <input type="hidden" name="token" id="authToken" value="<?php echo htmlspecialchars($token); ?>" />

        <div class="form-group">
          <label>Payment Method *</label>
          <?php foreach ($paymentMethods as $value => $label): ?>
            <label class="radio-inline">
              <input type="radio" name="pay_method" value="<?php echo $value; ?>" <?php echo $value === 'cash' ? 'checked' : ''; ?> required>
              <?php echo htmlspecialchars($label); ?>
            </label>
          <?php endforeach; ?>
        </div>

        <div class="form-group">
          <label for="notes">Special Requests (optional)</label>
          <textarea id="notes" name="notes" rows="4" placeholder="Any style notes or requests..."></textarea>
        </div>

        <div class="form-actions">
          <button type="button" onclick="history.back()" class="btn btn-outline">← Back</button>
          <button type="submit" class="btn btn-primary">Confirm Booking →</button>
        </div>
      </form>
    </div>
  </main>

  <?php include 'includes/user_footer.php'; ?>
  <script src="../js/user-booking-wizard.js"></script>
</body>
</html>
