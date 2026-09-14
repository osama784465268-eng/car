<?php
/**
 * API: فلاتر البحث
 * GET /newcar/api/filters.php
 * يعيد: الماركات، الموديلات، المدن، الوقود، ناقل الحركة، المعارض، نطاقات الأسعار
 */

require_once __DIR__ . '/../db/database.php';

// Handle CORS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    http_response_code(200);
    exit;
}

$db     = getDB();
$brand  = trim($_GET['brand'] ?? '');

// الماركات
$brands = $db->query("SELECT DISTINCT `ماركة_السيارة` FROM `cars`
    WHERE `ماركة_السيارة` IS NOT NULL AND `ماركة_السيارة` != ''
    ORDER BY `ماركة_السيارة`")->fetchAll(PDO::FETCH_COLUMN);

// الموديلات (بحسب الماركة إذا تم تحديدها)
if ($brand !== '') {
    $stmt = $db->prepare("SELECT DISTINCT `موديل_السيارة` FROM `cars`
        WHERE `ماركة_السيارة` = ? AND `موديل_السيارة` IS NOT NULL AND `موديل_السيارة` != ''
        ORDER BY `موديل_السيارة`");
    $stmt->execute([$brand]);
} else {
    $stmt = $db->query("SELECT DISTINCT `موديل_السيارة` FROM `cars`
        WHERE `موديل_السيارة` IS NOT NULL AND `موديل_السيارة` != ''
        ORDER BY `موديل_السيارة`");
}
$models = $stmt->fetchAll(PDO::FETCH_COLUMN);

// المدن
$cities = $db->query("SELECT DISTINCT `المدينة` FROM `cars`
    WHERE `المدينة` IS NOT NULL AND `المدينة` != ''
    ORDER BY `المدينة`")->fetchAll(PDO::FETCH_COLUMN);

// أنواع الوقود
$fuels = $db->query("SELECT DISTINCT `نوع_الوقود` FROM `cars`
    WHERE `نوع_الوقود` IS NOT NULL AND `نوع_الوقود` != ''
    ORDER BY `نوع_الوقود`")->fetchAll(PDO::FETCH_COLUMN);

// ناقل الحركة
$transmissions = $db->query("SELECT DISTINCT `ناقل_الحركة` FROM `cars`
    WHERE `ناقل_الحركة` IS NOT NULL AND `ناقل_الحركة` != ''
    ORDER BY `ناقل_الحركة`")->fetchAll(PDO::FETCH_COLUMN);

// المعارض
$showrooms = $db->query("SELECT DISTINCT `اسم_المعرض` FROM `cars`
    WHERE `اسم_المعرض` IS NOT NULL AND `اسم_المعرض` != ''
    ORDER BY `اسم_المعرض`")->fetchAll(PDO::FETCH_COLUMN);

// نطاقات الأسعار والسنة والممشى
$price = $db->query("SELECT MIN(`السعر_بالدولار`), MAX(`السعر_بالدولار`) FROM `cars`")->fetch(PDO::FETCH_NUM);
$year  = $db->query("SELECT MIN(`سنة_الصنع`),      MAX(`سنة_الصنع`)      FROM `cars`")->fetch(PDO::FETCH_NUM);
$km    = $db->query("SELECT MIN(`الممشى_كم`),       MAX(`الممشى_كم`)       FROM `cars`")->fetch(PDO::FETCH_NUM);

jsonResponse([
    'brands'        => array_values($brands),
    'models'        => array_values($models),
    'cities'        => array_values($cities),
    'fuels'         => array_values($fuels),
    'transmissions' => array_values($transmissions),
    'showrooms'     => array_values($showrooms),
    'min_price'     => (float)($price[0] ?? 0),
    'max_price'     => (float)($price[1] ?? 100000),
    'min_year'      => (int)($year[0] ?? 2000),
    'max_year'      => (int)($year[1] ?? 2026),
    'min_km'        => (float)($km[0] ?? 0),
    'max_km'        => (float)($km[1] ?? 500000),
]);
