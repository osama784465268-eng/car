<?php
/**
 * API: تفاصيل سيارة واحدة - GET / PUT / DELETE
 * GET    /newcar/api/car_detail.php?id=5
 * PUT    /newcar/api/car_detail.php?id=5
 * DELETE /newcar/api/car_detail.php?id=5
 */

require_once __DIR__ . '/../db/database.php';

$method = $_SERVER['REQUEST_METHOD'];

// CORS preflight
if ($method === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    http_response_code(200); exit;
}

$carId = (int)($_GET['id'] ?? 0);
if ($carId <= 0) {
    jsonResponse(['error' => 'معرّف السيارة غير صحيح'], 400);
}

$db = getDB();

// ============================================================
// GET: جلب تفاصيل سيارة
// ============================================================
if ($method === 'GET') {
    $stmt = $db->prepare("SELECT * FROM `cars` WHERE `رقم_السجل` = ?");
    $stmt->execute([$carId]);
    $car = $stmt->fetch();
    if (!$car) {
        jsonResponse(['error' => 'السيارة غير موجودة'], 404);
    }
    jsonResponse($car);
}

// ============================================================
// PUT: تعديل بيانات سيارة
// ============================================================
if ($method === 'PUT') {
    $stmt = $db->prepare("SELECT * FROM `cars` WHERE `رقم_السجل` = ?");
    $stmt->execute([$carId]);
    $existing = $stmt->fetch();
    if (!$existing) {
        jsonResponse(['error' => 'السيارة غير موجودة'], 404);
    }

    $data = getJsonBody();

    $update = $db->prepare("UPDATE `cars` SET
        `اسم_السيارة`    = ?,
        `ماركة_السيارة`  = ?,
        `موديل_السيارة`  = ?,
        `سنة_الصنع`      = ?,
        `السعر_بالدولار` = ?,
        `الممشى_كم`      = ?,
        `ناقل_الحركة`    = ?,
        `نوع_الوقود`     = ?,
        `المدينة`        = ?,
        `اسم_المعرض`     = ?,
        `لون_السيارة`    = ?,
        `حالة_السيارة`   = ?,
        `رقم_الهاتف`     = ?
    WHERE `رقم_السجل` = ?");

    $update->execute([
        $data['اسم_السيارة']    ?? $existing['اسم_السيارة'],
        $data['ماركة_السيارة']  ?? $existing['ماركة_السيارة'],
        $data['موديل_السيارة']  ?? $existing['موديل_السيارة'],
        (int)($data['سنة_الصنع']       ?? $existing['سنة_الصنع']),
        (float)($data['السعر_بالدولار'] ?? $existing['السعر_بالدولار']),
        (float)($data['الممشى_كم']      ?? $existing['الممشى_كم']),
        $data['ناقل_الحركة']    ?? $existing['ناقل_الحركة'],
        $data['نوع_الوقود']     ?? $existing['نوع_الوقود'],
        $data['المدينة']         ?? $existing['المدينة'],
        $data['اسم_المعرض']     ?? $existing['اسم_المعرض'],
        $data['لون_السيارة']    ?? $existing['لون_السيارة'],
        $data['حالة_السيارة']   ?? $existing['حالة_السيارة'],
        $data['رقم_الهاتف']     ?? $existing['رقم_الهاتف'],
        $carId,
    ]);

    jsonResponse(['message' => 'تم تحديث بيانات السيارة بنجاح']);
}

// ============================================================
// DELETE: حذف سيارة
// ============================================================
if ($method === 'DELETE') {
    $stmt = $db->prepare("DELETE FROM `cars` WHERE `رقم_السجل` = ?");
    $stmt->execute([$carId]);
    jsonResponse(['message' => 'تم حذف السيارة من المعرض بنجاح']);
}

jsonResponse(['error' => 'طريقة غير مدعومة'], 405);
