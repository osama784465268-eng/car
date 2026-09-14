-- ============================================================
-- إنشاء قاعدة بيانات معرض السيارات على MySQL / XAMPP
-- Car Showroom Database Setup
-- ترميز: UTF-8mb4 لدعم العربية الكاملة
-- ============================================================

-- CREATE DATABASE IF NOT EXISTS `newcar_db`
--   CHARACTER SET utf8mb4
--   COLLATE utf8mb4_unicode_ci;

-- USE `newcar_db`;

-- -------------------------------------------------------
-- جدول السيارات
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cars` (
    `رقم_السجل`      INT             NOT NULL AUTO_INCREMENT,
    `اسم_السيارة`    VARCHAR(255)    DEFAULT NULL,
    `ماركة_السيارة`  VARCHAR(100)    DEFAULT NULL,
    `موديل_السيارة`  VARCHAR(100)    DEFAULT NULL,
    `سنة_الصنع`      INT             DEFAULT NULL,
    `السعر_بالدولار` DECIMAL(15,2)   DEFAULT NULL,
    `الممشى_كم`      DECIMAL(15,2)   DEFAULT NULL,
    `ناقل_الحركة`    VARCHAR(50)     DEFAULT NULL,
    `نوع_الوقود`     VARCHAR(50)     DEFAULT NULL,
    `المدينة`        VARCHAR(100)    DEFAULT NULL,
    `اسم_المعرض`     VARCHAR(255)    DEFAULT NULL,
    `لون_السيارة`    VARCHAR(50)     DEFAULT NULL,
    `حالة_السيارة`   VARCHAR(100)    DEFAULT NULL,
    `رقم_الهاتف`     VARCHAR(20)     DEFAULT NULL,
    PRIMARY KEY (`رقم_السجل`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------
-- جدول الحجوزات / طلبات المعاينة
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS `bookings` (
    `id`              INT          NOT NULL AUTO_INCREMENT,
    `car_id`          INT          DEFAULT NULL,
    `car_title`       VARCHAR(255) DEFAULT NULL,
    `customer_name`   VARCHAR(100) DEFAULT NULL,
    `customer_phone`  VARCHAR(30)  DEFAULT NULL,
    `preferred_date`  DATE         DEFAULT NULL,
    `notes`           TEXT         DEFAULT NULL,
    `status`          VARCHAR(30)  DEFAULT 'معلقة',
    `created_at`      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;
