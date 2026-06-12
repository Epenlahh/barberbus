<?php
require_once 'api/config.php';
$db = getDB();
$tables = $db->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
echo json_encode($tables);
