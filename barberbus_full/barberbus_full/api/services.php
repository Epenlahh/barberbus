<?php
// =============================================
// BARBERBUS – SERVICES & BARBERS API
// GET  /api/services.php           → all services
// POST /api/services.php           → create (admin)
// PUT  /api/services.php?id=X      → update (admin)
// DELETE /api/services.php?id=X    → delete (admin)
// =============================================

require_once __DIR__ . '/config.php';
setCORSHeaders();

$db     = getDB();
$method = $_SERVER['REQUEST_METHOD'];
$id     = intval($_GET['id'] ?? 0);
$type   = $_GET['type'] ?? 'services'; // 'services' or 'barbers'

if ($type === 'barbers') {
    handleBarbers($db, $method, $id);
} else {
    handleServices($db, $method, $id);
}

function handleServices(PDO $db, string $method, int $id): void {
    switch ($method) {
        case 'GET':
            $stmt = $db->query('SELECT * FROM services WHERE is_active=1 ORDER BY category, price');
            jsonResponse(true, 'OK', ['services' => $stmt->fetchAll()]);
            break;

        case 'POST':
            requireAdmin();
            $b = getBody();
            $name = trim($b['name'] ?? '');
            $price = floatval($b['price'] ?? 0);
            $duration = intval($b['duration'] ?? 0);
            if (!$name || !$price || !$duration) jsonResponse(false, 'Name, price and duration are required.', [], 422);
            $stmt = $db->prepare('INSERT INTO services (name, description, price, duration, category) VALUES (?,?,?,?,?)');
            $stmt->execute([$name, $b['description'] ?? '', $price, $duration, $b['category'] ?? 'haircut']);
            jsonResponse(true, 'Service created.', ['id' => $db->lastInsertId()]);
            break;

        case 'PUT':
            requireAdmin();
            if (!$id) jsonResponse(false, 'ID required.', [], 422);
            $b = getBody();
            $stmt = $db->prepare('UPDATE services SET name=?, description=?, price=?, duration=?, category=?, is_active=? WHERE id=?');
            $stmt->execute([
                $b['name'], $b['description'], $b['price'],
                $b['duration'], $b['category'], intval($b['is_active'] ?? 1), $id
            ]);
            jsonResponse(true, 'Service updated.');
            break;

        case 'DELETE':
            requireAdmin();
            if (!$id) jsonResponse(false, 'ID required.', [], 422);
            $db->prepare('UPDATE services SET is_active=0 WHERE id=?')->execute([$id]);
            jsonResponse(true, 'Service deactivated.');
            break;

        default:
            jsonResponse(false, 'Method not allowed.', [], 405);
    }
}

function handleBarbers(PDO $db, string $method, int $id): void {
    switch ($method) {
        case 'GET':
            // For admin, return all (including inactive) when ?admin=1 is set
            $admin = isset($_GET['admin']);
            $where = $admin ? '' : ' WHERE is_active=1';
            $stmt  = $db->query('SELECT b.*, u.email AS officer_email FROM barbers b LEFT JOIN users u ON u.name = b.name AND u.role = \'officer\'' . $where . ' ORDER BY b.name');
            jsonResponse(true, 'OK', ['barbers' => $stmt->fetchAll()]);
            break;

        case 'POST':
            requireAdmin();
            $b    = getBody();
            $name = trim($b['name'] ?? '');
            if (!$name) jsonResponse(false, 'Name required.', [], 422);

            $stmt = $db->prepare('INSERT INTO barbers (name, specialty, bio, experience, rating) VALUES (?,?,?,?,?)');
            $stmt->execute([$name, $b['specialty'] ?? '', $b['bio'] ?? '', intval($b['experience'] ?? 0), floatval($b['rating'] ?? 5.0)]);
            $barberId = $db->lastInsertId();

            // If officer credentials provided, create linked officer account
            $officerEmail = trim($b['officer_email'] ?? '');
            $officerPass  = trim($b['officer_password'] ?? '');
            if ($officerEmail && $officerPass) {
                if (!filter_var($officerEmail, FILTER_VALIDATE_EMAIL)) {
                    jsonResponse(false, 'Barber created but officer email is invalid.', ['id' => $barberId], 422);
                }
                $exists = $db->prepare('SELECT id FROM users WHERE email = ?');
                $exists->execute([$officerEmail]);
                if ($exists->fetch()) {
                    jsonResponse(false, 'Barber created but that officer email is already in use.', ['id' => $barberId], 409);
                }
                $hash = password_hash($officerPass, PASSWORD_BCRYPT);
                $db->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, \'officer\')')
                   ->execute([$name, $officerEmail, $hash]);
            }

            jsonResponse(true, 'Barber created.', ['id' => $barberId]);
            break;

        case 'PUT':
            requireAdmin();
            if (!$id) jsonResponse(false, 'ID required.', [], 422);
            $b = getBody();

            $stmt = $db->prepare('UPDATE barbers SET name=?, specialty=?, bio=?, experience=?, rating=?, is_active=? WHERE id=?');
            $stmt->execute([$b['name'], $b['specialty'] ?? '', $b['bio'] ?? '', intval($b['experience'] ?? 0), floatval($b['rating'] ?? 5.0), intval($b['is_active'] ?? 1), $id]);

            // Handle officer account update
            $officerEmail = trim($b['officer_email'] ?? '');
            $officerPass  = trim($b['officer_password'] ?? '');
            $barberName   = trim($b['name'] ?? '');

            if ($officerEmail) {
                // Check if officer already exists with this barber's name
                $existing = $db->prepare("SELECT id FROM users WHERE name = ? AND role = 'officer'");
                $existing->execute([$barberName]);
                $existingUser = $existing->fetch();

                if ($existingUser) {
                    // Update existing officer account
                    $updateFields = ['email = ?'];
                    $updateParams = [$officerEmail];
                    if ($officerPass) {
                        $updateFields[] = 'password = ?';
                        $updateParams[] = password_hash($officerPass, PASSWORD_BCRYPT);
                    }
                    $updateParams[] = $existingUser['id'];
                    $db->prepare('UPDATE users SET ' . implode(', ', $updateFields) . ' WHERE id = ?')
                       ->execute($updateParams);
                } else {
                    // Create new officer account (only if password provided)
                    if ($officerPass) {
                        $emailCheck = $db->prepare('SELECT id FROM users WHERE email = ?');
                        $emailCheck->execute([$officerEmail]);
                        if (!$emailCheck->fetch()) {
                            $hash = password_hash($officerPass, PASSWORD_BCRYPT);
                            $db->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, \'officer\')')
                               ->execute([$barberName, $officerEmail, $hash]);
                        }
                    }
                }
            }

            jsonResponse(true, 'Barber updated.');
            break;

        case 'DELETE':
            requireAdmin();
            if (!$id) jsonResponse(false, 'ID required.', [], 422);
            $db->prepare('UPDATE barbers SET is_active=0 WHERE id=?')->execute([$id]);
            jsonResponse(true, 'Barber deactivated.');
            break;

        default:
            jsonResponse(false, 'Method not allowed.', [], 405);
    }
}
?>
