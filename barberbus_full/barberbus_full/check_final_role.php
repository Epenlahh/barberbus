<?php
require_once 'api/config.php';
$db = getDB();
$user = $db->query("SELECT email, role FROM users WHERE email='hakimirfan3443@gmail.com'")->fetch();
echo json_encode($user);
