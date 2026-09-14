-- SQLite Schema for Car Showroom Application
CREATE TABLE IF NOT EXISTS cars (
    `رقم_السجل`      INTEGER PRIMARY KEY AUTOINCREMENT,
    `اسم_السيارة`    TEXT,
    `ماركة_السيارة`  TEXT,
    `موديل_السيارة`  TEXT,
    `سنة_الصنع`      INTEGER,
    `السعر_بالدولار` REAL,
    `الممشى_كم`      REAL,
    `ناقل_الحركة`    TEXT,
    `نوع_الوقود`     TEXT,
    `المدينة`        TEXT,
    `اسم_المعرض`     TEXT,
    `لون_السيارة`    TEXT,
    `حالة_السيارة`   TEXT,
    `رقم_الهاتف`     TEXT
);

CREATE TABLE IF NOT EXISTS bookings (
    `id`              INTEGER PRIMARY KEY AUTOINCREMENT,
    `car_id`          INTEGER,
    `car_title`       TEXT,
    `customer_name`   TEXT,
    `customer_phone`  TEXT,
    `preferred_date`  TEXT,
    `notes`           TEXT,
    `status`          TEXT DEFAULT 'معلقة',
    `created_at`      DATETIME DEFAULT CURRENT_TIMESTAMP
);
