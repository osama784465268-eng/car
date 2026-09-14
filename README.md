# 🚗 معرض السيارات (PHP & MySQL)

تطبيق متكامل لإدارة ومعرض السيارات مُطور بلغة PHP وقواعد بيانات MySQL مخصص للعمل على سيرفر محلي (XAMPP).

---

## 📘 دليل التشغيل للطلاب والمبتدئين
إذا كنت مبتدئاً وتسمع عن XAMPP وقواعد البيانات لأول مرة، قمنا بكتابة دليل شامل ومُبسط خطوة بخطوة باللغة العربية:

👉 **[اضغط هنا لقراءة الدليل التفصيلي للمبتدئين (GUIDE_FOR_BEGINNERS.md)](file:///home/eng/Desktop/so/newcar/GUIDE_FOR_BEGINNERS.md)**

---

## ⚡ التشغيل السريع (للمطورين)
1. **نقل المشروع**: انسخ `newcar/` إلى `C:\xampp\htdocs\newcar\`.
2. **قاعدة البيانات**: أنشئ database باسم `newcar_db` واستورد `sql/setup.sql`.
3. **تفعيل SQLite**: فعّل `extension=pdo_sqlite` في `php.ini` وأعد تشغيل Apache.
4. **الترحيل**: افتح `http://localhost/newcar/migrate/migrate_sqlite_to_mysql.php`.
5. **الموقع**: افتح `http://localhost/newcar/`.
# car
