<?php
// =============================================
// BARBERBUS – USERS API (Admin only)
// GET    /api/users.php          → all users with booking count
// PUT    /api/users.php?id=X     → update user role
// POST   /api/users.php          → create new user account
// DELETE /api/users.php?id=X     → delete user
// =============================================

require_once __DIR__ . '/config.php';
setCORSHeaders();
$user = requireAuth(); // Allow authenticated users, check role per method

$db     = getDB();
$method = $_SERVER['REQUEST_METHOD'];
$id     = intval($_GET['id'] ?? 0);

// Role checks per method
if ($method === 'GET') {
    if (!in_array($user['role'], ['admin', 'officer'])) {
        jsonResponse(false, 'Access denied.', [], 403);
    }
} else {
    if ($user['role'] !== 'admin') {
        jsonResponse(false, 'Admin access required for this operation.', [], 403);
    }
}

switch ($method) {

    case 'GET':
        $stmt = $db->query("
            SELECT u.id, u.name, u.email, u.phone, u.role, u.created_at,
                   COUNT(b.id) AS booking_count
            FROM users u
            LEFT JOIN bookings b ON u.id = b.user_id
            GROUP BY u.id
            ORDER BY u.created_at DESC
        ");
        jsonResponse(true, 'OK', ['users' => $stmt->fetchAll()]);
        break;

    case 'POST':
        // Create a new user account (e.g. officer)
        $body     = getBody();
        $name     = trim($body['name']     ?? '');
        $email    = trim($body['email']    ?? '');
        $password = trim($body['password'] ?? '');
        $role     = trim($body['role']     ?? 'user');
        $phone    = trim($body['phone']    ?? '');

        $allowed_roles = ['user', 'admin', 'officer'];
        if (!$name || !$email || !$password) {
            jsonResponse(false, 'Name, email and password are required.', [], 422);
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            jsonResponse(false, 'Invalid email address.', [], 422);
        }
        if (!in_array($role, $allowed_roles)) {
            jsonResponse(false, 'Invalid role. Must be user, admin, or officer.', [], 422);
        }

        // Check if email already exists
        $check = $db->prepare('SELECT id FROM users WHERE email = ?');
        $check->execute([$email]);
        if ($check->fetch()) {
            jsonResponse(false, 'A user with this email already exists.', [], 409);
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $db->prepare('INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$name, $email, $phone, $hash, $role]);
        jsonResponse(true, 'User account created.', ['id' => $db->lastInsertId()]);
        break;

    case 'PUT':
        // Update user role or details
        if (!$id) jsonResponse(false, 'User ID is required.', [], 422);
        $body = getBody();
        $allowed_roles = ['user', 'admin', 'officer'];

        if (isset($body['role'])) {
            $role = trim($body['role']);
            if (!in_array($role, $allowed_roles)) {
                jsonResponse(false, 'Invalid role. Must be user, admin, or officer.', [], 422);
            }
            $stmt = $db->prepare('UPDATE users SET role = ? WHERE id = ?');
            $stmt->execute([$role, $id]);
            if ($stmt->rowCount() === 0) {
                jsonResponse(false, 'User not found.', [], 404);
            }
            jsonResponse(true, 'User role updated to ' . $role . '.');
        }

        // Update password if provided
        if (isset($body['password']) && $body['password']) {
            $hash = password_hash($body['password'], PASSWORD_BCRYPT);
            $stmt = $db->prepare('UPDATE users SET password = ? WHERE id = ?');
            $stmt->execute([$hash, $id]);
        }

        jsonResponse(true, 'User updated.');
        break;

    case 'DELETE':
        if (!$id) jsonResponse(false, 'User ID is required.', [], 422);
        // Prevent admin from deleting themselves
        $auth = verifyToken(str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION'] ?? ''));
        if ($auth && intval($auth['sub']) === $id) {
            jsonResponse(false, 'You cannot delete your own account.', [], 403);
        }
        $stmt = $db->prepare('DELETE FROM users WHERE id = ?');
        $stmt->execute([$id]);
        if ($stmt->rowCount() === 0) jsonResponse(false, 'User not found.', [], 404);
        jsonResponse(true, 'User deleted.');
        break;

    default:
        jsonResponse(false, 'Method not allowed.', [], 405);
}
?>
