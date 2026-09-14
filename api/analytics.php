<?php
/**
 * API: الإحصائيات والتحليلات
 * GET /newcar/api/analytics.php
 */

require_once __DIR__ . '/../db/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    http_response_code(200); exit;
}

$db = getDB();

// ─── إحصائيات أساسية ───────────────────────────────────────
$total_cars      = (int)$db->query("SELECT COUNT(*) FROM `cars`")->fetchColumn();
$avg_price       = (float)($db->query("SELECT AVG(`السعر_بالدولار`) FROM `cars`")->fetchColumn() ?? 0);
$total_valuation = (float)($db->query("SELECT SUM(`السعر_بالدولار`) FROM `cars`")->fetchColumn() ?? 0);
$avg_km          = (float)($db->query("SELECT AVG(`الممشى_كم`) FROM `cars`")->fetchColumn() ?? 0);
$total_showrooms = (int)$db->query("SELECT COUNT(DISTINCT `اسم_المعرض`) FROM `cars`")->fetchColumn();

// ─── إحصائيات الحجوزات ─────────────────────────────────────
$total_bookings   = (int)$db->query("SELECT COUNT(*) FROM `bookings`")->fetchColumn();
$pending_bookings = (int)$db->query("SELECT COUNT(*) FROM `bookings` WHERE `status` = 'معلقة'")->fetchColumn();

// ─── أكثر الماركات ─────────────────────────────────────────
$top_brands_rows = $db->query("SELECT `ماركة_السيارة`, COUNT(*) AS cnt FROM `cars`
    GROUP BY `ماركة_السيارة` ORDER BY cnt DESC LIMIT 7")->fetchAll();
$top_brands = array_map(fn($r) => ['brand' => $r['ماركة_السيارة'], 'count' => (int)$r['cnt']], $top_brands_rows);

// ─── أكثر المدن ───────────────────────────────────────────
$top_cities_rows = $db->query("SELECT `المدينة`, COUNT(*) AS cnt FROM `cars`
    GROUP BY `المدينة` ORDER BY cnt DESC LIMIT 7")->fetchAll();
$top_cities = array_map(fn($r) => ['city' => $r['المدينة'], 'count' => (int)$r['cnt']], $top_cities_rows);

// ─── متوسط الأسعار حسب السنة ──────────────────────────────
$year_price_rows = $db->query("SELECT `سنة_الصنع`, ROUND(AVG(`السعر_بالدولار`), 2) AS avg_p FROM `cars`
    GROUP BY `سنة_الصنع` ORDER BY `سنة_الصنع` ASC")->fetchAll();
$year_prices = array_map(fn($r) => ['year' => (int)$r['سنة_الصنع'], 'avg_price' => (float)$r['avg_p']], $year_price_rows);

// ─── توزيع الوقود ─────────────────────────────────────────
$fuel_rows = $db->query("SELECT `نوع_الوقود`, COUNT(*) AS cnt FROM `cars`
    GROUP BY `نوع_الوقود` ORDER BY cnt DESC")->fetchAll();
$fuel_dist = array_map(fn($r) => ['fuel' => $r['نوع_الوقود'], 'count' => (int)$r['cnt']], $fuel_rows);

// ─── توزيع ناقل الحركة ────────────────────────────────────
$trans_rows = $db->query("SELECT `ناقل_الحركة`, COUNT(*) AS cnt FROM `cars`
    GROUP BY `ناقل_الحركة` ORDER BY cnt DESC")->fetchAll();
$trans_dist = array_map(fn($r) => ['transmission' => $r['ناقل_الحركة'], 'count' => (int)$r['cnt']], $trans_rows);

jsonResponse([
    'total_cars'       => $total_cars,
    'avg_price'        => round($avg_price, 2),
    'total_valuation'  => round($total_valuation, 2),
    'avg_km'           => round($avg_km, 2),
    'total_showrooms'  => $total_showrooms,
    'total_bookings'   => $total_bookings,
    'pending_bookings' => $pending_bookings,
    'top_brands'       => $top_brands,
    'top_cities'       => $top_cities,
    'year_prices'      => $year_prices,
    'fuel_dist'        => $fuel_dist,
    'trans_dist'       => $trans_dist,
]);
