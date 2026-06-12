<?php
require_once 'api/config.php';
$db = getDB();
$cols = $db->query('DESCRIBE bookings')->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($cols);
