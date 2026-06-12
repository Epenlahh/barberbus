<?php
// Include the database config
require_once __DIR__ . '/../api/config.php';

try {
    $db = getDB();

    // 1. Fetch Store Settings
    $stmtStore = $db->prepare('SELECT * FROM store_settings LIMIT 1');
    $stmtStore->execute();
    $storeSettings = $stmtStore->fetch(PDO::FETCH_ASSOC);

    // Fallback defaults if no store settings in DB
    if (!$storeSettings) {
        $storeSettings = [
            'name' => 'BarberBus',
            'description' => 'Premium grooming experience for the modern gentleman.',
            'is_open' => 1,
            'business_hours' => 'Mon-Sat: 10am - 9pm',
            'location' => 'No. 5, Jalan PJ, Petaling Jaya',
            'phone' => '+60 12-345 6789',
            'profile_image' => ''
        ];
    }

    // 2. Helper function to fetch Page Content dynamically
    if (!function_exists('getPageContent')) {
        function getPageContent($pageName) {
            global $db;
            $stmt = $db->prepare('SELECT * FROM page_content WHERE page_name = ? LIMIT 1');
            $stmt->execute([$pageName]);
            $content = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$content) {
                // Fallback content if not found
                return [
                    'title' => ucfirst($pageName),
                    'hero' => 'Welcome to BarberBus',
                    'content' => '',
                    'footer' => ''
                ];
            }
            return $content;
        }
    }

} catch (Exception $e) {
    // Graceful fallback on DB failure
    $storeSettings = [
        'name' => 'BarberBus',
        'description' => 'Premium grooming experience for the modern gentleman.',
        'is_open' => 1,
        'business_hours' => 'Mon-Sat: 10am - 9pm',
        'location' => 'No. 5, Jalan PJ, Petaling Jaya',
        'phone' => '+60 12-345 6789',
        'profile_image' => ''
    ];

    if (!function_exists('getPageContent')) {
        function getPageContent($pageName) {
            return [
                'title' => ucfirst($pageName),
                'hero' => 'Welcome to BarberBus',
                'content' => '',
                'footer' => ''
            ];
        }
    }
}
?>
