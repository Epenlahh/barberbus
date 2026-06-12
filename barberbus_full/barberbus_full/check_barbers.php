<?php
require_once 'api/config.php';
$db = getDB();
$cols = $db->query('DESCRIBE barbers')->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($cols);
