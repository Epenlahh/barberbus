<?php
// =============================================
// PAGE CONTENT API
// =============================================
require 'config.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$auth   = verifyToken(getAuthToken());

// Require admin for write operations
if (($method === 'POST' || $method === 'PUT') && (!$auth || $auth['role'] !== 'admin')) {
    jsonResponse(false, 'Admin access required', [], 403);
    exit;
}

try {
    if ($method === 'GET') {
        // Get page content - public endpoint
        $pageName = $_GET['page'] ?? 'dashboard';
        
        $stmt = $db->prepare('SELECT * FROM page_content WHERE page_name = ?');
        $stmt->execute([$pageName]);
        $content = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$content) {
            // Return default content if not found
            $content = [
                'page_name' => $pageName,
                'title'     => ucfirst($pageName),
                'hero'      => 'Welcome to ' . ucfirst($pageName),
                'content'   => 'Page content coming soon...',
                'footer'    => ''
            ];
        }

        jsonResponse(true, 'Page content retrieved', ['content' => $content]);

    } elseif ($method === 'POST') {
        // Save page content
        $data = json_decode(file_get_contents('php://input'), true);

        $pageName = $data['page']    ?? 'dashboard';
        $title    = $data['title']   ?? '';
        $hero     = $data['hero']    ?? '';
        $content  = $data['content'] ?? '';
        $footer   = $data['footer']  ?? '';

        // Check if page content exists
        $check = $db->prepare('SELECT id FROM page_content WHERE page_name = ?');
        $check->execute([$pageName]);
        $exists = $check->fetch();

        if ($exists) {
            // Update
            $stmt = $db->prepare('
                UPDATE page_content 
                SET title=?, hero=?, content=?, footer=?, updated_by=?
                WHERE page_name=?
            ');
            $stmt->execute([$title, $hero, $content, $footer, $auth['sub'], $pageName]);
        } else {
            // Insert
            $stmt = $db->prepare('
                INSERT INTO page_content (page_name, title, hero, content, footer, updated_by)
                VALUES (?, ?, ?, ?, ?, ?)
            ');
            $stmt->execute([$pageName, $title, $hero, $content, $footer, $auth['sub']]);
        }

        jsonResponse(true, 'Page content saved successfully');

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
