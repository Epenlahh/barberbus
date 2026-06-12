<?php
require_once '../api/config.php';
$pdo = getDB();
$barbers = $pdo->query("SELECT * FROM barbers WHERE is_active = 1 ORDER BY id ASC")->fetchAll();

$pageTitle = "BarberBus – Our Barbers";
$currentPage = "barbers";
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'includes/user_head.php'; ?>
<body class="user-dashboard-body">

  <?php include 'includes/user_navbar.php'; ?>

  <main class="dashboard-container">
    <section class="welcome-section">
      <p class="section-label">Professionals</p>
      <h1>Our Expert Barbers</h1>
      <p>Masters of their craft. Each specializing in unique styles and techniques.</p>
    </section>

    <div class="barbers-grid">
      <?php foreach ($barbers as $barber): ?>
      <?php
        $words = explode(' ', $barber['name']);
        $initials = '';
        foreach ($words as $w) if (!empty($w)) $initials .= strtoupper($w[0]);
        $initials = substr($initials, 0, 2);
        
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
        <div class="barber-avatar" style="background: <?php echo $grad; ?>; width:80px; height:80px; font-size:1.5rem; margin-bottom:1rem;">
          <span><?php echo htmlspecialchars($initials); ?></span>
        </div>
        <div class="barber-info">
          <span class="barber-profession"><?php echo htmlspecialchars($barber['specialty']); ?></span>
          <h3 style="font-size:1.5rem; margin-bottom:0.5rem;"><?php echo htmlspecialchars($barber['name']); ?></h3>
          <p class="barber-bio" style="color:var(--text-muted); margin-bottom:1.2rem; font-size:0.9rem;">
            <?php echo htmlspecialchars($barber['bio']); ?>
          </p>
          <div class="barber-stats" style="margin-bottom:1.5rem;">
            <div class="b-stat"><span class="b-num"><?php echo htmlspecialchars($barber['experience']); ?>+</span><span>Years</span></div>
            <div class="b-stat"><span class="b-num"><?php echo htmlspecialchars($barber['rating']); ?>★</span><span>Rating</span></div>
          </div>
          <a href="booking.php?barber=<?php echo urlencode(strtolower(str_replace(' ', '', $barber['name']))); ?>" class="btn-primary full">Book with <?php echo htmlspecialchars(explode(' ', $barber['name'])[0]); ?></a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </main>

  <?php include 'includes/user_footer.php'; ?>
  <script src="../js/user-dashboard.js"></script>
</body>
</html>
