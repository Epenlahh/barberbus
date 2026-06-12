<?php
$pageTitle = "BarberBus – AR Try-On";
$currentPage = "tryon";
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'includes/user_head.php'; ?>
<body class="user-dashboard-body" style="overflow: hidden;">

  <?php include 'includes/user_navbar.php'; ?>

  <div class="tryon-iframe-container">
    <!-- Using iframe to embed the existing self-contained AR tryon page -->
    <iframe src="../tryon.php" title="AR Hair Try-On"></iframe>
  </div>

  <?php include 'includes/user_footer.php'; ?>
  <script src="../js/user-dashboard.js"></script>
</body>
</html>
