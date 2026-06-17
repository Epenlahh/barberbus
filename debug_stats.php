<?php
require_once 'api/config.php';
$db = getDB();

try {
    $totalUsers    = $db->query("SELECT COUNT(*) FROM users WHERE role='user'")->fetchColumn();
    echo "Users: $totalUsers\n";
    $totalBookings = $db->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
    echo "Bookings: $totalBookings\n";
    $totalRevenue  = $db->query("SELECT COALESCE(SUM(total_price),0) FROM bookings WHERE status IN ('confirmed','completed')")->fetchColumn();
    echo "Revenue: $totalRevenue\n";
    
    // Test the complex query
    $recentBookings = $db->query("
        SELECT b.id, b.booking_date, b.booking_time, b.status, b.total_price,
               u.name AS user_name, s.name AS service_name, br.name AS barber_name
        FROM bookings b
        JOIN users    u  ON b.user_id    = u.id
        JOIN services s  ON b.service_id = s.id
        LEFT JOIN barbers br ON b.barber_id = br.id
        ORDER BY b.created_at DESC
        LIMIT 10
    ")->fetchAll();
    echo "Recent Bookings Count: " . count($recentBookings) . "\n";
    
    echo "SUCCESS";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
