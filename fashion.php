<?php
  require_once __DIR__ . '/includes/init.php';
  $fashionContent = getPageContent('fashion');
  $pageTitle = htmlspecialchars($storeSettings['name'] ?? 'BarberBus') . " – " . htmlspecialchars($fashionContent['title'] ?? 'Fashion Cuts');
  $currentPage = "fashion";
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'includes/head.php'; ?>
<body>

  <?php include 'includes/navbar.php'; ?>

  <section class="page-hero">
    <div class="page-hero-content">
      <p class="section-label"><?php echo htmlspecialchars($fashionContent['title'] ?? 'Inspiration Gallery'); ?></p>
      <h1><?php echo nl2br(htmlspecialchars($fashionContent['hero'] ?? 'Fashion Cuts')); ?></h1>
      <p><?php echo htmlspecialchars($fashionContent['content'] ?? 'Browse our trending styles and find your next look.'); ?></p>
    </div>
  </section>

  <section class="fashion-section">
    <div class="fashion-filter">
      <button class="filter-btn active" data-filter="all">All Styles</button>
      <button class="filter-btn" data-filter="fade">Fades</button>
      <button class="filter-btn" data-filter="classic">Classic</button>
      <button class="filter-btn" data-filter="modern">Modern</button>
      <button class="filter-btn" data-filter="texture">Textured</button>
      <button class="filter-btn" data-filter="beard">Beard</button>
    </div>

    <div class="fashion-grid" id="fashionGrid">

      <div class="fashion-card" data-cat="fade">
        <div class="fashion-img" style="background: linear-gradient(135deg, #1a1a1a 0%, #c9a84c 100%);">
          <div class="fashion-overlay">
            <div class="fashion-icon">✂</div>
          </div>
        </div>
        <div class="fashion-info">
          <h4>High Skin Fade</h4>
          <p>Zero to skin with high contrast top styling.</p>
          <div class="fashion-tags">
            <span>Fade</span><span>Sharp</span><span>Modern</span>
          </div>
          <a href="booking.php" class="btn-small">Get This Cut</a>
        </div>
      </div>

      <div class="fashion-card" data-cat="fade">
        <div class="fashion-img" style="background: linear-gradient(135deg, #0d0d0d 0%, #8a5c0a 100%);">
          <div class="fashion-overlay"><div class="fashion-icon">✂</div></div>
        </div>
        <div class="fashion-info">
          <h4>Mid Taper Fade</h4>
          <p>Smooth gradual fade from mid-ear. Versatile and clean.</p>
          <div class="fashion-tags"><span>Taper</span><span>Clean</span></div>
          <a href="booking.php" class="btn-small">Get This Cut</a>
        </div>
      </div>

      <div class="fashion-card large" data-cat="modern">
        <div class="fashion-img" style="background: linear-gradient(135deg, #1a1a2e 0%, #c9a84c 60%, #1a1a2e 100%);">
          <div class="fashion-overlay"><div class="fashion-icon">★</div></div>
        </div>
        <div class="fashion-info">
          <h4>Textured Quiff</h4>
          <p>Voluminous, structured quiff with a fade base. The ultimate style statement.</p>
          <div class="fashion-tags"><span>Trending</span><span>Volume</span><span>Fade</span></div>
          <a href="booking.php" class="btn-small">Get This Cut</a>
        </div>
      </div>

      <div class="fashion-card" data-cat="classic">
        <div class="fashion-img" style="background: linear-gradient(135deg, #2d1b00 0%, #c9a84c 100%);">
          <div class="fashion-overlay"><div class="fashion-icon">✂</div></div>
        </div>
        <div class="fashion-info">
          <h4>Side Part Classic</h4>
          <p>Timeless side-part with a clean scissor finish.</p>
          <div class="fashion-tags"><span>Classic</span><span>Formal</span></div>
          <a href="booking.php" class="btn-small">Get This Cut</a>
        </div>
      </div>

      <div class="fashion-card" data-cat="modern">
        <div class="fashion-img" style="background: linear-gradient(135deg, #000 0%, #5a5a5a 100%);">
          <div class="fashion-overlay"><div class="fashion-icon">✂</div></div>
        </div>
        <div class="fashion-info">
          <h4>Disconnected Undercut</h4>
          <p>Bold, edgy undercut with a sharp disconnected line.</p>
          <div class="fashion-tags"><span>Bold</span><span>Edgy</span></div>
          <a href="booking.php" class="btn-small">Get This Cut</a>
        </div>
      </div>

      <div class="fashion-card" data-cat="texture">
        <div class="fashion-img" style="background: linear-gradient(135deg, #1a1a1a 0%, #3a7a3a 100%);">
          <div class="fashion-overlay"><div class="fashion-icon">✂</div></div>
        </div>
        <div class="fashion-info">
          <h4>Textured Crop</h4>
          <p>Short, textured top with a skin or low fade base.</p>
          <div class="fashion-tags"><span>Crop</span><span>Texture</span><span>Low maintenance</span></div>
          <a href="booking.php" class="btn-small">Get This Cut</a>
        </div>
      </div>

      <div class="fashion-card" data-cat="beard">
        <div class="fashion-img" style="background: linear-gradient(135deg, #1a0000 0%, #8a2020 100%);">
          <div class="fashion-overlay"><div class="fashion-icon">🧔</div></div>
        </div>
        <div class="fashion-info">
          <h4>Full Beard + Fade</h4>
          <p>Manicured full beard paired with a clean fade for the complete look.</p>
          <div class="fashion-tags"><span>Beard</span><span>Fade</span><span>Full Look</span></div>
          <a href="booking.php" class="btn-small">Get This Cut</a>
        </div>
      </div>

      <div class="fashion-card" data-cat="classic">
        <div class="fashion-img" style="background: linear-gradient(135deg, #1a1000 0%, #b8860b 100%);">
          <div class="fashion-overlay"><div class="fashion-icon">✂</div></div>
        </div>
        <div class="fashion-info">
          <h4>Ivy League</h4>
          <p>A refined prep-school style. Short, neat, and always elegant.</p>
          <div class="fashion-tags"><span>Classic</span><span>Preppy</span></div>
          <a href="booking.php" class="btn-small">Get This Cut</a>
        </div>
      </div>

      <div class="fashion-card large" data-cat="fade">
        <div class="fashion-img" style="background: linear-gradient(135deg, #0a0a0a 0%, #c9a84c 50%, #8a0000 100%);">
          <div class="fashion-overlay"><div class="fashion-icon">★</div></div>
        </div>
        <div class="fashion-info">
          <h4>Burst Fade Mohawk</h4>
          <p>Dramatic burst fade around the ear with a raised mohawk strip. Bold statement piece.</p>
          <div class="fashion-tags"><span>Bold</span><span>Statement</span><span>Burst Fade</span></div>
          <a href="booking.php" class="btn-small">Get This Cut</a>
        </div>
      </div>

      <div class="fashion-card" data-cat="texture">
        <div class="fashion-img" style="background: linear-gradient(135deg, #000 0%, #2a2a5a 100%);">
          <div class="fashion-overlay"><div class="fashion-icon">✂</div></div>
        </div>
        <div class="fashion-info">
          <h4>French Crop</h4>
          <p>European-inspired crop with textured fringe and clean sides.</p>
          <div class="fashion-tags"><span>French</span><span>Crop</span><span>Fringe</span></div>
          <a href="booking.php" class="btn-small">Get This Cut</a>
        </div>
      </div>

      <div class="fashion-card" data-cat="beard">
        <div class="fashion-img" style="background: linear-gradient(135deg, #000 0%, #4a3000 100%);">
          <div class="fashion-overlay"><div class="fashion-icon">🧔</div></div>
        </div>
        <div class="fashion-info">
          <h4>Stubble & Line-Up</h4>
          <p>Precisely lined-up stubble for a sharp, clean professional look.</p>
          <div class="fashion-tags"><span>Stubble</span><span>Line-up</span><span>Clean</span></div>
          <a href="booking.php" class="btn-small">Get This Cut</a>
        </div>
      </div>

      <div class="fashion-card" data-cat="modern">
        <div class="fashion-img" style="background: linear-gradient(135deg, #0a0a0a 0%, #6a006a 100%);">
          <div class="fashion-overlay"><div class="fashion-icon">✂</div></div>
        </div>
        <div class="fashion-info">
          <h4>Pompadour Fade</h4>
          <p>Classic pompadour volume meets modern fade for an iconic look.</p>
          <div class="fashion-tags"><span>Pompadour</span><span>Retro</span><span>Modern</span></div>
          <a href="booking.php" class="btn-small">Get This Cut</a>
        </div>
      </div>

    </div>
  </section>

  <section class="cta-section">
    <div class="cta-content">
      <h2>Found Your Style?</h2>
      <p>Book now and let our barbers bring it to life.</p>
      <a href="booking.php" class="btn-primary large">Book Appointment</a>
    </div>
  </section>

  <?php include 'includes/footer.php'; ?>
  <script src="js/fashion.js"></script>
</body>
</html>
