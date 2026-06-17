<?php
// =============================================
// BARBERBUS – Dashboard Stats API
// File: api/stats.php
// =============================================
require_once 'config.php';

$db = getDB();
$today = date('Y-m-d');

// Today stats
$today_stats = $db->query("
    SELECT
        COUNT(*) as total_bookings,
        SUM(CASE WHEN status='done' THEN amount ELSE 0 END) as revenue,
        SUM(CASE WHEN status='done' THEN 1 ELSE 0 END) as clients_served,
        SUM(CASE WHEN status='pending' THEN 1 ELSE 0 END) as pending
    FROM bookings WHERE booking_date = '$today'
")->fetch_assoc();

// Weekly revenue (last 7 days)
$weekly = $db->query("
    SELECT DATE_FORMAT(booking_date, '%a') as day,
           booking_date,
           SUM(CASE WHEN status='done' THEN amount ELSE 0 END) as revenue,
           COUNT(*) as bookings
    FROM bookings
    WHERE booking_date >= DATE_SUB('$today', INTERVAL 6 DAY)
    GROUP BY booking_date
    ORDER BY booking_date ASC
")->fetch_all(MYSQLI_ASSOC);

// Popular services this month
$services = $db->query("
    SELECT s.name, COUNT(b.id) as count, SUM(b.amount) as revenue
    FROM bookings b JOIN services s ON b.service_id = s.id
    WHERE MONTH(b.booking_date) = MONTH(CURDATE())
    GROUP BY b.service_id ORDER BY count DESC LIMIT 5
")->fetch_all(MYSQLI_ASSOC);

// Barber performance today
$barbers = $db->query("
    SELECT br.name, br.id,
           COUNT(b.id) as bookings,
           SUM(CASE WHEN b.status='done' THEN b.amount ELSE 0 END) as revenue
    FROM barbers br
    LEFT JOIN bookings b ON br.id = b.barber_id AND b.booking_date = '$today'
    GROUP BY br.id ORDER BY revenue DESC
")->fetch_all(MYSQLI_ASSOC);

echo json_encode([
    'success'      => true,
    'today'        => $today_stats,
    'weekly'       => $weekly,
    'services'     => $services,
    'barbers'      => $barbers
]);

$db->close();
?>