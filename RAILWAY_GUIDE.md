# 🚀 دليل رفع المشروع على Railway و GitHub

تم تجهيز المشروع بالكامل ليعمل بشكل سليم وتلقائي عند رفعه على استضافة **Railway** وربطه بـ **GitHub**.

---

## ⚡ مميزات التجهيز التلقائي
1. **ربط تلقائي لقاعدة البيانات (MySQL)**: يتعرف المشروع تلقائياً على بيانات الاتصال المقدمة من Railway (`MYSQLHOST`, `MYSQLUSER`, `MYSQLPASSWORD`, `MYSQLDATABASE`, `MYSQLPORT`, أو `MYSQL_URL`).
2. **إنشاء الجداول تلقائياً**: عند أول تشغيل للموقع، سيقوم الكود بإنشاء جدول السيارات (`cars`) وجدول الحجوزات (`bookings`) بدون الحاجة لاستيراد SQL يدوياً.
3. **تعبئة البيانات تلقائياً**: سيقوم الكود بقراءة ملف `cleaned_used_cars.csv` وتغذية قاعدة البيانات ببيانات السيارات تلقائياً عند أول تشغيل.
4. **دعم بيئة Docker**: تم إضافة ملف `Dockerfile` مجهز بـ Apache و PHP PDO MySQL ومعالجة المنافذ الديناميكية.

---

## 📋 الخطوات خطوة بخطوة للرفع على Railway

### 1️⃣ رفع المشروع على GitHub
1. افتح حسابك على [GitHub.com](https://github.com) وأنشئ **Repository جديد** باسم `car-showroom` أو أي اسم تختاره.
2. ارفع محتويات المشروع (ملفات مجلد `newcar`) إلى هذا المستودع (Repository).

### 2️⃣ إنشاء المشروع على Railway
1. اذهب إلى منصة [Railway.app](https://railway.app) وسجل دخولك عبر حساب GitHub.
2. اضغط على زر **New Project**، ثم اختر **Deploy from GitHub repo**.
3. اختر مستودع المشروع الخاص بك (`car-showroom`).
4. سيبدأ Railway بالتعرف على ملف `Dockerfile` وبناء التطبيق.

### 3️⃣ إضافة قاعدة بيانات MySQL في Railway
1. في لوحة تحكم المشروع على Railway (Canvas)، اضغط على **Create** أو زر `+` ثم اختر **Database** -> **Add MySQL**.
2. سيتم إضافة خدمة MySQL إلى مشروعك.
3. قم بربط خدمة MySQL بـ تطبيق الـ PHP (أو يمكنك ببساطة سحب وإفلات Connector بين الخدمتين في اللوحة، أو استخدام Railway Variables حيث يشارك Railway المتغيرات تلقائياً عبر Reference).
4. تأكد في إعدادات خدمة الـ PHP (تطبيقاك) تحت تبويب **Variables** من وجود متغيرات الاتصال (`MYSQLHOST`, `MYSQLUSER`, `MYSQLPASSWORD`, `MYSQLDATABASE`, `MYSQLPORT`) أو إضافة **Shared Variables** من خدمة MySQL.

### 4️⃣ توليد الرابط التجاري للموقع (Public Networking Domain)
1. في لوحة Railway، اضغط على خدمة التطبيق (الويب).
2. اذهب إلى تبويب **Settings**.
3. تحت قسم **Networking** -> **Public Networking**، اضغط على **Generate Domain**.
4. سيظهر لك رابط مخصص (مثل: `https://car-showroom-production.up.railway.app`).
5. افتح الرابط وسيظهر موقعك يعمل بكفاءة وبكامل بيانات السيارات! 🎉

---

## 🔧 التطوير المحلي (XAMPP)
المشروع ما يزال يعمل محلياً 100% على XAMPP دون أي تعديلات، فقط افتح `localhost/newcar/` وسيستخدم الإعدادات الافتراضية المحلية تلقائياً.
