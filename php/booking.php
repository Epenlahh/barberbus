<?php
// =============================================
// BARBERBUS – Booking API
// File: api/booking.php
// Handles: POST (create), GET (list/detail)
// =============================================
require_once 'config.php';

$method = $_SERVER['REQUEST_METHOD'];

// ── GET: Fetch bookings (admin use) ──
if ($method === 'GET') {
    $db = getDB();

    $filter  = $_GET['filter']  ?? 'all';
    $date    = $_GET['date']    ?? date('Y-m-d');
    $barber  = $_GET['barber']  ?? '';
    $search  = $_GET['search']  ?? '';
    $id      = $_GET['id']      ?? '';

    // Single booking by ID
    if ($id) {
        $stmt = $db->prepare("
            SELECT b.*, c.name as client_name, c.email as client_email, c.phone as client_phone,
                   br.name as barber_name, s.name as service_name, s.duration_min
            FROM bookings b
            JOIN clients c ON b.client_id = c.id
            LEFT JOIN barbers br ON b.barber_id = br.id
            JOIN services s ON b.service_id = s.id
            WHERE b.id = ?
        ");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        echo json_encode(['success' => true, 'booking' => $result]);
        $db->close();
        exit();
    }

    // Build query
    $where = [];
    $params = [];
    $types  = '';

    if ($filter !== 'all') {
        $where[] = 'b.status = ?';
        $params[] = $filter;
        $types .= 's';
    }
    if ($date) {
        $where[] = 'b.booking_date = ?';
        $params[] = $date;
        $types .= 's';
    }
    if ($barber) {
        $where[] = 'b.barber_id = ?';
        $params[] = intval($barber);
        $types .= 'i';
    }
    if ($search) {
        $where[] = '(c.name LIKE ? OR b.ref_code LIKE ? OR c.phone LIKE ?)';
        $like = '%' . $search . '%';
        $params[] = $like; $params[] = $like; $params[] = $like;
        $types .= 'sss';
    }

    $sql = "
        SELECT b.id, b.ref_code, b.booking_date, b.booking_time, b.status,
               b.pay_method, b.pay_status, b.amount, b.notes, b.created_at,
               c.name as client_name, c.email as client_email, c.phone as client_phone,
               br.name as barber_name,
               s.name as service_name, s.duration_min
        FROM bookings b
        JOIN clients c ON b.client_id = c.id
        LEFT JOIN barbers br ON b.barber_id = br.id
        JOIN services s ON b.service_id = s.id
    ";
    if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
    $sql .= ' ORDER BY b.booking_date ASC, b.booking_time ASC';

    $stmt = $db->prepare($sql);
    if ($params) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Summary counts
    $counts = $db->query("
        SELECT status, COUNT(*) as cnt FROM bookings
        WHERE booking_date = '" . $db->real_escape_string($date) . "'
        GROUP BY status
    ")->fetch_all(MYSQLI_ASSOC);

    $summary = ['all' => 0, 'pending' => 0, 'confirmed' => 0, 'in_progress' => 0, 'done' => 0, 'cancelled' => 0];
    foreach ($counts as $c) {
        $summary[$c['status']] = (int)$c['cnt'];
        $summary['all'] += (int)$c['cnt'];
    }

    echo json_encode(['success' => true, 'bookings' => $rows, 'summary' => $summary]);
    $db->close();
    exit();
}

// ── POST: Create new booking (from customer booking form) ──
if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data) {
        // Try form POST
        $data = $_POST;
    }

    // Validate required fields
    $required = ['client_name', 'client_email', 'client_phone', 'service_id', 'booking_date', 'booking_time', 'pay_method'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => "Missing field: $field"]);
            exit();
        }
    }

    $db = getDB();

    // Upsert client
    $stmt = $db->prepare("INSERT INTO clients (name, email, phone) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE name=VALUES(name), phone=VALUES(phone)");
    $stmt->bind_param('sss', $data['client_name'], $data['client_email'], $data['client_phone']);
    $stmt->execute();
    $client_id = $db->insert_id ?: $db->query("SELECT id FROM clients WHERE email='" . $db->real_escape_string($data['client_email']) . "'")->fetch_assoc()['id'];

    // Get service price
    $svc = $db->query("SELECT price FROM services WHERE id=" . intval($data['service_id']))->fetch_assoc();
    if (!$svc) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid service']);
        exit();
    }
    $amount = $svc['price'];

    // Generate unique ref code
    $ref = 'BB-' . strtoupper(substr(md5(uniqid()), 0, 6));

    $barber_id = !empty($data['barber_id']) ? intval($data['barber_id']) : null;
    $notes     = $data['notes'] ?? '';

    $stmt = $db->prepare("
        INSERT INTO bookings (ref_code, client_id, barber_id, service_id, booking_date, booking_time, notes, pay_method, amount)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param('siiiisssd',
        $ref, $client_id, $barber_id,
        $data['service_id'], $data['booking_date'], $data['booking_time'],
        $notes, $data['pay_method'], $amount
    );

    if ($stmt->execute()) {
        $booking_id = $db->insert_id;
        echo json_encode([
            'success'    => true,
            'message'    => 'Booking created successfully',
            'ref_code'   => $ref,
            'booking_id' => $booking_id,
            'amount'     => $amount
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to create booking: ' . $db->error]);
    }

    $db->close();
    exit();
}

// ── PUT: Update booking status (admin action) ──
if ($method === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id   = intval($data['id'] ?? 0);

    if (!$id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Missing booking ID']);
        exit();
    }

    $db = getDB();
    $allowed_statuses = ['pending', 'confirmed', 'in_progress', 'done', 'cancelled'];

    if (!empty($data['status']) && in_array($data['status'], $allowed_statuses)) {
        $stmt = $db->prepare("UPDATE bookings SET status = ? WHERE id = ?");
        $stmt->bind_param('si', $data['status'], $id);
        $stmt->execute();
        echo json_encode(['success' => true, 'message' => 'Status updated to ' . $data['status']]);
    }

    if (!empty($data['pay_status'])) {
        $stmt = $db->prepare("UPDATE bookings SET pay_status = ? WHERE id = ?");
        $stmt->bind_param('si', $data['pay_status'], $id);
        $stmt->execute();
        echo json_encode(['success' => true, 'message' => 'Payment status updated']);
    }

    $db->close();
    exit();
}

// ── DELETE: Cancel booking ──
if ($method === 'DELETE') {
    $id = intval($_GET['id'] ?? 0);
    if (!$id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Missing ID']);
        exit();
    }
    $db = getDB();
    $stmt = $db->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    echo json_encode(['success' => true, 'message' => 'Booking cancelled']);
    $db->close();
    exit();
}

http_response_code(405);
echo json_encode(['success' => false, 'error' => 'Method not allowed']);
?>