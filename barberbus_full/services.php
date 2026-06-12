<?php
require_once __DIR__ . '/includes/init.php';
require_once 'api/config.php';
$pdo = getDB();

$stmt = $pdo->query("SELECT * FROM services WHERE is_active = 1 ORDER BY id ASC");
$services = $stmt->fetchAll();

$servicesContent = getPageContent('services');
$pageTitle = htmlspecialchars($storeSettings['name'] ?? 'BarberBus') . " – " . htmlspecialchars($servicesContent['title'] ?? 'Services');
$currentPage = "services";
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'includes/head.php'; ?>
<body>

  <?php include 'includes/navbar.php'; ?>

  <section class="page-hero">
    <div class="page-hero-content">
      <p class="section-label"><?php echo htmlspecialchars($servicesContent['title'] ?? 'What We Offer'); ?></p>
      <h1><?php echo nl2br(htmlspecialchars($servicesContent['hero'] ?? 'Our Services')); ?></h1>
      <p><?php echo htmlspecialchars($servicesContent['content'] ?? 'Every cut is crafted with precision and care.'); ?></p>
    </div>
  </section>

  <section class="services-section">
    <div class="services-filter">
      <button class="filter-btn active" data-filter="all">All</button>
      <button class="filter-btn" data-filter="haircut">Cuts</button>
      <button class="filter-btn" data-filter="beard">Beard</button>
      <button class="filter-btn" data-filter="treatment">Treatment</button>
      <button class="filter-btn" data-filter="colour">Colour</button>
      <button class="filter-btn" data-filter="combo">Packages</button>
    </div>

    <div class="services-grid" id="servicesGrid">

      <?php foreach ($services as $service): ?>
      <?php
        $cat = $service['category'];
        // Determine icon based on category
        $icon = '<i class="fas fa-cut"></i>';
        if ($cat == 'beard') $icon = '🧔';
        if ($cat == 'treatment') $icon = '<i class="fas fa-spa"></i>';
        if ($cat == 'combo') $icon = '<i class="fas fa-gem"></i>';
        if ($cat == 'colour') $icon = '<i class="fas fa-tint"></i>';
        
        $isFeatured = ($cat == 'combo') ? 'featured' : '';
      ?>
      <div class="service-card <?php echo $isFeatured; ?>" data-cat="<?php echo htmlspecialchars($cat); ?>">
        <?php if ($isFeatured): ?>
        <div class="badge">Best Value</div>
        <?php endif; ?>
        <div class="service-icon"><?php echo $icon; ?></div>
        <div class="service-info">
          <h3><?php echo htmlspecialchars($service['name']); ?></h3>
          <p><?php echo htmlspecialchars($service['description']); ?></p>
          <div class="service-meta">
            <span class="duration"><i class="fas fa-clock"></i> <?php echo htmlspecialchars($service['duration']); ?> min</span>
            <span class="price">RM <?php echo htmlspecialchars(number_format($service['price'], 0)); ?></span>
          </div>
        </div>
        <a href="booking.php?service=<?php echo urlencode($service['id']); ?>" class="btn-small">Book</a>
      </div>
      <?php endforeach; ?>

    </div>
  </section>

  <?php include 'includes/footer.php'; ?>
  <script src="js/services.js"></script>
</body>
</html>
