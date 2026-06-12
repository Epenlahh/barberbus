<?php
// =============================================
// BARBERBUS – ADMIN STATS API
// GET /api/stats.php → dashboard statistics
// =============================================

require_once __DIR__ . '/config.php';
setCORSHeaders();
requireAdmin();

try {
    $db = getDB();

    // Total counts
    $totalUsers    = $db->query("SELECT COUNT(*) FROM users WHERE role='user'")->fetchColumn();
    $totalBookings = $db->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
    $totalRevenue  = $db->query("SELECT COALESCE(SUM(total_price),0) FROM bookings WHERE status IN ('confirmed','completed')")->fetchColumn();
    $totalBarbers  = $db->query("SELECT COUNT(*) FROM barbers WHERE is_active=1")->fetchColumn();

    // Today's bookings
    $today            = date('Y-m-d');
    $todayCount       = $db->query("SELECT COUNT(*) FROM bookings WHERE booking_date='$today'")->fetchColumn();

    // Pending bookings
    $pending = $db->query("SELECT COUNT(*) FROM bookings WHERE status='pending'")->fetchColumn();

    // Monthly revenue (last 6 months)
    $monthly = $db->query("
        SELECT DATE_FORMAT(booking_date, '%b') AS month,
               YEAR(booking_date) AS yr,
               MONTH(booking_date) AS mo,
               COALESCE(SUM(total_price),0) AS revenue,
               COUNT(*) AS count
        FROM bookings
        WHERE status IN ('confirmed','completed')
          AND booking_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
        GROUP BY yr, mo, month
        ORDER BY yr, mo
    ")->fetchAll(PDO::FETCH_ASSOC);

    // Popular services
    $popularServices = $db->query("
        SELECT s.name, COUNT(b.id) AS count, SUM(b.total_price) AS revenue
        FROM bookings b
        JOIN services s ON b.service_id = s.id
        WHERE b.status IN ('confirmed','completed')
        GROUP BY s.id, s.name
        ORDER BY count DESC
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);

    // Recent bookings
    $recentBookings = $db->query("
        SELECT b.id, b.booking_date, b.booking_time, b.status, b.total_price,
               u.name AS user_name, s.name AS service_name, br.name AS barber_name
        FROM bookings b
        JOIN users    u  ON b.user_id    = u.id
        JOIN services s  ON b.service_id = s.id
        LEFT JOIN barbers br ON b.barber_id = br.id
        ORDER BY b.created_at DESC
        LIMIT 10
    ")->fetchAll(PDO::FETCH_ASSOC);

    jsonResponse(true, 'OK', [
        'stats' => [
            'total_users'    => (int)$totalUsers,
            'total_bookings' => (int)$totalBookings,
            'total_revenue'  => (float)$totalRevenue,
            'total_barbers'  => (int)$totalBarbers,
            'today_bookings' => (int)$todayCount,
            'pending'        => (int)$pending,
        ],
        'monthly_revenue'  => $monthly,
        'popular_services' => $popularServices,
        'recent_bookings'  => $recentBookings,
    ]);

} catch (Exception $e) {
    jsonResponse(false, 'Database Error: ' . $e->getMessage());
}
?>
