<?php
$pageTitle = "BarberBus – Fashion Inspiration";
$currentPage = "fashion";
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'includes/user_head.php'; ?>
<body class="user-dashboard-body">

  <?php include 'includes/user_navbar.php'; ?>

  <main class="dashboard-container">
    <section class="welcome-section">
      <p class="section-label">Style Gallery</p>
      <h1>Fashion Inspiration</h1>
      <p>Browse our trending styles and find your next look.</p>
    </section>

    <section class="fashion-section">
      <div class="fashion-filter" style="margin-bottom: 2rem;">
        <button class="filter-btn active" data-filter="all">All Styles</button>
        <button class="filter-btn" data-filter="fade">Fades</button>
        <button class="filter-btn" data-filter="classic">Classic</button>
        <button class="filter-btn" data-filter="modern">Modern</button>
        <button class="filter-btn" data-filter="texture">Textured</button>
      </div>

      <div class="fashion-grid" id="fashionGrid">
        <!-- Reusing styles from root fashion.php -->
        <div class="fashion-card" data-cat="fade">
          <div class="fashion-img" style="background: linear-gradient(135deg, #1a1a1a 0%, #c9a84c 100%);">
            <div class="fashion-overlay"><div class="fashion-icon">✂</div></div>
          </div>
          <div class="fashion-info">
            <h4>High Skin Fade</h4>
            <p>Zero to skin with high contrast top styling.</p>
            <div class="fashion-tags"><span>Fade</span><span>Sharp</span></div>
            <a href="booking.php" class="btn-small">Get This Cut</a>
          </div>
        </div>

        <div class="fashion-card" data-cat="modern">
          <div class="fashion-img" style="background: linear-gradient(135deg, #1a1a2e 0%, #c9a84c 60%, #1a1a2e 100%);">
            <div class="fashion-overlay"><div class="fashion-icon">★</div></div>
          </div>
          <div class="fashion-info">
            <h4>Textured Quiff</h4>
            <p>Voluminous, structured quiff with a fade base.</p>
            <div class="fashion-tags"><span>Trending</span><span>Volume</span></div>
            <a href="booking.php" class="btn-small">Get This Cut</a>
          </div>
        </div>

        <div class="fashion-card" data-cat="classic">
          <div class="fashion-img" style="background: linear-gradient(135deg, #2d1b00 0%, #c9a84c 100%);">
            <div class="fashion-overlay"><div class="fashion-icon">✂</div></div>
          </div>
          <div class="fashion-info">
            <h4>Side Part Classic</h4>
            <p>Timeless side-part with a clean finish.</p>
            <div class="fashion-tags"><span>Classic</span><span>Formal</span></div>
            <a href="booking.php" class="btn-small">Get This Cut</a>
          </div>
        </div>

        <div class="fashion-card" data-cat="texture">
          <div class="fashion-img" style="background: linear-gradient(135deg, #1a1a1a 0%, #3a7a3a 100%);">
            <div class="fashion-overlay"><div class="fashion-icon">✂</div></div>
          </div>
          <div class="fashion-info">
            <h4>Textured Crop</h4>
            <p>Short, textured top with a skin or low fade.</p>
            <div class="fashion-tags"><span>Crop</span><span>Texture</span></div>
            <a href="booking.php" class="btn-small">Get This Cut</a>
          </div>
        </div>
        
        <!-- More cards can be added here -->
      </div>
    </section>
  </main>

  <?php include 'includes/user_footer.php'; ?>
  <script src="../js/user-dashboard.js"></script>
  <script src="../js/fashion.js"></script>
</body>
</html>
