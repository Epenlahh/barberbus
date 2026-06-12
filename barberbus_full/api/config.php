<?php
// =============================================
// BARBERBUS – DATABASE CONFIGURATION
// =============================================

define('DB_HOST', 'localhost:3366');
ini_set('display_errors', 0);
error_reporting(E_ALL);
define('DB_NAME', 'barberbus_db');
define('DB_USER', 'root');          // Change to your MySQL username
define('DB_PASS', '');              // Change to your MySQL password
define('DB_CHARSET', 'utf8mb4');

// JWT Secret for session tokens
define('JWT_SECRET', '5b8a5411561f221bdfaeef3d54fb61675a3ff62df1db3061edafaed3eaf3e897');

// API settings
define('API_VERSION', '1.0');
define('ALLOWED_ORIGIN', '*');      // Change to your domain in production

// ── PDO Connection ──
function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
            exit;
        }
    }
    return $pdo;
}

// ── CORS Headers ──
function setCORSHeaders(): void {
    header('Access-Control-Allow-Origin: ' . ALLOWED_ORIGIN);
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
    header('Content-Type: application/json; charset=UTF-8');
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(204);
        exit;
    }
}

// ── JSON Response Helper ──
function jsonResponse(bool $success, string $message, array $data = [], int $status = 200): void {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode(['success' => $success, 'message' => $message, 'data' => $data]);
    exit;
}

// ── Get Request Body ──
function getBody(): array {
    $raw = file_get_contents('php://input');
    return json_decode($raw, true) ?? [];
}

// ── Simple JWT ──
function createToken(array $payload): string {
    $header  = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT'])));
    $payload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode($payload)));
    $sig     = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(hash_hmac('sha256', "$header.$payload", JWT_SECRET, true)));
    return "$header.$payload.$sig";
}

function verifyToken(string $token): ?array {
    $parts = explode('.', $token);
    if (count($parts) !== 3) return null;
    [$header, $payload, $sig] = $parts;
    
    // Recalculate signature (ignoring padding in comparison)
    $expected = base64_encode(hash_hmac('sha256', "$header.$payload", JWT_SECRET, true));
    $expected = str_replace(['+', '/', '='], ['-', '_', ''], $expected);
    
    // Also need to normalize the sig from the token in case it was url-safe
    $sig = str_replace(['+', '/', '='], ['-', '_', ''], $sig);
    
    if (!hash_equals($expected, $sig)) return null;
    
    // Base64 decode with padding restoration
    $decodedPayload = base64_decode(str_replace(['-', '_'], ['+', '/'], $payload));
    if (!$decodedPayload) return null;
    
    $data = json_decode($decodedPayload, true);
    if (isset($data['exp']) && $data['exp'] < time()) return null;
    return $data;
}

function getBearerToken(): ?string {
    $headers = null;
    if (isset($_SERVER['Authorization'])) {
        $headers = trim($_SERVER["Authorization"]);
    } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
        $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
    } else if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
        $headers = trim($_SERVER["REDIRECT_HTTP_AUTHORIZATION"]);
    } else if (function_exists('apache_request_headers')) {
        $requestHeaders = apache_request_headers();
        foreach ($requestHeaders as $key => $value) {
            if (strtolower($key) === 'authorization') {
                $headers = trim($value);
                break;
            }
        }
    }
    
    if (!empty($headers)) {
        if (preg_match('/Bearer\s(\S+)/i', $headers, $matches)) {
            return $matches[1];
        }
    }
    return null;
}

function requireAuth(): array {
    $token = getBearerToken();
    $payload = verifyToken($token ?? '');
    if (!$payload) {
        jsonResponse(false, 'Unauthorized. Please login.', [], 401);
    }
    return $payload;
}

function requireAdmin(): array {
    $payload = requireAuth();
    if (($payload['role'] ?? '') !== 'admin') {
        jsonResponse(false, 'Admin access required.', [], 403);
    }
    return $payload;
}
?>
