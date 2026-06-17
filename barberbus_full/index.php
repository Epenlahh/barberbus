<?php
  require_once __DIR__ . '/includes/init.php';
  $dashboardContent = getPageContent('dashboard');
  $pageTitle = htmlspecialchars($storeSettings['name'] ?? 'BarberBus') . " – Sharp Cuts, Classic Style";
  $currentPage = "home";
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'includes/head.php'; ?>
<body>

  <!-- NAV -->
  <?php include 'includes/navbar.php'; ?>

  <!-- HERO -->
  <section class="hero">
    <div class="hero-bg-text"><?php echo strtoupper(htmlspecialchars($storeSettings['name'] ?? 'BARBERBUS')); ?></div>
    <div class="hero-content">
      <p class="hero-tagline">EST. 2024 · <?php echo strtoupper(htmlspecialchars(explode(',', $storeSettings['location'] ?? 'KUALA LUMPUR')[count(explode(',', $storeSettings['location'] ?? 'KUALA LUMPUR'))-1] ?? 'KUALA LUMPUR')); ?></p>
      <h1 class="hero-title"><?php echo nl2br(htmlspecialchars($dashboardContent['hero'] ?? "Sharp Cuts.\nClassic Style.")); ?></h1>
      <p class="hero-sub"><?php echo htmlspecialchars($dashboardContent['content'] ?? 'Premium grooming experience for the modern gentleman. Walk in looking good, walk out looking legendary.'); ?></p>
      <div class="hero-btns">
        <a href="booking.php" class="btn-primary">Book Appointment</a>
        <a href="services.php" class="btn-outline">View Services</a>
      </div>
    </div>
    <div class="hero-image">
      <div class="hero-card">
        <div class="hero-card-inner">
          <i class="fas fa-scissors hero-scissors"></i>
          <p class="hero-card-text">Walk-ins<br/>Welcome</p>
          <span class="hero-card-badge">Open Today</span>
        </div>
      </div>
    </div>
    <div class="hero-scroll">
      <span>Scroll Down</span>
      <i class="fas fa-chevron-down"></i>
    </div>
  </section>

  <!-- STATS -->
  <section class="stats">
    <div class="stat-item">
      <h2 class="stat-num">5+</h2>
      <p>Expert Barbers</p>
    </div>
    <div class="stat-item">
      <h2 class="stat-num">3K+</h2>
      <p>Happy Clients</p>
    </div>
    <div class="stat-item">
      <h2 class="stat-num">15+</h2>
      <p>Cut Styles</p>
    </div>
    <div class="stat-item">
      <h2 class="stat-num">4.9★</h2>
      <p>Rating</p>
    </div>
  </section>

  <!-- PROMOTIONS CAROUSEL -->
  <section id="promotionsCarousel" style="display:none;margin:2rem 0"></section>

  <!-- SERVICES PREVIEW -->
  <section class="section-preview">
    <div class="section-header">
      <p class="section-label">What We Offer</p>
      <h2>Our Services</h2>
    </div>
    <div class="preview-grid">
      <div class="preview-card">
        <div class="preview-icon"><i class="fas fa-cut"></i></div>
        <h3>Classic Cut</h3>
        <p>Timeless styles for the gentleman who knows what he wants.</p>
        <span class="price">From RM25</span>
      </div>
      <div class="preview-card highlight">
        <div class="preview-icon"><i class="fas fa-magic"></i></div>
        <h3>Fade & Taper</h3>
        <p>Precision fading from skin to styled top — our specialty.</p>
        <span class="price">From RM35</span>
      </div>
      <div class="preview-card">
        <div class="preview-icon"><i class="fas fa-beard"></i></div>
        <h3>Beard Grooming</h3>
        <p>Shape, trim and style your beard to perfection.</p>
        <span class="price">From RM20</span>
      </div>
    </div>
    <div class="center-btn">
      <a href="services.php" class="btn-primary">All Services</a>
    </div>
  </section>

  <!-- TESTIMONIALS -->
  <section class="testimonials">
    <div class="section-header light">
      <p class="section-label">What Clients Say</p>
      <h2>Reviews</h2>
    </div>
    <div class="testimonial-grid">
      <div class="testimonial-card">
        <div class="stars">★★★★★</div>
        <p>"Best barber in PJ. Aariz knows exactly what I want every time. The fade is always crisp!"</p>
        <span class="client-name">– Hafiz R.</span>
      </div>
      <div class="testimonial-card">
        <div class="stars">★★★★★</div>
        <p>"Great atmosphere, great cuts. I always leave feeling like a new man. Highly recommended!"</p>
        <span class="client-name">– Daniel T.</span>
      </div>
      <div class="testimonial-card">
        <div class="stars">★★★★★</div>
        <p>"Booked online, walked in, got the best haircut of my life. The booking system is super easy."</p>
        <span class="client-name">– Amirul K.</span>
      </div>
    </div>
  </section>

  <!-- CTA -->
  <section class="cta-section">
    <div class="cta-content">
      <h2>Ready for a Fresh Look?</h2>
      <p>Book your appointment online in under 2 minutes.</p>
      <a href="booking.php" class="btn-primary large">Book Now</a>
    </div>
  </section>

  <!-- FOOTER -->
  <?php include 'includes/footer.php'; ?>

  <script src="js/promotions.js"></script>
</body>
</html>
