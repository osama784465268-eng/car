<?php
/**
 * اتصال قاعدة البيانات - معرض السيارات
 * Database Connection using PDO with Arabic UTF-8 support
 * يدعم قاعدة البيانات المضمنة SQLite (داخل المستودع) وقاعدة البيانات السحابية MySQL
 */

/**
 * جلب إعدادات قاعدة البيانات ديناميكياً من بيئة التشغيل (Railway / XAMPP)
 */
function getDBConfig(): array {
    // 1. التحقق من رابط الاتصال الكامل (MYSQL_URL / DATABASE_URL / MYSQL_PUBLIC_URL)
    $dbUrl = getenv('MYSQL_URL') ?: getenv('DATABASE_URL') ?: getenv('MYSQL_PUBLIC_URL');
    if ($dbUrl) {
        $parsed = parse_url($dbUrl);
        return [
            'host' => $parsed['host'] ?? 'localhost',
            'port' => $parsed['port'] ?? 3306,
            'name' => ltrim($parsed['path'] ?? '/newcar_db', '/'),
            'user' => $parsed['user'] ?? 'root',
            'pass' => $parsed['pass'] ?? '',
        ];
    }

    // 2. التحقق من المتغيرات المنفصلة (Railway / XAMPP)
    $host = getenv('MYSQLHOST') ?: getenv('MYSQL_HOST') ?: getenv('DB_HOST') ?: 'localhost';
    $port = getenv('MYSQLPORT') ?: getenv('MYSQL_PORT') ?: getenv('DB_PORT') ?: '3306';
    $name = getenv('MYSQLDATABASE') ?: getenv('MYSQL_DATABASE') ?: getenv('DB_NAME') ?: 'newcar_db';
    $user = getenv('MYSQLUSER') ?: getenv('MYSQL_USER') ?: getenv('DB_USER') ?: 'root';
    $pass = getenv('MYSQLPASSWORD') ?: getenv('MYSQL_PASSWORD') ?: getenv('DB_PASS') ?: '';

    return [
        'host' => $host,
        'port' => $port,
        'name' => $name,
        'user' => $user,
        'pass' => $pass,
    ];
}

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $sqliteFile = __DIR__ . '/newcar.sqlite';
        $hasMysqlEnv = (bool)(getenv('MYSQLHOST') ?: getenv('MYSQL_HOST') ?: getenv('MYSQL_URL') ?: getenv('DATABASE_URL'));
        $dbType = strtolower(getenv('DB_TYPE') ?: '');

        // استخدام SQLite المضمنة في المستودع افتراضياً (أو إذا تم تحديد DB_TYPE=sqlite أو عدم وجود سيرفر MySQL)
        if (($dbType === 'sqlite' || file_exists($sqliteFile) || !$hasMysqlEnv) && $dbType !== 'mysql') {
            try {
                $pdo = new PDO('sqlite:' . $sqliteFile);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                autoInitializeSQLiteDatabase($pdo);
                return $pdo;
            } catch (PDOException $e) {
                if ($dbType === 'sqlite' || !$hasMysqlEnv) {
                    http_response_code(500);
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode(['error' => 'خطأ في الاتصال بقاعدة بيانات SQLite: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
                    exit;
                }
            }
        }

        // الاتصال بـ MySQL
        $config = getDBConfig();
        $dsn = "mysql:host=" . $config['host'] . ";port=" . $config['port'] . ";dbname=" . $config['name'] . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
        ];
        try {
            $pdo = new PDO($dsn, $config['user'], $config['pass'], $options);
            autoInitializeDatabase($pdo);
        } catch (PDOException $e) {
            http_response_code(500);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'error' => 'خطأ في الاتصال بقاعدة البيانات: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }
    return $pdo;
}

/**
 * تهيئة SQLite تلقائياً إذا لم تكن الجداول موجودة
 */
function autoInitializeSQLiteDatabase(PDO $pdo): void {
    try {
        $check = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='cars'");
        if (!$check || !$check->fetch()) {
            $sqlFile = __DIR__ . '/../sql/setup_sqlite.sql';
            if (file_exists($sqlFile)) {
                $sql = file_get_contents($sqlFile);
                $pdo->exec($sql);
            }
            autoSeedCarsData($pdo);
        }
    } catch (Exception $e) {
        error_log("SQLite auto init error: " . $e->getMessage());
    }
}

/**
 * إنشاء الجداول واستيراد البيانات تلقائياً عند التشغيل الأول في MySQL
 */
function autoInitializeDatabase(PDO $pdo): void {
    try {
        $check = $pdo->query("SHOW TABLES LIKE 'cars'");
        if ($check->rowCount() === 0) {
            $sqlFile = __DIR__ . '/../sql/setup.sql';
            if (file_exists($sqlFile)) {
                $sql = file_get_contents($sqlFile);
                $sql = preg_replace('/CREATE DATABASE.*?;/is', '', $sql);
                $sql = preg_replace('/USE `.*?`;/is', '', $sql);
                $pdo->exec($sql);
            }
        }

        $countQuery = $pdo->query("SELECT COUNT(*) as cnt FROM cars");
        $row = $countQuery->fetch();
        if ($row && intval($row['cnt']) === 0) {
            autoSeedCarsData($pdo);
        }
    } catch (Exception $e) {
        error_log("Auto init error: " . $e->getMessage());
    }
}

/**
 * استيراد بيانات السيارات من CSV تلقائياً عند أول تشغيل
 */
function autoSeedCarsData(PDO $pdo): void {
    $csvFile = __DIR__ . '/../cleaned_used_cars.csv';
    if (!file_exists($csvFile)) return;

    $handle = fopen($csvFile, 'r');
    if ($handle === false) return;

    fgetcsv($handle, 0, ","); // تخطي صف الهيدر

    $sql = "INSERT INTO cars (
        `رقم_السجل`, `اسم_السيارة`, `ماركة_السيارة`, `موديل_السيارة`,
        `سنة_الصنع`, `السعر_بالدولار`, `الممشى_كم`, `ناقل_الحركة`,
        `نوع_الوقود`, `المدينة`, `اسم_المعرض`, `لون_السيارة`,
        `حالة_السيارة`, `رقم_الهاتف`
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);

    $pdo->beginTransaction();
    $inserted = 0;
    while (($row = fgetcsv($handle, 0, ",")) !== false) {
        if (count($row) < 14) continue;
        try {
            $stmt->execute([
                trim($row[0]), trim($row[2]), trim($row[3]), trim($row[4]),
                trim($row[5]), str_replace(',', '', trim($row[6])), str_replace(',', '', trim($row[7])),
                trim($row[9]), trim($row[10]), trim($row[13]), trim($row[1]),
                trim($row[8]), trim($row[11]), trim($row[12])
            ]);
            $inserted++;
            if ($inserted % 500 === 0) {
                $pdo->commit();
                $pdo->beginTransaction();
            }
        } catch (Exception $e) {
            // تجاهل الصفوف المكررة أو الأخطاء الفردية
        }
    }
    if ($pdo->inTransaction()) {
        $pdo->commit();
    }
    fclose($handle);
}

/**
 * إرجاع استجابة JSON
 */
function jsonResponse(array $data, int $code = 200): void {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
    exit;
}

/**
 * قراءة body الطلب JSON
 */
function getJsonBody(): array {
    $raw = file_get_contents('php://input');
    return json_decode($raw, true) ?? [];
}
