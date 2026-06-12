<?php
require_once 'api/config.php';
$db = getDB();
$cols = $db->query('DESCRIBE services')->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($cols);
