<?php
/**
 * API: الحجوزات / طلبات المعاينة
 * GET  /newcar/api/bookings.php        ← جلب جميع الحجوزات
 * POST /newcar/api/bookings.php        ← إضافة حجز جديد
 */

require_once __DIR__ . '/../db/database.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    http_response_code(200); exit;
}

$db = getDB();

// ============================================================
// POST: إضافة حجز جديد
// ============================================================
if ($method === 'POST') {
    $data = getJsonBody();

    $car_id         = isset($data['car_id']) ? (int)$data['car_id'] : null;
    $car_title      = trim($data['car_title']       ?? '');
    $customer_name  = trim($data['customer_name']   ?? '');
    $customer_phone = trim($data['customer_phone']  ?? '');
    $preferred_date = trim($data['preferred_date']  ?? '');
    $notes          = trim($data['notes']           ?? '');

    if ($customer_name === '' || $customer_phone === '') {
        jsonResponse(['error' => 'يرجى إدخال اسم العميل ورقم الهاتف للتواصل'], 400);
    }

    // التحقق من صيغة التاريخ
    $dateParsed = $preferred_date !== '' ? $preferred_date : null;

    $stmt = $db->prepare("INSERT INTO `bookings`
        (`car_id`, `car_title`, `customer_name`, `customer_phone`, `preferred_date`, `notes`)
        VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$car_id, $car_title, $customer_name, $customer_phone, $dateParsed, $notes]);
    $booking_id = (int)$db->lastInsertId();

    jsonResponse([
        'message'    => 'تم استلام طلب المعاينة بنجاح، سيقوم فريق المعرض بالتواصل معك قريباً',
        'booking_id' => $booking_id,
    ], 201);
}

// ============================================================
// GET: جلب جميع الحجوزات
// ============================================================
if ($method === 'GET') {
    $rows = $db->query("SELECT * FROM `bookings` ORDER BY `id` DESC")->fetchAll();
    jsonResponse(['bookings' => $rows]);
}

jsonResponse(['error' => 'طريقة غير مدعومة'], 405);
