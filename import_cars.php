<?php


set_time_limit(0);
ini_set('memory_limit', '512M');

require_once __DIR__ . '/db/database.php';


require_once __DIR__ . '/db/database.php';

$csvFile = __DIR__ . '/cleaned_used_cars.csv';

if (!file_exists($csvFile)) {
    die("ملف cleaned_used_cars.csv غير موجود.");
}

$db = getDB();

$handle = fopen($csvFile, 'r');

if ($handle === false) {
    die("تعذر فتح ملف البيانات.");
}

// قراءة أول سطر: أسماء الأعمدة
$headers = fgetcsv($handle, 0, ",");

if ($headers === false) {
    die("ملف البيانات فارغ.");
}

$sql = "
    INSERT INTO cars (
        `رقم_السجل`,
        `اسم_السيارة`,
        `ماركة_السيارة`,
        `موديل_السيارة`,
        `سنة_الصنع`,
        `السعر_بالدولار`,
        `الممشى_كم`,
        `ناقل_الحركة`,
        `نوع_الوقود`,
        `المدينة`,
        `اسم_المعرض`,
        `لون_السيارة`,
        `حالة_السيارة`,
        `رقم_الهاتف`
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
";

$stmt = $db->prepare($sql);

$count = 0;
$errors = 0;

while (($row = fgetcsv($handle, 0, ",")) !== false) {

    // تجاهل الصفوف الفارغة
    if (count($row) < 14) {
        continue;
    }

    $id          = trim($row[0]);
    $showroom    = trim($row[1]);
    $carName     = trim($row[2]);
    $brand       = trim($row[3]);
    $model       = trim($row[4]);
    $year        = trim($row[5]);

    // إزالة الفواصل من السعر والممشى
    $price       = str_replace(',', '', trim($row[6]));
    $mileage     = str_replace(',', '', trim($row[7]));

    $color       = trim($row[8]);
    $transmission = trim($row[9]);
    $fuel        = trim($row[10]);
    $condition   = trim($row[11]);
    $phone       = trim($row[12]);
    $city        = trim($row[13]);

    try {

        $stmt->execute([
            $id,
            $carName,
            $brand,
            $model,
            $year,
            $price,
            $mileage,
            $transmission,
            $fuel,
            $city,
            $showroom,
            $color,
            $condition,
            $phone
        ]);

        $count++;

    } catch (PDOException $e) {

        $errors++;

    }
}

fclose($handle);

echo "<!DOCTYPE html>";
echo "<html lang='ar' dir='rtl'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<title>استيراد السيارات</title>";
echo "<style>
body {
    font-family: Arial;
    text-align: center;
    padding: 50px;
    background: #f5f5f5;
}
.box {
    background: white;
    padding: 30px;
    border-radius: 15px;
    max-width: 600px;
    margin: auto;
    box-shadow: 0 5px 20px rgba(0,0,0,.1);
}
.success {
    color: green;
    font-size: 22px;
}
.error {
    color: red;
}
a {
    display: inline-block;
    margin-top: 20px;
    padding: 12px 25px;
    background: #222;
    color: white;
    text-decoration: none;
    border-radius: 8px;
}
</style>";
echo "</head>";
echo "<body>";

echo "<div class='box'>";

echo "<h2>استيراد بيانات السيارات</h2>";

echo "<p class='success'>تم إدخال: " . number_format($count) . " سيارة</p>";

if ($errors > 0) {
    echo "<p class='error'>عدد الصفوف التي حدث فيها خطأ: " . number_format($errors) . "</p>";
}

echo "<a href='index.php'>العودة إلى المعرض</a>";

echo "</div>";

echo "</body>";
echo "</html>";
