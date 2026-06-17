  <nav class="navbar" id="navbar">
    <div class="nav-logo">
      <span class="logo-icon">✂</span>
      <span class="logo-text"><?php echo htmlspecialchars($storeSettings['name'] ?? 'BarberBus'); ?></span>
    </div>
    <ul class="nav-links" id="navLinks">
      <?php $currentPage = $currentPage ?? 'home'; ?>
      <li><a href="index.php" class="<?php echo $currentPage == 'home' ? 'active' : ''; ?>">Home</a></li>
      <li><a href="services.php" class="<?php echo $currentPage == 'services' ? 'active' : ''; ?>">Services</a></li>
      <li><a href="barbers.php" class="<?php echo $currentPage == 'barbers' ? 'active' : ''; ?>">Our Barbers</a></li>
      <li><a href="fashion.php" class="<?php echo $currentPage == 'fashion' ? 'active' : ''; ?>">Fashion Cuts</a></li>
      <li><a href="booking.php" class="<?php echo $currentPage == 'booking' ? 'active' : ''; ?>">Book Now</a></li>
      <li><a href="user/dashboard.php" class="<?php echo $currentPage == 'user_dashboard' ? 'active' : ''; ?>"><i class="fas fa-user-circle" style="margin-right:0.3rem"></i>Member Portal</a></li>
      <li><a href="tryon.php" class="<?php echo $currentPage == 'tryon' ? 'active' : ''; ?>" style="color:var(--gold)"><i class="fas fa-magic" style="margin-right:0.3rem"></i>Try-On</a></li>
    </ul>
    <div class="nav-right">
      <a id="mainLoginBtn" href="login.php" class="btn-login">Login</a>
      <div class="hamburger" id="hamburger">
        <span></span><span></span><span></span>
      </div>
    </div>
  </nav>
