<?php
/**
 * API: تفاصيل حجز - PUT (تعديل الحالة) / DELETE (حذف)
 * PUT    /newcar/api/booking_detail.php?id=3
 * DELETE /newcar/api/booking_detail.php?id=3
 */

require_once __DIR__ . '/../db/database.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    http_response_code(200); exit;
}

$bookingId = (int)($_GET['id'] ?? 0);
if ($bookingId <= 0) {
    jsonResponse(['error' => 'معرّف الحجز غير صحيح'], 400);
}

$db = getDB();

// ============================================================
// PUT: تعديل حالة الحجز
// ============================================================
if ($method === 'PUT') {
    $data   = getJsonBody();
    $status = trim($data['status'] ?? 'معلقة');

    $allowed = ['معلقة', 'مؤكدة', 'ملغاة', 'منتهية'];
    if (!in_array($status, $allowed, true)) {
        $status = 'معلقة';
    }

    $stmt = $db->prepare("UPDATE `bookings` SET `status` = ? WHERE `id` = ?");
    $stmt->execute([$status, $bookingId]);

    jsonResponse(['message' => 'تم تحديث حالة طلب المعاينة']);
}

// ============================================================
// DELETE: حذف حجز
// ============================================================
if ($method === 'DELETE') {
    $stmt = $db->prepare("DELETE FROM `bookings` WHERE `id` = ?");
    $stmt->execute([$bookingId]);
    jsonResponse(['message' => 'تم حذف الطلب بنجاح']);
}

jsonResponse(['error' => 'طريقة غير مدعومة'], 405);
