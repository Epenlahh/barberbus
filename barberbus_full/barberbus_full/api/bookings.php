<?php
// =============================================
// BARBERBUS – BOOKINGS API
// GET    /api/bookings.php             → user's bookings
// POST   /api/bookings.php             → create booking
// PUT    /api/bookings.php?id=X        → update booking status
// DELETE /api/bookings.php?id=X        → cancel booking
// GET    /api/bookings.php?admin=1     → all bookings (admin)
// =============================================

require_once __DIR__ . '/config.php';
setCORSHeaders();

$db     = getDB();
$method = $_SERVER['REQUEST_METHOD'];
$id     = intval($_GET['id'] ?? 0);
$admin  = isset($_GET['admin']);

switch ($method) {

    // ── GET Bookings ──
    case 'GET':
        if ($admin) {
            requireAdmin();
            $stmt = $db->query("
                SELECT b.*, u.name AS user_name, u.email AS user_email, u.phone AS user_phone,
                       s.name AS service_name, s.duration,
                       br.name AS barber_name
                FROM bookings b
                JOIN users    u  ON b.user_id    = u.id
                JOIN services s  ON b.service_id = s.id
                LEFT JOIN barbers br ON b.barber_id = br.id
                ORDER BY b.booking_date DESC, b.booking_time DESC
            ");
        } else {
            $auth = requireAuth();
            $stmt = $db->prepare("
                SELECT b.*, s.name AS service_name, s.duration,
                       br.name AS barber_name
                FROM bookings b
                JOIN services s  ON b.service_id = s.id
                LEFT JOIN barbers br ON b.barber_id = br.id
                WHERE b.user_id = ?
                ORDER BY b.booking_date DESC, b.booking_time DESC
            ");
            $stmt->execute([$auth['sub']]);
        }
        $bookings = $stmt->fetchAll();
        jsonResponse(true, 'OK', ['bookings' => $bookings]);
        break;

    // ── CREATE Booking ──
    case 'POST':
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        $auth = null;
        if ($authHeader) {
            $token = str_replace('Bearer ', '', $authHeader);
            $auth = verifyToken($token);
            if (!$auth) {
                jsonResponse(false, 'Invalid auth token.', [], 401);
            }
        }

        $body       = getBody();
        $serviceId  = intval($body['service_id']   ?? 0);
        $barberId   = intval($body['barber_id']    ?? 0) ?: null;
        $date       = trim($body['booking_date']   ?? '');
        $time       = trim($body['booking_time']   ?? '');
        $payMethod  = trim($body['pay_method']     ?? 'cash');
        $notes      = trim($body['notes']          ?? '');
        $name       = trim($body['name']          ?? '');
        $email      = trim($body['email']         ?? '');
        $phone      = trim($body['phone']         ?? '');

        if (!$serviceId || !$date || !$time) {
            jsonResponse(false, 'Service, date and time are required.', [], 422);
        }
        if (!$auth && (!$name || !$email)) {
            jsonResponse(false, 'Name and email are required for guest bookings.', [], 422);
        }
        if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            jsonResponse(false, 'A valid email address is required.', [], 422);
        }
        if (strtotime($date) < strtotime(date('Y-m-d'))) {
            jsonResponse(false, 'Booking date must be today or in the future.', [], 422);
        }

        // Get service price
        $s = $db->prepare('SELECT id, price FROM services WHERE id = ? AND is_active = 1');
        $s->execute([$serviceId]);
        $service = $s->fetch();
        if (!$service) jsonResponse(false, 'Service not found.', [], 404);

        // Determine booking user
        if ($auth) {
            $userId = $auth['sub'];
        } else {
            $userStmt = $db->prepare('SELECT id FROM users WHERE email = ?');
            $userStmt->execute([$email]);
            $existingUser = $userStmt->fetch();
            if ($existingUser) {
                $userId = $existingUser['id'];
                $db->prepare('UPDATE users SET name = ?, phone = ? WHERE id = ?')->execute([$name, $phone, $userId]);
            } else {
                $passwordHash = password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT);
                $insertUser = $db->prepare('INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)');
                $insertUser->execute([$name, $email, $phone, $passwordHash]);
                $userId = $db->lastInsertId();
            }
        }

        // Check for time slot conflict
        $conflict = $db->prepare(
            'SELECT id FROM bookings
            WHERE barber_id = ? AND booking_date = ? AND booking_time = ?
              AND status NOT IN ("cancelled")'
        );
        $conflict->execute([$barberId, $date, $time]);
        if ($conflict->fetch()) {
            jsonResponse(false, 'This time slot is already booked. Please choose another.', [], 409);
        }

        $stmt = $db->prepare(
            'INSERT INTO bookings (user_id, barber_id, service_id, booking_date, booking_time, total_price, pay_method, notes)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $userId,
            $barberId,
            $serviceId,
            $date,
            $time,
            $service['price'],
            $payMethod,
            $notes
        ]);
        $bookingId = $db->lastInsertId();

        jsonResponse(true, 'Booking created successfully!', ['booking_id' => $bookingId]);
        break;

    // ── UPDATE Booking (status) ──
    case 'PUT':
        $body   = getBody();
        $status = trim($body['status'] ?? '');
        $valid  = ['pending','confirmed','completed','cancelled'];

        if (!$id) jsonResponse(false, 'Booking ID is required.', [], 422);
        if (!in_array($status, $valid)) jsonResponse(false, 'Invalid status.', [], 422);

        // Admin can update any; user can only cancel their own
        if (isset($_GET['admin'])) {
            requireAdmin();
            $stmt = $db->prepare('UPDATE bookings SET status=? WHERE id=?');
            $stmt->execute([$status, $id]);
        } else {
            $auth = requireAuth();
            if ($status !== 'cancelled') jsonResponse(false, 'Users can only cancel bookings.', [], 403);
            $stmt = $db->prepare('UPDATE bookings SET status="cancelled" WHERE id=? AND user_id=? AND status="pending"');
            $stmt->execute([$id, $auth['sub']]);
            if ($stmt->rowCount() === 0) {
                jsonResponse(false, 'Booking not found or cannot be cancelled.', [], 404);
            }
        }
        jsonResponse(true, 'Booking updated.');
        break;

    // ── DELETE Booking (admin only) ──
    case 'DELETE':
        requireAdmin();
        if (!$id) jsonResponse(false, 'Booking ID is required.', [], 422);
        $db->prepare('DELETE FROM bookings WHERE id=?')->execute([$id]);
        jsonResponse(true, 'Booking deleted.');
        break;

    default:
        jsonResponse(false, 'Method not allowed.', [], 405);
}
?>
