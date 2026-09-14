<?php
/**
 * API: السيارات - GET (قائمة مع فلترة وترقيم) + POST (إضافة)
 * GET  /newcar/api/cars.php
 * POST /newcar/api/cars.php
 */

require_once __DIR__ . '/../db/database.php';

$method = $_SERVER['REQUEST_METHOD'];

// CORS preflight
if ($method === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    http_response_code(200); exit;
}

// ============================================================
// POST: إضافة سيارة جديدة
// ============================================================
if ($method === 'POST') {
    $data = getJsonBody();

    $name         = trim($data['اسم_السيارة']    ?? '');
    $brand        = trim($data['ماركة_السيارة']   ?? '');
    $model        = trim($data['موديل_السيارة']   ?? '');
    $year         = (int)($data['سنة_الصنع']       ?? date('Y'));
    $price        = (float)($data['السعر_بالدولار'] ?? 0);
    $km           = (float)($data['الممشى_كم']      ?? 0);
    $transmission = trim($data['ناقل_الحركة']    ?? 'أوتوماتيك');
    $fuel         = trim($data['نوع_الوقود']      ?? 'بنزين');
    $city         = trim($data['المدينة']          ?? 'صنعاء');
    $showroom     = trim($data['اسم_المعرض']      ?? 'معرض المتميز');
    $color        = trim($data['لون_السيارة']     ?? 'أسود');
    $condition    = trim($data['حالة_السيارة']    ?? 'مستعمل ممتاز');
    $phone        = trim($data['رقم_الهاتف']      ?? '770000000');

    if ($name === '' || $brand === '') {
        jsonResponse(['error' => 'يرجى تعبئة اسم السيارة والماركة على الأقل'], 400);
    }

    $db = getDB();
    $stmt = $db->prepare("INSERT INTO `cars`
        (`اسم_السيارة`, `ماركة_السيارة`, `موديل_السيارة`, `سنة_الصنع`,
         `السعر_بالدولار`, `الممشى_كم`, `ناقل_الحركة`, `نوع_الوقود`,
         `المدينة`, `اسم_المعرض`, `لون_السيارة`, `حالة_السيارة`, `رقم_الهاتف`)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->execute([$name, $brand, $model, $year, $price, $km,
                    $transmission, $fuel, $city, $showroom, $color, $condition, $phone]);
    $newId = $db->lastInsertId();

    jsonResponse(['message' => 'تم إضافة السيارة بنجاح', 'car_id' => (int)$newId], 201);
}

// ============================================================
// GET: جلب السيارات مع الفلترة والترقيم والترتيب
// ============================================================
if ($method === 'GET') {
    $page   = max(1, (int)($_GET['page']  ?? 1));
    $limit  = max(1, (int)($_GET['limit'] ?? 12));
    $offset = ($page - 1) * $limit;

    $search       = trim($_GET['search']       ?? '');
    $brand        = trim($_GET['brand']        ?? '');
    $model        = trim($_GET['model']        ?? '');
    $city         = trim($_GET['city']         ?? '');
    $fuel         = trim($_GET['fuel']         ?? '');
    $transmission = trim($_GET['transmission'] ?? '');
    $showroom     = trim($_GET['showroom']     ?? '');
    $sort_by      = trim($_GET['sort_by']      ?? 'default');

    $min_price = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? (float)$_GET['min_price'] : null;
    $max_price = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? (float)$_GET['max_price'] : null;
    $min_year  = isset($_GET['min_year'])  && $_GET['min_year']  !== '' ? (int)$_GET['min_year']   : null;
    $max_year  = isset($_GET['max_year'])  && $_GET['max_year']  !== '' ? (int)$_GET['max_year']   : null;
    $min_km    = isset($_GET['min_km'])    && $_GET['min_km']    !== '' ? (float)$_GET['min_km']   : null;
    $max_km    = isset($_GET['max_km'])    && $_GET['max_km']    !== '' ? (float)$_GET['max_km']   : null;

    $where  = " WHERE 1=1 ";
    $params = [];

    // بحث متعدد الكلمات
    if ($search !== '') {
        foreach (explode(' ', $search) as $word) {
            if ($word === '') continue;
            $where .= " AND (`اسم_السيارة` LIKE ? OR `ماركة_السيارة` LIKE ? OR `موديل_السيارة` LIKE ?
                         OR `اسم_المعرض` LIKE ? OR `المدينة` LIKE ?
                         OR CAST(`سنة_الصنع` AS CHAR) LIKE ? OR `لون_السيارة` LIKE ?)";
            $p = "%$word%";
            $params = array_merge($params, [$p, $p, $p, $p, $p, $p, $p]);
        }
    }

    if ($brand        !== '') { $where .= " AND `ماركة_السيارة` = ?";  $params[] = $brand; }
    if ($model        !== '') { $where .= " AND `موديل_السيارة` = ?";  $params[] = $model; }
    if ($city         !== '') { $where .= " AND `المدينة` = ?";         $params[] = $city; }
    if ($fuel         !== '') { $where .= " AND `نوع_الوقود` = ?";     $params[] = $fuel; }
    if ($transmission !== '') { $where .= " AND `ناقل_الحركة` = ?";   $params[] = $transmission; }
    if ($showroom     !== '') { $where .= " AND `اسم_المعرض` = ?";     $params[] = $showroom; }

    if ($min_price !== null) { $where .= " AND `السعر_بالدولار` >= ?"; $params[] = $min_price; }
    if ($max_price !== null) { $where .= " AND `السعر_بالدولار` <= ?"; $params[] = $max_price; }
    if ($min_year  !== null) { $where .= " AND `سنة_الصنع` >= ?";      $params[] = $min_year; }
    if ($max_year  !== null) { $where .= " AND `سنة_الصنع` <= ?";      $params[] = $max_year; }
    if ($min_km    !== null) { $where .= " AND `الممشى_كم` >= ?";       $params[] = $min_km; }
    if ($max_km    !== null) { $where .= " AND `الممشى_كم` <= ?";       $params[] = $max_km; }

    // الترتيب
    $orderMap = [
        'price_asc'  => " ORDER BY `السعر_بالدولار` ASC",
        'price_desc' => " ORDER BY `السعر_بالدولار` DESC",
        'year_desc'  => " ORDER BY `سنة_الصنع` DESC",
        'year_asc'   => " ORDER BY `سنة_الصنع` ASC",
        'km_asc'     => " ORDER BY `الممشى_كم` ASC",
        'km_desc'    => " ORDER BY `الممشى_كم` DESC",
        'default'    => " ORDER BY `رقم_السجل` DESC",
    ];
    $order = $orderMap[$sort_by] ?? $orderMap['default'];

    $db = getDB();

    // عد الإجمالي
    $countStmt = $db->prepare("SELECT COUNT(*) FROM `cars`" . $where);
    $countStmt->execute($params);
    $total_count = (int)$countStmt->fetchColumn();

    // جلب الصفحة
    $dataStmt = $db->prepare("SELECT * FROM `cars`" . $where . $order . " LIMIT ? OFFSET ?");
    $dataStmt->execute(array_merge($params, [$limit, $offset]));
    $cars = $dataStmt->fetchAll();

    $total_pages = $total_count > 0 ? (int)ceil($total_count / $limit) : 1;

    jsonResponse([
        'cars'        => $cars,
        'total_count' => $total_count,
        'page'        => $page,
        'total_pages' => $total_pages,
        'limit'       => $limit,
    ]);
}

jsonResponse(['error' => 'طريقة غير مدعومة'], 405);
