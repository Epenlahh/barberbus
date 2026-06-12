<?php
require_once 'api/config.php';
$pdo = getDB();

$stmt = $pdo->query("SELECT * FROM barbers WHERE is_active = 1 ORDER BY id ASC");
$barbers = $stmt->fetchAll();

$pageTitle = "BarberBus – Our Barbers";
$currentPage = "barbers";
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'includes/head.php'; ?>
<body>

  <?php include 'includes/navbar.php'; ?>

  <section class="page-hero">
    <div class="page-hero-content">
      <p class="section-label">The Team</p>
      <h1>Our Barbers</h1>
      <p>Masters of their craft. Passionate about every cut.</p>
    </div>
  </section>

  <section class="barbers-section">
    <div class="barbers-grid">

      <?php foreach ($barbers as $barber): ?>
      <?php
        // Generate initials
        $words = explode(' ', $barber['name']);
        $initials = '';
        foreach ($words as $w) {
          if (!empty($w)) $initials .= strtoupper($w[0]);
        }
        $initials = substr($initials, 0, 2);

        // Randomish gradient based on ID
        $gradients = [
            'linear-gradient(135deg, #c9a84c, #8a5c0a)',
            'linear-gradient(135deg, #1a1a2e, #c9a84c)',
            'linear-gradient(135deg, #2d5016, #6aaa2e)',
            'linear-gradient(135deg, #1a1a40, #8a2be2)',
            'linear-gradient(135deg, #5a0000, #c9a84c)'
        ];
        $grad = $gradients[$barber['id'] % count($gradients)];
      ?>
      <div class="barber-card">
        <div class="barber-avatar" style="background: <?php echo $grad; ?>;">
          <span><?php echo htmlspecialchars($initials); ?></span>
        </div>
        <div class="barber-info">
          <div class="barber-badge">Barber</div>
          <h3><?php echo htmlspecialchars($barber['name']); ?></h3>
          <p class="barber-specialty"><?php echo htmlspecialchars($barber['specialty']); ?></p>
          <p class="barber-bio"><?php echo htmlspecialchars($barber['bio']); ?></p>
          <div class="barber-stats">
            <div class="b-stat"><span class="b-num"><?php echo htmlspecialchars($barber['experience']); ?>+</span><span>Years</span></div>
            <div class="b-stat"><span class="b-num">1K+</span><span>Clients</span></div>
            <div class="b-stat"><span class="b-num"><?php echo htmlspecialchars($barber['rating']); ?>★</span><span>Rating</span></div>
          </div>
          <a href="booking.php?barber=<?php echo urlencode(strtolower(str_replace(' ', '', $barber['name']))); ?>" class="btn-primary full">Book with <?php echo htmlspecialchars(explode(' ', $barber['name'])[0]); ?></a>
        </div>
      </div>
      <?php endforeach; ?>

    </div>
  </section>

  <?php include 'includes/footer.php'; ?>
</body>
</html>
