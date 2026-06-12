<?php
$files = [
    'barbers.php' => ['title' => 'BarberBus – Our Barbers', 'page' => 'barbers'],
    'services.php' => ['title' => 'BarberBus – Services', 'page' => 'services'],
    'booking.php' => ['title' => 'BarberBus – Book Now', 'page' => 'booking'],
    'dashboard.php' => ['title' => 'BarberBus – Dashboard', 'page' => 'dashboard'],
    'fashion.php' => ['title' => 'BarberBus – Fashion Cuts', 'page' => 'fashion'],
    'tryon.php' => ['title' => 'BarberBus – Try-On', 'page' => 'tryon'],
    'login.php' => ['title' => 'BarberBus – Login', 'page' => 'login']
];

foreach ($files as $filename => $meta) {
    if (!file_exists($filename)) {
        echo "File $filename not found.\n";
        continue;
    }
    
    $content = file_get_contents($filename);
    
    // Add PHP vars at top
    $phpHeader = "<?php\n  \$pageTitle = \"{$meta['title']}\";\n  \$currentPage = \"{$meta['page']}\";\n?>\n<!DOCTYPE html>";
    $content = preg_replace('/<!DOCTYPE html>/i', $phpHeader, $content, 1);
    
    // Replace Head
    $content = preg_replace('/<head>.*?<\/head>/is', "<?php include 'includes/head.php'; ?>", $content, 1);
    
    // Replace Nav
    $content = preg_replace('/<nav class="navbar" id="navbar">.*?<\/nav>/is', "<?php include 'includes/navbar.php'; ?>", $content, 1);
    
    // Replace Footer + Scripts
    // Some pages have extra scripts like js/services.js or js/booking.js
    // We should keep the extra scripts. The footer.php only includes js/main.js
    // Let's replace only the <footer> element first.
    $content = preg_replace('/<footer class="footer">.*?<\/footer>/is', "<?php include 'includes/footer.php'; ?>", $content, 1);
    
    // Remove the `<script src="js/main.js"></script>` since it's already in footer.php
    $content = preg_replace('/<script src="js\/main\.js"><\/script>/i', '', $content);
    
    file_put_contents($filename, $content);
    echo "Updated $filename\n";
}
?>
