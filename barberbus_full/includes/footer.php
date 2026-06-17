  <footer class="footer">
    <div class="footer-grid">
      <div class="footer-brand">
        <span class="logo-text"><?php echo htmlspecialchars($storeSettings['name'] ?? 'BarberBus'); ?></span>
        <p><?php echo htmlspecialchars($storeSettings['description'] ?? 'Premium grooming for the modern gentleman. Located in the heart of Petaling Jaya.'); ?></p>
      </div>
      <div class="footer-links">
        <h4>Pages</h4>
        <ul>
          <li><a href="services.php">Services</a></li>
          <li><a href="barbers.php">Barbers</a></li>
          <li><a href="fashion.php">Fashion Cuts</a></li>
          <li><a href="booking.php">Booking</a></li>
        </ul>
      </div>
      <div class="footer-contact">
        <h4>Contact</h4>
        <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($storeSettings['location'] ?? 'No. 5, Jalan PJ, Petaling Jaya'); ?></p>
        <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($storeSettings['phone'] ?? '+60 12-345 6789'); ?></p>
        <p><i class="fas fa-clock"></i> <?php echo htmlspecialchars($storeSettings['business_hours'] ?? 'Mon–Sat: 10am – 9pm'); ?></p>
      </div>
    </div>
    <div class="footer-bottom">
      <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($storeSettings['name'] ?? 'BarberBus'); ?>. All rights reserved.</p>
    </div>
  </footer>
  <script src="js/main.js"></script>
