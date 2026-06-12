<?php
// =============================================
// BARBERBUS – AUTH API
// POST /api/auth.php?action=login
// POST /api/auth.php?action=register
// POST /api/auth.php?action=me  (requires token)
// =============================================

require_once __DIR__ . '/config.php';
setCORSHeaders();

$action = $_GET['action'] ?? '';
$body   = getBody();

switch ($action) {

    // ── REGISTER ──
    case 'register':
        $name  = trim($body['name']  ?? '');
        $email = trim($body['email'] ?? '');
        $phone = trim($body['phone'] ?? '');
        $pass  = $body['password']   ?? '';

        if (!$name || !$email || !$pass) {
            jsonResponse(false, 'Name, email and password are required.', [], 422);
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            jsonResponse(false, 'Invalid email address.', [], 422);
        }
        if (strlen($pass) < 6) {
            jsonResponse(false, 'Password must be at least 6 characters.', [], 422);
        }

        $db = getDB();

        // Check if email already exists
        $stmt = $db->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            jsonResponse(false, 'Email already registered. Please login.', [], 409);
        }

        $hash = password_hash($pass, PASSWORD_BCRYPT);
        $stmt = $db->prepare('INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, "user")');
        $stmt->execute([$name, $email, $phone, $hash]);
        $userId = $db->lastInsertId();

        $token = createToken([
            'sub'   => $userId,
            'email' => $email,
            'role'  => 'user',
            'exp'   => time() + (7 * 24 * 3600), // 7 days
        ]);

        jsonResponse(true, 'Account created successfully!', [
            'token' => $token,
            'user'  => ['id' => $userId, 'name' => $name, 'email' => $email, 'role' => 'user'],
        ]);
        break;

    // ── LOGIN ──
    case 'login':
        $email = trim($body['email']    ?? '');
        $pass  = $body['password']      ?? '';

        if (!$email || !$pass) {
            jsonResponse(false, 'Email and password are required.', [], 422);
        }

        $db   = getDB();
        $stmt = $db->prepare('SELECT id, name, email, phone, password, role, avatar FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($pass, $user['password'])) {
            jsonResponse(false, 'Invalid email or password.', [], 401);
        }

        $token = createToken([
            'sub'   => $user['id'],
            'email' => $user['email'],
            'role'  => $user['role'],
            'exp'   => time() + (7 * 24 * 3600),
        ]);

        unset($user['password']);
        jsonResponse(true, 'Login successful!', [
            'token' => $token,
            'user'  => $user,
        ]);
        break;

    // ── GET CURRENT USER ──
    case 'me':
        $auth = requireAuth();
        $db   = getDB();
        $stmt = $db->prepare('SELECT id, name, email, phone, role, avatar, created_at FROM users WHERE id = ?');
        $stmt->execute([$auth['sub']]);
        $user = $stmt->fetch();
        if (!$user) jsonResponse(false, 'User not found.', [], 404);
        jsonResponse(true, 'OK', ['user' => $user]);
        break;

    // ── UPDATE PROFILE ──
    case 'update':
        $auth  = requireAuth();
        $name  = trim($body['name']  ?? '');
        $phone = trim($body['phone'] ?? '');
        if (!$name) jsonResponse(false, 'Name is required.', [], 422);
        $db   = getDB();
        $stmt = $db->prepare('UPDATE users SET name=?, phone=? WHERE id=?');
        $stmt->execute([$name, $phone, $auth['sub']]);
        jsonResponse(true, 'Profile updated successfully.');
        break;

    // ── CHANGE PASSWORD ──
    case 'change-password':
        $auth    = requireAuth();
        $current = $body['current_password'] ?? '';
        $newPass = $body['new_password']     ?? '';
        if (!$current || !$newPass) jsonResponse(false, 'Both passwords are required.', [], 422);
        if (strlen($newPass) < 6) jsonResponse(false, 'New password must be at least 6 characters.', [], 422);

        $db   = getDB();
        $stmt = $db->prepare('SELECT password FROM users WHERE id=?');
        $stmt->execute([$auth['sub']]);
        $user = $stmt->fetch();
        if (!password_verify($current, $user['password'])) {
            jsonResponse(false, 'Current password is incorrect.', [], 401);
        }
        $hash = password_hash($newPass, PASSWORD_BCRYPT);
        $db->prepare('UPDATE users SET password=? WHERE id=?')->execute([$hash, $auth['sub']]);
        jsonResponse(true, 'Password changed successfully.');
        break;

    default:
        jsonResponse(false, 'Invalid action.', [], 400);
}
?>
