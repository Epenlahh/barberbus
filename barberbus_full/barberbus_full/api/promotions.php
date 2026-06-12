<?php
// =============================================
// PROMOTIONS API
// =============================================
require 'config.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$auth   = verifyToken(getAuthToken());

// Require admin for write operations
if (($method === 'POST' || $method === 'PUT' || $method === 'DELETE') && (!$auth || $auth['role'] !== 'admin')) {
    jsonResponse(false, 'Admin access required', [], 403);
    exit;
}

try {
    if ($method === 'GET') {
        // Get all active promotions (public endpoint) or all for admin
        $query = 'SELECT * FROM promotions';
        $params = [];

        if (!$auth || $auth['role'] !== 'admin') {
            // Public users only see active promotions
            $query .= ' WHERE is_active = 1 AND (start_date IS NULL OR start_date <= CURDATE()) AND (end_date IS NULL OR end_date >= CURDATE())';
        }

        $query .= ' ORDER BY created_at DESC';

        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $promotions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        jsonResponse(true, 'Promotions retrieved', ['promotions' => $promotions]);

    } elseif ($method === 'POST') {
        // Create new promotion
        $data = json_decode(file_get_contents('php://input'), true);

        $title       = $data['title']       ?? '';
        $description = $data['description'] ?? '';
        $image       = $data['image']       ?? '';
        $link        = $data['link']        ?? '';
        $isActive    = $data['is_active']   ?? 1;
        $startDate   = $data['start_date']  ?? null;
        $endDate     = $data['end_date']    ?? null;

        if (!$title) {
            jsonResponse(false, 'Promotion title is required', [], 422);
            exit;
        }

        $stmt = $db->prepare('
            INSERT INTO promotions (title, description, image, link, is_active, start_date, end_date, created_by)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ');
        $stmt->execute([$title, $description, $image, $link, $isActive, $startDate ?: null, $endDate ?: null, $auth['sub']]);

        jsonResponse(true, 'Promotion created successfully', ['id' => $db->lastInsertId()]);

    } elseif ($method === 'PUT') {
        // Update promotion
        $id   = $_GET['id'] ?? null;
        $data = json_decode(file_get_contents('php://input'), true);

        if (!$id) {
            jsonResponse(false, 'Promotion ID is required', [], 422);
            exit;
        }

        $title       = $data['title']       ?? '';
        $description = $data['description'] ?? '';
        $image       = $data['image']       ?? '';
        $link        = $data['link']        ?? '';
        $isActive    = $data['is_active']   ?? 1;
        $startDate   = $data['start_date']  ?? null;
        $endDate     = $data['end_date']    ?? null;

        $stmt = $db->prepare('
            UPDATE promotions 
            SET title=?, description=?, image=?, link=?, is_active=?, start_date=?, end_date=?
            WHERE id=?
        ');
        $stmt->execute([$title, $description, $image, $link, $isActive, $startDate ?: null, $endDate ?: null, $id]);

        if ($stmt->rowCount() > 0) {
            jsonResponse(true, 'Promotion updated successfully');
        } else {
            jsonResponse(false, 'Promotion not found', [], 404);
        }

    } elseif ($method === 'DELETE') {
        // Delete promotion
        $id = $_GET['id'] ?? null;

        if (!$id) {
            jsonResponse(false, 'Promotion ID is required', [], 422);
            exit;
        }

        $stmt = $db->prepare('DELETE FROM promotions WHERE id=?');
        $stmt->execute([$id]);

        if ($stmt->rowCount() > 0) {
            jsonResponse(true, 'Promotion deleted successfully');
        } else {
            jsonResponse(false, 'Promotion not found', [], 404);
        }

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
