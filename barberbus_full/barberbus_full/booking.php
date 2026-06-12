<?php
require_once 'api/config.php';
$pdo = getDB();

$services = $pdo->query("SELECT * FROM services WHERE is_active = 1 ORDER BY id ASC")->fetchAll();
$barbers = $pdo->query("SELECT * FROM barbers WHERE is_active = 1 ORDER BY id ASC")->fetchAll();

$pageTitle = "BarberBus – Book Appointment";
$currentPage = "booking";
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'includes/head.php'; ?>
<body>

  <?php include 'includes/navbar.php'; ?>

  <section class="page-hero">
    <div class="page-hero-content">
      <p class="section-label">Reserve Your Spot</p>
      <h1>Book Appointment</h1>
      <p>Pick your barber, service, date and time in under 2 minutes.</p>
    </div>
  </section>

  <!-- BOOKING + PAYMENT -->
  <section class="booking-section">

    <!-- STEPS INDICATOR -->
    <div class="booking-steps">
      <div class="step active" id="step-indicator-1">
        <div class="step-circle">1</div>
        <span>Details</span>
      </div>
      <div class="step-line"></div>
      <div class="step" id="step-indicator-2">
        <div class="step-circle">2</div>
        <span>Schedule</span>
      </div>
      <div class="step-line"></div>
      <div class="step" id="step-indicator-3">
        <div class="step-circle">3</div>
        <span>Payment</span>
      </div>
      <div class="step-line"></div>
      <div class="step" id="step-indicator-4">
        <div class="step-circle">4</div>
        <span>Confirm</span>
      </div>
    </div>

    <div class="booking-layout">

      <!-- FORM STEPS -->
      <div class="booking-form-area">

        <!-- STEP 1: Personal Info + Service -->
        <div class="booking-step active" id="step1">
          <h3><span class="step-num">01</span> Your Details & Service</h3>

          <div class="form-grid">
            <div class="form-group">
              <label>Full Name *</label>
              <input type="text" id="clientName" placeholder="Ahmad bin Ali" required/>
            </div>
            <div class="form-group">
              <label>Phone Number *</label>
              <input type="tel" id="clientPhone" placeholder="+60 12-345 6789" required/>
            </div>
            <div class="form-group full-width">
              <label>Email Address *</label>
              <input type="email" id="clientEmail" placeholder="your@email.com" required/>
            </div>
          </div>
          <p style="margin-top:1rem; color:var(--text-muted); font-size:0.95rem;">Not logged in? You can still book as a guest. If you're logged in, your name and email will fill automatically.</p>

          <h4 style="margin:2rem 0 1rem;">Choose Service</h4>
          <div class="service-select-grid" id="serviceSelectGrid">
            <?php $firstService = true; ?>
            <?php foreach ($services as $service): ?>
            <?php
              $cat = $service['category'];
              $icon = '<i class="fas fa-cut"></i>';
              if ($cat == 'beard') $icon = '🧔';
              if ($cat == 'treatment') $icon = '<i class="fas fa-spa"></i>';
              if ($cat == 'combo') $icon = '<i class="fas fa-gem"></i>';
              if ($cat == 'colour') $icon = '<i class="fas fa-tint"></i>';
              
              $activeClass = $firstService ? 'selected' : '';
              $firstService = false;
            ?>
            <div class="service-select-card <?php echo $activeClass; ?>" data-service="<?php echo htmlspecialchars($service['name']); ?>" data-service-id="<?php echo $service['id']; ?>" data-price="<?php echo htmlspecialchars(number_format($service['price'], 0)); ?>" data-duration="<?php echo htmlspecialchars($service['duration']); ?>">
              <?php echo $icon; ?>
              <span><?php echo htmlspecialchars($service['name']); ?></span>
              <strong>RM <?php echo htmlspecialchars(number_format($service['price'], 0)); ?></strong>
            </div>
            <?php endforeach; ?>
          </div>

          <h4 style="margin:2rem 0 1rem;">Choose Barber</h4>
          <div class="barber-select-grid" id="barberSelectGrid">
            <div class="barber-select-card selected" data-barber="Any Available" data-barber-id="">
              <div class="bsc-avatar" style="background:#c9a84c;">★</div>
              <span>Any Available</span>
            </div>
            <?php foreach ($barbers as $barber): ?>
            <?php
              $words = explode(' ', $barber['name']);
              $initials = '';
              foreach ($words as $w) {
                if (!empty($w)) $initials .= strtoupper($w[0]);
              }
              $initials = substr($initials, 0, 2);
              $gradients = ['linear-gradient(135deg,#c9a84c,#8a5c0a)','linear-gradient(135deg,#1a1a2e,#c9a84c)','linear-gradient(135deg,#2d5016,#6aaa2e)','linear-gradient(135deg,#1a1a40,#8a2be2)','linear-gradient(135deg,#5a0000,#c9a84c)'];
              $grad = $gradients[$barber['id'] % count($gradients)];
            ?>
            <div class="barber-select-card" data-barber="<?php echo htmlspecialchars($barber['name']); ?>" data-barber-id="<?php echo $barber['id']; ?>">
              <div class="bsc-avatar" style="background:<?php echo $grad; ?>;"><?php echo htmlspecialchars($initials); ?></div>
              <span><?php echo htmlspecialchars(explode(' ', $barber['name'])[0]); ?></span>
            </div>
            <?php endforeach; ?>
          </div>

          <div class="form-group" style="margin-top:1.5rem;">
            <label>Special Requests (optional)</label>
            <textarea id="notes" rows="3" placeholder="Any specific style, references, or notes for your barber..."></textarea>
          </div>

          <button class="btn-primary full" onclick="nextStep(2)">Next: Choose Time →</button>
        </div>

        <!-- STEP 2: Date & Time -->
        <div class="booking-step" id="step2">
          <h3><span class="step-num">02</span> Pick a Date & Time</h3>
          <div class="calendar-section">
            <div class="calendar-header">
              <button onclick="changeMonth(-1)" class="cal-nav">‹</button>
              <h4 id="calMonthYear"></h4>
              <button onclick="changeMonth(1)" class="cal-nav">›</button>
            </div>
            <div class="calendar-grid" id="calendarGrid"></div>
          </div>

          <div class="time-slots" id="timeSlots">
            <h4>Available Times</h4>
            <div class="slots-grid" id="slotsGrid"></div>
          </div>

          <div class="step-btns">
            <button class="btn-outline" onclick="nextStep(1)">← Back</button>
            <button class="btn-primary" onclick="nextStep(3)">Next: Payment →</button>
          </div>
        </div>

        <!-- STEP 3: Payment -->
        <div class="booking-step" id="step3">
          <h3><span class="step-num">03</span> Payment</h3>

          <div class="payment-methods">
            <h4>Select Payment Method</h4>
            <div class="payment-grid">
              <div class="payment-card selected" data-pay="cash">
                <i class="fas fa-money-bill-wave"></i>
                <span>Pay at Shop</span>
              </div>
              <div class="payment-card" data-pay="qr">
                <i class="fas fa-qrcode"></i>
                <span>QR Payment</span>
              </div>
            </div>
          </div>

          <!-- CASH PANEL -->
          <div class="pay-panel active" id="panel-cash">
            <div class="cash-info">
              <i class="fas fa-store" style="font-size:3rem;color:var(--gold);margin-bottom:1rem;"></i>
              <h4>Pay at the Shop</h4>
              <p>Your appointment will be reserved. Please arrive on time and pay at the counter.</p>
              <p>We accept <strong>cash</strong> and <strong>DuitNow QR</strong> at the shop.</p>
            </div>
          </div>

          <!-- QR PANEL -->
          <div class="pay-panel" id="panel-qr">
            <div class="cash-info">
              <i class="fas fa-qrcode" style="font-size:3rem;color:var(--gold);margin-bottom:1rem;"></i>
              <h4>QR Payment</h4>
              <p>Please scan the shop's DuitNow QR code when you arrive to complete payment.</p>
            </div>
          </div>

          <div class="step-btns">
            <button class="btn-outline" onclick="nextStep(2)">← Back</button>
            <button class="btn-primary" onclick="submitBooking()">Confirm Booking →</button>
          </div>
        </div>

        <!-- STEP 4: Confirmation -->
        <div class="booking-step" id="step4">
          <div class="confirm-box">
            <div class="confirm-icon">✓</div>
            <h3>Booking Confirmed!</h3>
            <p>Your appointment has been successfully booked. A confirmation will be sent to your email.</p>

            <div class="confirm-summary" id="confirmSummary"></div>

            <div class="confirm-ref">
              Booking Ref: <strong id="bookingRef"></strong>
            </div>

            <div class="confirm-btns">
              <a href="index.php" class="btn-outline">Back to Home</a>
              <button onclick="window.print()" class="btn-primary">Print Receipt</button>
            </div>
          </div>
        </div>

      </div>

      <!-- BOOKING SUMMARY SIDEBAR -->
      <div class="booking-summary" id="bookingSummary">
        <h4>Booking Summary</h4>
        <div class="summary-item">
          <span>Service</span>
          <strong id="sum-service">Classic Haircut</strong>
        </div>
        <div class="summary-item">
          <span>Barber</span>
          <strong id="sum-barber">Any Available</strong>
        </div>
        <div class="summary-item">
          <span>Date</span>
          <strong id="sum-date">Not selected</strong>
        </div>
        <div class="summary-item">
          <span>Time</span>
          <strong id="sum-time">Not selected</strong>
        </div>
        <div class="summary-item">
          <span>Duration</span>
          <strong id="sum-duration">30 min</strong>
        </div>
        <div class="summary-divider"></div>
        <div class="summary-item total">
          <span>Total</span>
          <strong id="sum-total">RM 25</strong>
        </div>
      </div>

    </div>
  </section>

  <!-- Toast -->
  <div class="toast" id="toast"></div>

  <?php include 'includes/footer.php'; ?>
  <script src="js/booking.js"></script>
</body>
</html>
