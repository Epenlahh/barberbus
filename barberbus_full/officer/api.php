<?php
// =============================================
// BARBERBUS – OFFICER API
// All endpoints for barber/officer dashboard
// =============================================
require_once __DIR__ . '/../api/config.php';
setCORSHeaders();

$db     = getDB();
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

// ── AUTH: officer role (admin or barber) ──
function requireOfficer(): array {
    $auth  = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    $token = str_replace('Bearer ', '', $auth);
    global $db;
    $payload = verifyToken($token);
    if (!$payload) jsonResponse(false, 'Unauthorised.', [], 401);
    $role = strtolower(trim($payload['role'] ?? ''));
    if (!in_array($role, ['admin','officer'])) {
        jsonResponse(false, 'Officer access required.', [], 403);
    }
    $payload['role'] = $role;
    return $payload;
}

function getOfficerBarberId(array $payload): ?int {
    global $db;
    if (($payload['role'] ?? '') !== 'officer') {
        return null;
    }
    $name = trim($payload['name'] ?? '');
    if (!$name) {
        return null;
    }
    $name = preg_replace('/\s*\(Officer\)$/i', '', $name);
    $stmt = $db->prepare('SELECT id FROM barbers WHERE name LIKE ? LIMIT 1');
    $stmt->execute([$name]);
    $barber = $stmt->fetch();
    return $barber ? (int)$barber['id'] : null;
}

function getBarberFilter(array $payload, array &$params): string {
    $barberId = getOfficerBarberId($payload);
    if ($barberId) {
        $params[] = $barberId;
        return ' AND b.barber_id = ?';
    }
    return '';
}

switch ($action) {

    // ─────────────────────────────────────────
    // TODAY'S FULL SCHEDULE
    // ─────────────────────────────────────────
    case 'today':
        $payload = requireOfficer();
        $today = date('Y-m-d');
        $params = [$today];
        $barberFilter = getBarberFilter($payload, $params);
        $stmt  = $db->prepare("
            SELECT b.id, b.booking_date, b.booking_time, b.status,
                   b.total_price, b.pay_method, b.notes,
                   u.name  AS customer_name,  u.email AS customer_email,
                   u.phone AS customer_phone,
                   s.name  AS service_name,   s.duration,
                   br.name AS barber_name,    br.id   AS barber_id
            FROM bookings b
            JOIN users    u  ON b.user_id    = u.id
            JOIN services s  ON b.service_id = s.id
            LEFT JOIN barbers br ON b.barber_id = br.id
            WHERE b.booking_date = ?" . $barberFilter . "
            ORDER BY b.booking_time ASC
        ");
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        // Group by hour for timeline
        $timeline = [];
        foreach ($rows as $r) {
            $h = substr($r['booking_time'], 0, 2) . ':00';
            $timeline[$h][] = $r;
        }
        jsonResponse(true, 'OK', ['schedule' => $rows, 'timeline' => $timeline, 'date' => $today]);
        break;

    // ─────────────────────────────────────────
    // LIVE QUEUE  (pending + confirmed TODAY sorted by time)
    // ─────────────────────────────────────────
    case 'queue':
        $payload = requireOfficer();
        $today = date('Y-m-d');
        $params = [$today];
        $barberFilter = getBarberFilter($payload, $params);
        $stmt  = $db->prepare("
            SELECT b.id, b.booking_time, b.status, b.notes,
                   b.total_price, b.pay_method,
                   u.name  AS customer_name,  u.phone AS customer_phone,
                   s.name  AS service_name,   s.duration,
                   br.name AS barber_name,    br.id   AS barber_id,
                   TIMESTAMPDIFF(MINUTE, NOW(), CONCAT(b.booking_date,' ',b.booking_time)) AS mins_until
            FROM bookings b
            JOIN users    u  ON b.user_id    = u.id
            JOIN services s  ON b.service_id = s.id
            LEFT JOIN barbers br ON b.barber_id = br.id
            WHERE b.booking_date = ?" . $barberFilter . "
              AND b.status IN ('pending','confirmed')
            ORDER BY b.booking_time ASC
            LIMIT 30
        ");
        $stmt->execute($params);
        jsonResponse(true, 'OK', ['queue' => $stmt->fetchAll()]);
        break;

    // ─────────────────────────────────────────
    // QUICK STATUS UPDATE (confirm/complete/cancel)
    // ─────────────────────────────────────────
    case 'update-status':
        requireOfficer();
        if ($method !== 'POST') jsonResponse(false, 'POST required.', [], 405);
        $b      = getBody();
        $id     = intval($b['booking_id'] ?? 0);
        $status = trim($b['status'] ?? '');
        $valid  = ['pending','confirmed','completed','cancelled'];
        if (!$id || !in_array($status, $valid)) jsonResponse(false, 'Invalid data.', [], 422);
        $db->prepare('UPDATE bookings SET status=?, updated_at=NOW() WHERE id=?')->execute([$status, $id]);
        jsonResponse(true, 'Status updated to ' . $status . '.');
        break;

    // ─────────────────────────────────────────
    // WALK-IN: create booking without user account
    // ─────────────────────────────────────────
    case 'walkin':
        requireOfficer();
        if ($method !== 'POST') jsonResponse(false, 'POST required.', [], 405);
        $b = getBody();

        $name      = trim($b['customer_name']  ?? '');
        $phone     = trim($b['customer_phone'] ?? '');
        $serviceId = intval($b['service_id']   ?? 0);
        $barberId  = intval($b['barber_id']    ?? 0) ?: null;
        $time      = trim($b['booking_time']   ?? date('H:i:s'));
        $payMethod = trim($b['pay_method']     ?? 'cash');
        $notes     = trim($b['notes']          ?? 'Walk-in customer');

        if (!$name || !$serviceId) jsonResponse(false, 'Name and service required.', [], 422);

        // Find or create a guest user for walk-ins
        $userStmt = $db->prepare("SELECT id FROM users WHERE email=?");
        $fakeEmail = 'walkin_' . preg_replace('/\W+/','', strtolower($name)) . '_' . time() . '@barberbus.walkin';
        $userStmt->execute([$fakeEmail]);
        $existing = $userStmt->fetch();

        if ($existing) {
            $userId = $existing['id'];
        } else {
            $pass = password_hash('walkin_' . rand(1000,9999), PASSWORD_BCRYPT);
            $ins  = $db->prepare("INSERT INTO users (name,email,phone,password,role) VALUES (?,?,?,?,'user')");
            $ins->execute([$name, $fakeEmail, $phone, $pass]);
            $userId = $db->lastInsertId();
        }

        $svc = $db->prepare('SELECT price FROM services WHERE id=? AND is_active=1');
        $svc->execute([$serviceId]);
        $service = $svc->fetch();
        if (!$service) jsonResponse(false, 'Service not found.', [], 404);

        $today = date('Y-m-d');
        $ins2  = $db->prepare("INSERT INTO bookings
            (user_id,barber_id,service_id,booking_date,booking_time,status,total_price,pay_method,notes)
            VALUES (?,?,?,?,?,'confirmed',?,?,?)");
        $ins2->execute([$userId, $barberId, $serviceId, $today, $time, $service['price'], $payMethod, $notes]);
        jsonResponse(true, 'Walk-in added to queue!', ['booking_id' => $db->lastInsertId()]);
        break;

    // ─────────────────────────────────────────
    // OFFICER STATS (live numbers for their view)
    // ─────────────────────────────────────────
    case 'stats':
        $payload = requireOfficer();
        $today = date('Y-m-d');
        $statsParams = [$today];
        $barberFilter = getBarberFilter($payload, $statsParams);

        $todayTotal  = $db->prepare("SELECT COUNT(*) FROM bookings b WHERE b.booking_date=?" . $barberFilter);
        $todayTotal->execute($statsParams); $todayTotal = (int)$todayTotal->fetchColumn();

        $todayDone   = $db->prepare("SELECT COUNT(*) FROM bookings b WHERE b.booking_date=? AND b.status='completed'" . $barberFilter);
        $todayDone->execute($statsParams); $todayDone = (int)$todayDone->fetchColumn();

        $todayRev    = $db->prepare("SELECT COALESCE(SUM(b.total_price),0) FROM bookings b WHERE b.booking_date=? AND b.status='completed'" . $barberFilter);
        $todayRev->execute($statsParams); $todayRev = (float)$todayRev->fetchColumn();

        $pending     = $db->prepare("SELECT COUNT(*) FROM bookings b WHERE b.booking_date=? AND b.status='pending'" . $barberFilter);
        $pending->execute($statsParams); $pending = (int)$pending->fetchColumn();

        $confirmed   = $db->prepare("SELECT COUNT(*) FROM bookings b WHERE b.booking_date=? AND b.status='confirmed'" . $barberFilter);
        $confirmed->execute($statsParams); $confirmed = (int)$confirmed->fetchColumn();

        $cancelled   = $db->prepare("SELECT COUNT(*) FROM bookings b WHERE b.booking_date=? AND b.status='cancelled'" . $barberFilter);
        $cancelled->execute($statsParams); $cancelled = (int)$cancelled->fetchColumn();

        $allParams = [];
        $allFilter = '';
        if (($payload['role'] ?? '') === 'officer') {
            $allFilter = getBarberFilter($payload, $allParams);
        }
        $totalBookingsStmt = $db->prepare('SELECT COUNT(*) FROM bookings b WHERE 1=1' . $allFilter);
        $totalBookingsStmt->execute($allParams);
        $totalBookings = (int)$totalBookingsStmt->fetchColumn();

        // Next customer
        $nextParams = [$today];
        $nextFilter = getBarberFilter($payload, $nextParams);
        $next = $db->prepare("
            SELECT b.booking_time, u.name AS customer_name, s.name AS service_name, br.name AS barber_name
            FROM bookings b
            JOIN users u ON b.user_id=u.id JOIN services s ON b.service_id=s.id
            LEFT JOIN barbers br ON b.barber_id=br.id
            WHERE b.booking_date=? AND b.status IN ('pending','confirmed')" . $nextFilter . "
              AND b.booking_time >= TIME(NOW())
            ORDER BY b.booking_time ASC LIMIT 1
        ");
        $next->execute($nextParams); $nextCustomer = $next->fetch() ?: null;

        // Barber utilisation today
        $barberUtilParams = [];
        $barberUtilWhere = '';
        if (($payload['role'] ?? '') === 'officer') {
            $barberId = getOfficerBarberId($payload);
            if ($barberId) {
                $barberUtilWhere = ' AND br.id = ?';
                $barberUtilParams[] = $barberId;
            }
        }
        $barberUtil = $db->prepare("
            SELECT br.name, br.id,
                   COUNT(CASE WHEN b.booking_date='$today' AND b.status NOT IN ('cancelled') THEN 1 END) AS jobs_today,
                   COUNT(CASE WHEN b.booking_date='$today' AND b.status='completed' THEN 1 END) AS done_today
            FROM barbers br
            LEFT JOIN bookings b ON br.id=b.barber_id
            WHERE br.is_active=1" . $barberUtilWhere . "
            GROUP BY br.id, br.name
            ORDER BY jobs_today DESC
        ");
        $barberUtil->execute($barberUtilParams);
        $barberUtil = $barberUtil->fetchAll();

        jsonResponse(true, 'OK', [
            'today_total'    => $todayTotal,
            'total_bookings' => $totalBookings,
            'today_done'     => $todayDone,
            'today_revenue'  => $todayRev,
            'pending'        => $pending,
            'confirmed'      => $confirmed,
            'cancelled'      => $cancelled,
            'remaining'      => $todayTotal - $todayDone - $cancelled,
            'next_customer'  => $nextCustomer,
            'barber_util'    => $barberUtil,
        ]);
        break;

    // ─────────────────────────────────────────
    // NOTIFICATIONS (new bookings since X seconds ago)
    // ─────────────────────────────────────────
    case 'notifications':
        requireOfficer();
        $since = intval($_GET['since'] ?? 30); // seconds
        $stmt  = $db->prepare("
            SELECT b.id, b.booking_date, b.booking_time, b.status, b.created_at,
                   u.name AS customer_name, s.name AS service_name
            FROM bookings b
            JOIN users u ON b.user_id=u.id
            JOIN services s ON b.service_id=s.id
            WHERE b.created_at >= NOW() - INTERVAL ? SECOND
            ORDER BY b.created_at DESC
            LIMIT 20
        ");
        $stmt->execute([$since]);
        jsonResponse(true, 'OK', ['notifications' => $stmt->fetchAll()]);
        break;

    // ─────────────────────────────────────────
    // OFFICER LOGIN (same users table, admin or officer role)
    // ─────────────────────────────────────────
    case 'login':
        if ($method !== 'POST') jsonResponse(false, 'POST required.', [], 405);
        $b     = getBody();
        $email = trim($b['email']    ?? '');
        $pass  = trim($b['password'] ?? '');
        if (!$email || !$pass) jsonResponse(false, 'Email and password required.', [], 422);

        $stmt = $db->prepare("SELECT id,name,email,password,role FROM users WHERE email=?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if (!$user || !password_verify($pass, $user['password'])) {
            jsonResponse(false, 'Invalid email or password.', [], 401);
        }

        $user['role'] = strtolower(trim($user['role'] ?? ''));
        if (!in_array($user['role'], ['admin','officer'])) {
            jsonResponse(false, 'Your account does not have officer access.', [], 403);
        }

        $token = createToken(['sub'=>$user['id'],'email'=>$user['email'],'role'=>$user['role'],'exp'=>time()+86400*7]);
        unset($user['password']);
        jsonResponse(true, 'Login successful.', ['token'=>$token,'user'=>$user]);
        break;

    // ─────────────────────────────────────────
    // SEARCH customer by name/phone
    // ─────────────────────────────────────────
    case 'search':
        requireOfficer();
        $q = trim($_GET['q'] ?? '');
        if (strlen($q) < 2) jsonResponse(false, 'Query too short.', [], 422);
        $like = '%' . $q . '%';
        $stmt = $db->prepare("
            SELECT u.id, u.name, u.email, u.phone,
                   COUNT(b.id) AS total_bookings,
                   MAX(b.booking_date) AS last_visit
            FROM users u
            LEFT JOIN bookings b ON u.id=b.user_id
            WHERE (u.name LIKE ? OR u.phone LIKE ?) AND u.role='user'
            GROUP BY u.id
            ORDER BY last_visit DESC
            LIMIT 10
        ");
        $stmt->execute([$like, $like]);
        jsonResponse(true, 'OK', ['results' => $stmt->fetchAll()]);
        break;

    // ─────────────────────────────────────────
    // CUSTOMER HISTORY
    // ─────────────────────────────────────────
    case 'customer-history':
        requireOfficer();
        $uid  = intval($_GET['user_id'] ?? 0);
        if (!$uid) jsonResponse(false, 'user_id required.', [], 422);
        $stmt = $db->prepare("
            SELECT b.*, s.name AS service_name, br.name AS barber_name
            FROM bookings b
            JOIN services s ON b.service_id=s.id
            LEFT JOIN barbers br ON b.barber_id=br.id
            WHERE b.user_id=?
            ORDER BY b.booking_date DESC, b.booking_time DESC
            LIMIT 20
        ");
        $stmt->execute([$uid]);
        $user = $db->prepare("SELECT id,name,email,phone,created_at FROM users WHERE id=?");
        $user->execute([$uid]);
        jsonResponse(true, 'OK', ['history' => $stmt->fetchAll(), 'customer' => $user->fetch()]);
        break;

    default:
        jsonResponse(false, 'Unknown action: ' . $action, [], 400);
}
?>
