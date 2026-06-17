<?php
// =============================================
// BARBERBUS – Services API
// File: api/services.php
// =============================================
require_once 'config.php';

$db = getDB();
$services = $db->query("SELECT * FROM services ORDER BY category, price ASC")->fetch_all(MYSQLI_ASSOC);
echo json_encode(['success' => true, 'services' => $services]);
$db->close();
?>