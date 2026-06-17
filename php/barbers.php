<?php
// =============================================
// BARBERBUS – Barbers & Services API
// File: api/barbers.php
// =============================================
require_once 'config.php';

$method = $_SERVER['REQUEST_METHOD'];
$db = getDB();

if ($method === 'GET') {
    $barbers = $db->query("
        SELECT b.*, 
               COUNT(bk.id) as total_bookings_today,
               COALESCE(SUM(CASE WHEN bk.status='done' THEN bk.amount ELSE 0 END), 0) as revenue_today
        FROM barbers b
        LEFT JOIN bookings bk ON b.id = bk.barber_id AND bk.booking_date = CURDATE()
        GROUP BY b.id
        ORDER BY b.id ASC
    ")->fetch_all(MYSQLI_ASSOC);

    echo json_encode(['success' => true, 'barbers' => $barbers]);
}

$db->close();
?>