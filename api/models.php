<?php
/**
 * API: الموديلات حسب الماركة
 * GET /newcar/api/models.php?brand=تويوتا
 */

require_once __DIR__ . '/../db/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    http_response_code(200); exit;
}

$db    = getDB();
$brand = trim($_GET['brand'] ?? '');

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

jsonResponse(['models' => array_values($models)]);
