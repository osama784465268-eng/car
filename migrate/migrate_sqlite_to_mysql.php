<?php
/**
 * سكريبت نقل البيانات من SQLite إلى MySQL
 * Migration Script: SQLite → MySQL
 *
 * ⚠️  شغّل هذا الملف مرة واحدة فقط من المتصفح:
 *     http://localhost/newcar/migrate/migrate_sqlite_to_mysql.php
 *
 * ⚙️  متطلبات:
 *     - تفعيل extension=pdo_sqlite في php.ini الخاص بـ XAMPP
 *     - وجود ملف showroom.db في المسار المحدد أدناه
 */

set_time_limit(300);
ini_set('memory_limit', '512M');

require_once __DIR__ . '/../db/database.php';

// ✏️ عدّل المسار إذا كان ملف .db في مكان مختلف
// الخيار الأول: إذا نقلت الملف داخل مجلد newcar/migrate/
$SQLITE_PATH = __DIR__ . '/showroom.db';

// الخيار الثاني: المسار الأصلي للمشروع (تأكد من التعديل)
if (!file_exists($SQLITE_PATH)) {
    // حاول المسار الجانبي
    $SQLITE_PATH = dirname(__DIR__, 2) . '/car_showroom_python/showroom.db';
}

echo '<meta charset="UTF-8">';
echo '<style>body{font-family:Arial;direction:rtl;padding:20px;background:#111;color:#eee;}
      .ok{color:#4ade80;} .err{color:#f87171;} .info{color:#60a5fa;}
      pre{background:#1e1e2e;padding:15px;border-radius:8px;overflow:auto;}</style>';
echo '<h2>🚀 نقل البيانات من SQLite إلى MySQL</h2>';

// --- التحقق من وجود ملف SQLite ---
if (!file_exists($SQLITE_PATH)) {
    echo '<p class="err">❌ لم يتم العثور على ملف showroom.db في: ' . htmlspecialchars($SQLITE_PATH) . '</p>';
    echo '<p class="info">📁 يرجى نسخ ملف showroom.db إلى مجلد migrate/ أو تعديل المسار في الكود.</p>';
    exit;
}

echo '<p class="ok">✅ تم العثور على: ' . htmlspecialchars($SQLITE_PATH) . '</p>';

// --- الاتصال بـ SQLite ---
try {
    $sqlite = new PDO('sqlite:' . $SQLITE_PATH);
    $sqlite->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo '<p class="ok">✅ تم الاتصال بقاعدة بيانات SQLite</p>';
} catch (PDOException $e) {
    echo '<p class="err">❌ خطأ SQLite: ' . $e->getMessage() . '</p>';
    echo '<p class="info">💡 تأكد من تفعيل extension=pdo_sqlite في php.ini</p>';
    exit;
}

// --- الاتصال بـ MySQL ---
$mysql = getDB();
echo '<p class="ok">✅ تم الاتصال بقاعدة بيانات MySQL</p>';

// ========================================
// نقل جدول السيارات
// ========================================
echo '<h3>📦 نقل جدول السيارات...</h3>';

// حذف البيانات القديمة إذا وُجدت
$mysql->exec("TRUNCATE TABLE `cars`");

$cars = $sqlite->query("SELECT * FROM cars")->fetchAll(PDO::FETCH_ASSOC);
$totalCars = count($cars);
echo "<p class=\"info\">📊 إجمالي السيارات في SQLite: <strong>$totalCars</strong></p>";

if ($totalCars > 0) {
    // الحصول على أسماء الأعمدة من أول صف
    $cols = array_keys($cars[0]);
    $colList = implode('`, `', $cols);
    $placeholders = implode(', ', array_fill(0, count($cols), '?'));

    $stmt = $mysql->prepare("INSERT INTO `cars` (`$colList`) VALUES ($placeholders)");

    $inserted = 0;
    $errors   = 0;

    $mysql->beginTransaction();
    foreach ($cars as $row) {
        try {
            $stmt->execute(array_values($row));
            $inserted++;
        } catch (PDOException $e) {
            $errors++;
            if ($errors <= 5) {
                echo "<p class=\"err\">⚠️ خطأ في السطر $inserted: " . $e->getMessage() . "</p>";
            }
        }
    }
    $mysql->commit();

    echo "<p class=\"ok\">✅ تم إدراج: <strong>$inserted</strong> سيارة</p>";
    if ($errors > 0) {
        echo "<p class=\"err\">⚠️ فشل: <strong>$errors</strong> صف</p>";
    }
} else {
    echo '<p class="info">ℹ️ جدول السيارات فارغ</p>';
}

// ========================================
// نقل جدول الحجوزات
// ========================================
echo '<h3>📅 نقل جدول الحجوزات...</h3>';

try {
    $mysql->exec("TRUNCATE TABLE `bookings`");
    $bookings = $sqlite->query("SELECT * FROM bookings")->fetchAll(PDO::FETCH_ASSOC);
    $totalBookings = count($bookings);
    echo "<p class=\"info\">📊 إجمالي الحجوزات: <strong>$totalBookings</strong></p>";

    if ($totalBookings > 0) {
        $cols = array_keys($bookings[0]);
        $colList = implode('`, `', $cols);
        $placeholders = implode(', ', array_fill(0, count($cols), '?'));
        $stmt = $mysql->prepare("INSERT INTO `bookings` (`$colList`) VALUES ($placeholders)");

        $inserted = 0;
        $mysql->beginTransaction();
        foreach ($bookings as $row) {
            $stmt->execute(array_values($row));
            $inserted++;
        }
        $mysql->commit();
        echo "<p class=\"ok\">✅ تم إدراج: <strong>$inserted</strong> حجز</p>";
    } else {
        echo '<p class="info">ℹ️ جدول الحجوزات فارغ (سيتم إنشاء بيانات تجريبية)</p>';
        // إضافة بيانات تجريبية
        $mysql->exec("INSERT INTO bookings (car_title, customer_name, customer_phone, preferred_date, notes, status)
                      VALUES ('سيارة تجريبية', 'أحمد محمد', '770000000', CURDATE(), 'ملاحظات تجريبية', 'معلقة')");
        echo '<p class="ok">✅ تم إضافة حجز تجريبي</p>';
    }
} catch (Exception $e) {
    echo '<p class="err">⚠️ خطأ في الحجوزات: ' . $e->getMessage() . '</p>';
}

echo '<hr>';
echo '<h3 class="ok">🎉 اكتملت عملية النقل!</h3>';
echo '<p>يمكنك الآن الانتقال إلى: <a href="/newcar/" style="color:#60a5fa">http://localhost/newcar/</a></p>';
echo '<p class="err">⚠️ تحذير أمني: احذف هذا الملف بعد الانتهاء من عملية النقل!</p>';
