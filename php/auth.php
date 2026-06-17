<?php
// =============================================
// BARBERBUS – Admin Login API
// File: api/auth.php
// =============================================
require_once 'config.php';
session_start();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true) ?: $_POST;

    $username = trim($data['username'] ?? '');
    $password = trim($data['password'] ?? '');

    if (!$username || !$password) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Username and password required']);
        exit();
    }

    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM admin_users WHERE username = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    // For demo: accept password "admin123" - in production use password_verify()
    if ($user && ($password === 'admin123' || password_verify($password, $user['password_hash']))) {
        $_SESSION['admin_id']   = $user['id'];
        $_SESSION['admin_name'] = $user['name'];
        $_SESSION['admin_role'] = $user['role'];

        echo json_encode([
            'success' => true,
            'message' => 'Login successful',
            'name'    => $user['name'],
            'role'    => $user['role']
        ]);
    } else {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Invalid username or password']);
    }

    $db->close();
    exit();
}

if ($method === 'GET' && ($_GET['action'] ?? '') === 'logout') {
    session_destroy();
    echo json_encode(['success' => true, 'message' => 'Logged out']);
    exit();
}

if ($method === 'GET' && ($_GET['action'] ?? '') === 'check') {
    echo json_encode([
        'logged_in' => isset($_SESSION['admin_id']),
        'name'      => $_SESSION['admin_name'] ?? null
    ]);
    exit();
}
?>