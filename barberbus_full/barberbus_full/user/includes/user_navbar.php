<?php $currentPage = $currentPage ?? 'dashboard'; ?>
<nav class="navbar user-navbar" id="navbar">
  <div class="nav-logo">
    <span class="logo-icon">✂</span>
    <a href="../index.php" class="logo-text">BARBER<span class="accent">BUS</span></a>
  </div>
  <ul class="nav-links" id="navLinks">
    <li><a href="../index.php"><i class="fas fa-home"></i> Home</a></li>
    <li><a href="../services.php"><i class="fas fa-list"></i> Services</a></li>
    <li><a href="barbers.php" class="<?php echo $currentPage == 'barbers' ? 'active' : ''; ?>"><i class="fas fa-user-tie"></i> Our Barbers</a></li>
    <li><a href="fashion.php" class="<?php echo $currentPage == 'fashion' ? 'active' : ''; ?>"><i class="fas fa-palette"></i> Fashion Cut</a></li>
    <li><a href="tryon.php" class="<?php echo $currentPage == 'tryon' ? 'active' : ''; ?>" style="color:var(--gold)"><i class="fas fa-magic"></i> Try On</a></li>
    <li><a href="booking.php" class="<?php echo $currentPage == 'booking' ? 'active' : ''; ?>"><i class="fas fa-calendar-plus"></i> Booking</a></li>
    <li><a href="my_bookings.php" class="<?php echo $currentPage == 'my_bookings' ? 'active' : ''; ?>"><i class="fas fa-list-check"></i> Member Portal</a></li>
  </ul>
  <div class="nav-right">
    <div class="user-menu" id="userMenu">
      <div class="user-avatar" id="userAvatar">
        <span id="userInitials">U</span>
      </div>
      <span class="user-name-label" id="userNameLabel">User</span>
      <i class="fas fa-chevron-down user-chevron"></i>
      <div class="user-dropdown" id="userDropdown">
        <div class="dropdown-header">
          <strong id="dropdownName">User</strong>
          <small id="dropdownEmail">user@email.com</small>
        </div>
        <div class="dropdown-divider"></div>
        <a href="my_bookings.php"><i class="fas fa-calendar-check"></i> My Bookings</a>
        <div class="dropdown-divider"></div>
        <a href="#" onclick="userLogout(); return false;" class="logout-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
      </div>
    </div>
    <div class="hamburger" id="hamburger">
      <span></span><span></span><span></span>
    </div>
  </div>
</nav>
