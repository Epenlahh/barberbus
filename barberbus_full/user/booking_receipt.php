<?php
$pageTitle = 'BarberBus – Booking Receipt';
$currentPage = 'booking';
require_once '../api/config.php';
$pdo = getDB();

$error = '';
$booking = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token       = trim($_POST['token'] ?? '');
    $userPayload = verifyToken($token);
    if (!$userPayload) {
        $error = 'Your session expired. Please log in again.';
    }

    $serviceId   = intval($_POST['service_id'] ?? 0);
    $barberIdRaw = trim($_POST['barber_id'] ?? '0');
    $bookingDate = trim($_POST['booking_date'] ?? '');
    $bookingTime = trim($_POST['booking_time'] ?? '');
    $payMethod   = trim($_POST['pay_method'] ?? 'cash');
    $notes       = trim($_POST['notes'] ?? '');

    if (!$error) {
        if (!$serviceId || !$bookingDate || !$bookingTime || !$userPayload) {
            $error = 'Missing booking information. Please start again.';
        }
    }

    if (!$error) {
        $serviceStmt = $pdo->prepare('SELECT id, name, price, duration FROM services WHERE id = ? AND is_active = 1');
        $serviceStmt->execute([$serviceId]);
        $service = $serviceStmt->fetch();
        if (!$service) {
            $error = 'Selected service is unavailable. Please choose again.';
        }
    }

    $barberId = $barberIdRaw !== '' ? intval($barberIdRaw) : null;
    if ($barberId && !$error) {
        $barberStmt = $pdo->prepare('SELECT id, name FROM barbers WHERE id = ? AND is_active = 1');
        $barberStmt->execute([$barberId]);
        $barber = $barberStmt->fetch();
        if (!$barber) {
            $error = 'Selected barber is unavailable. Please choose again.';
        }
    } else {
        $barber = null;
        $barberId = null;
    }

    if (!$error) {
        $conflictSql = 'SELECT id FROM bookings WHERE booking_date = ? AND booking_time = ? AND status != "cancelled"';
        $conflictParams = [$bookingDate, $bookingTime];
        if ($barberId !== null) {
            $conflictSql .= ' AND barber_id = ?';
            $conflictParams[] = $barberId;
        }
        $conflictStmt = $pdo->prepare($conflictSql);
        $conflictStmt->execute($conflictParams);
        if ($conflictStmt->fetch()) {
            $error = 'The chosen time slot is already taken. Please select another time or barber.';
        }
    }

    if (!$error) {
        $insert = $pdo->prepare('INSERT INTO bookings (user_id, barber_id, service_id, booking_date, booking_time, total_price, pay_method, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $insert->execute([
            intval($userPayload['sub']),
            $barberId,
            $serviceId,
            $bookingDate,
            $bookingTime,
            $service['price'],
            $payMethod,
            $notes
        ]);
        $bookingId = $pdo->lastInsertId();
        header('Location: booking_receipt.php?booking_id=' . intval($bookingId));
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['booking_id'])) {
    $bookingId = intval($_GET['booking_id']);
    $stmt = $pdo->prepare('SELECT b.id, b.booking_date, b.booking_time, b.status, b.total_price, b.pay_method, b.notes,
                                  s.name AS service_name, s.duration AS service_duration,
                                  br.name AS barber_name,
                                  u.name AS customer_name, u.email AS customer_email, u.phone AS customer_phone
                           FROM bookings b
                           JOIN services s ON b.service_id = s.id
                           LEFT JOIN barbers br ON b.barber_id = br.id
                           JOIN users u ON b.user_id = u.id
                           WHERE b.id = ?');
    $stmt->execute([$bookingId]);
    $booking = $stmt->fetch();
    if (!$booking) {
        $error = 'Booking not found. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'includes/user_head.php'; ?>
<body class="user-dashboard-body">

  <?php include 'includes/user_navbar.php'; ?>

  <main class="page-container">
    <section class="welcome-section">
      <p class="section-label">Step 4 of 4</p>
      <h1>Booking Receipt</h1>
      <p>Your appointment has been confirmed. The officer dashboard will receive your booking instantly.</p>
    </section>

    <?php if ($error): ?>
      <div class="alert alert-error">
        <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
      </div>
      <div class="form-actions">
        <a href="booking.php" class="btn btn-primary">Start Again</a>
      </div>
    <?php elseif ($booking): ?>
      <div class="confirm-box">
        <div class="confirm-icon">✓</div>
        <h3>Booking Confirmed!</h3>
        <p>Thank you, <?php echo htmlspecialchars($booking['customer_name']); ?>. Your appointment is now in our system.</p>

        <div class="confirm-summary">
          <div class="summary-item"><span>Booking Ref</span><strong>BB-<?php echo htmlspecialchars($booking['id']); ?></strong></div>
          <div class="summary-item"><span>Service</span><strong><?php echo htmlspecialchars($booking['service_name']); ?></strong></div>
          <div class="summary-item"><span>Barber</span><strong><?php echo htmlspecialchars($booking['barber_name'] ?? 'Any Available'); ?></strong></div>
          <div class="summary-item"><span>Date</span><strong><?php echo htmlspecialchars($booking['booking_date']); ?></strong></div>
          <div class="summary-item"><span>Time</span><strong><?php echo htmlspecialchars(date('g:i A', strtotime($booking['booking_time']))); ?></strong></div>
          <div class="summary-item"><span>Duration</span><strong><?php echo htmlspecialchars($booking['service_duration']); ?> min</strong></div>
          <div class="summary-item"><span>Payment</span><strong><?php echo htmlspecialchars(strtoupper(str_replace('_', ' ', $booking['pay_method']))); ?></strong></div>
          <?php if ($booking['notes']): ?>
            <div class="summary-item"><span>Notes</span><strong><?php echo nl2br(htmlspecialchars($booking['notes'])); ?></strong></div>
          <?php endif; ?>
          <div class="summary-divider"></div>
          <div class="summary-item total"><span>Total Paid</span><strong>RM <?php echo htmlspecialchars(number_format($booking['total_price'], 0)); ?></strong></div>
        </div>

        <div class="confirm-ref">
          Officer staff will see this new booking on their dashboard immediately.
        </div>

        <div class="confirm-btns">
          <a href="dashboard.php" class="btn btn-primary">Done</a>
        </div>
      </div>
    <?php else: ?>
      <div class="alert alert-error">
        <strong>Notice:</strong> No booking information is available. Please restart the booking process.
      </div>
      <div class="form-actions">
        <a href="booking.php" class="btn btn-primary">Start Booking</a>
      </div>
    <?php endif; ?>
  </main>

  <?php include 'includes/user_footer.php'; ?>
  <script src="../js/user-booking-wizard.js"></script>
</body>
</html>
