<?php
require_once 'api/config.php';
$db = getDB();
$cols = $db->query("SELECT column_name, data_type, is_nullable, column_default
    FROM information_schema.columns
    WHERE table_name='barbers'
    ORDER BY ordinal_position")->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($cols);
