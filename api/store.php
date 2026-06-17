<?php
// =============================================
// STORE SETTINGS API
// =============================================
require 'config.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$auth   = verifyToken(getAuthToken());

// Require admin for all operations
if (!$auth || $auth['role'] !== 'admin') {
    jsonResponse(false, 'Admin access required', [], 403);
    exit;
}

try {
    if ($method === 'GET') {
        // Get store settings
        $stmt = $db->prepare('SELECT * FROM store_settings LIMIT 1');
        $stmt->execute();
        $store = $stmt->fetch(PDO::FETCH_ASSOC);
        jsonResponse(true, 'Store settings retrieved', ['store' => $store ?: []]);

    } elseif ($method === 'POST') {
        // Save store settings
        $data = json_decode(file_get_contents('php://input'), true);

        $name        = $data['name']           ?? 'BarberBus';
        $desc        = $data['description']    ?? '';
        $isOpen      = $data['is_open']        ?? 1;
        $image       = $data['profile_image']  ?? '';
        $hours       = $data['business_hours'] ?? '';
        $location    = $data['location']       ?? '';
        $phone       = $data['phone']          ?? '';

        // Check if settings exist
        $check = $db->prepare('SELECT id FROM store_settings LIMIT 1');
        $check->execute();
        $exists = $check->fetch();

        if ($exists) {
            // Update
            $stmt = $db->prepare('
                UPDATE store_settings 
                SET name=?, description=?, is_open=?, profile_image=?, 
                    business_hours=?, location=?, phone=?, updated_by=?
                WHERE id=?
            ');
            $stmt->execute([$name, $desc, $isOpen, $image, $hours, $location, $phone, $auth['sub'], $exists['id']]);
        } else {
            // Insert
            $stmt = $db->prepare('
                INSERT INTO store_settings (name, description, is_open, profile_image, business_hours, location, phone, updated_by)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ');
            $stmt->execute([$name, $desc, $isOpen, $image, $hours, $location, $phone, $auth['sub']]);
        }

        jsonResponse(true, 'Store settings saved successfully');

    } else {
        jsonResponse(false, 'Method not allowed', [], 405);
    }

} catch (PDOException $e) {
    jsonResponse(false, 'Database error: ' . $e->getMessage(), [], 500);
} catch (Exception $e) {
    jsonResponse(false, 'Error: ' . $e->getMessage(), [], 500);
}

function getAuthToken() {
    $headers = apache_request_headers();
    if (isset($headers['Authorization'])) {
        $matches = [];
        if (preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
            return $matches[1];
        }
    }
    return null;
}
?>
