<?php
require_once 'api/config.php';
$db = getDB();
$tables = $db->query("SELECT table_name FROM information_schema.tables WHERE table_schema='public' AND table_type='BASE TABLE' ORDER BY table_name")->fetchAll(PDO::FETCH_COLUMN);
echo json_encode($tables);
