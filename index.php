<?php
/**
 * الصفحة الرئيسية - معرض السيارات المتكامل
 * PHP version of the Flask Jinja2 template
 */
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>النظام المتكامل لمعرض السيارات | Executive Car Showroom Platform</title>
    <meta name="description" content="منصة متكاملة وشاملة لإدارة وتصفح ومقارنة أفضل السيارات المستعملة والجديدة مع حاسبة التمويل وطلبات المعاينة ورسومات اتخاذ القرار.">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&family=Outfit:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <!-- Dynamic Ambient Lighting Background -->
    <div class="bg-blur-orb orb-1"></div>
    <div class="bg-blur-orb orb-2"></div>
    <div class="bg-blur-orb orb-3"></div>

    <!-- Header Navigation -->
    <header class="navbar-header">
        <div class="container navbar-container">
            <a href="./" class="brand-logo">
                <div class="logo-icon">
                    <i class="fa-solid fa-car"></i>
                </div>
                <div class="logo-text">
                    <span class="logo-title">النظام<span class="highlight">المتكامل</span></span>
                    <span class="logo-subtitle">لمعارض السيارات الاحترافية</span>
                </div>
            </a>

            <!-- Search Bar -->
            <div class="header-search">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input type="text" id="global-search" placeholder="ابحث باسم الماركة، الموديل، المعرض، المدينة، أو السنة..." autocomplete="off">
                <button type="button" class="btn-clear-search" id="clear-search-btn" title="مسح البحث">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Header Action Controls -->
            <div class="header-actions">
                <button class="btn btn-action-secondary" id="open-add-car-btn" title="إضافة سيارة جديدة للمخزون">
                    <i class="fa-solid fa-plus-circle"></i>
                    <span>إضافة سيارة</span>
                </button>

                <button class="btn btn-action-secondary" id="open-bookings-btn" title="طلبات المعاينة والحجوزات">
                    <i class="fa-solid fa-calendar-check"></i>
                    <span>الطلبات</span>
                    <span class="badge-count hidden" id="bookings-count">0</span>
                </button>

                <button class="btn btn-action-secondary" id="open-compare-btn" title="مقارنة السيارات">
                    <i class="fa-solid fa-scale-balanced"></i>
                    <span>المقارنة</span>
                    <span class="badge-count hidden" id="compare-count">0</span>
                </button>

                <button class="btn btn-action-secondary" id="open-favorites-btn" title="السيارات المفضلة">
                    <i class="fa-solid fa-heart"></i>
                    <span>المفضلة</span>
                    <span class="badge-count hidden" id="fav-count">0</span>
                </button>

                <button class="btn btn-analytics" id="open-analytics-btn" title="فتح نافذة الإحصائيات السريعة">
                    <i class="fa-solid fa-chart-pie"></i>
                    <span>الإحصائيات السريعة</span>
                </button>
            </div>
        </div>
    </header>

    <!-- Main Navigation Pages Switcher Tabs Bar -->
    <div class="pages-tabs-wrapper container">
        <div class="pages-tabs-bar">
            <button class="nav-tab-btn active" id="tab-btn-cars" data-tab="cars">
                <i class="fa-solid fa-car-rear"></i>
                <span>الصفحة الأولى: تصفح السيارات والمخزون</span>
            </button>
            <button class="nav-tab-btn" id="tab-btn-analytics" data-tab="analytics">
                <i class="fa-solid fa-chart-line"></i>
                <span>الصفحة الثانية: لوحة إحصائيات اتخاذ القرار</span>
            </button>
        </div>
    </div>

    <!-- PAGE VIEW 1: CAR SHOWCASE & INVENTORY PAGE -->
    <div id="page-cars" class="page-view-container">
        
        <!-- Hero Banner & Executive Stats -->
        <section class="hero-section">
            <div class="container hero-container">
                <div class="hero-content">
                    <div class="hero-tag">
                        <i class="fa-solid fa-shield-check"></i> منصة شاملة لإدارة وتصفح السيارات بدقة فائقة
                    </div>
                    <h1 class="hero-title">تصفح وقارن بين أفضل السيارات بسهولة وسرعة</h1>
                    <p class="hero-description">نظام متكامل يتيح لك البحث المتقدم، حساب التمويل والأقساط، حجز مواعيد المعاينة، ومقارنة السيارات بدون أي تعقيد.</p>
                    
                    <!-- Counter Stats Bar -->
                    <div class="hero-counters">
                        <div class="counter-card">
                            <span class="counter-num" id="counter-total">--</span>
                            <span class="counter-label">إجمالي السيارات</span>
                        </div>
                        <div class="counter-divider"></div>
                        <div class="counter-card">
                            <span class="counter-num" id="counter-showrooms">--</span>
                            <span class="counter-label">معرض متاح</span>
                        </div>
                        <div class="counter-divider"></div>
                        <div class="counter-card">
                            <span class="counter-num" id="counter-avg-price">$0</span>
                            <span class="counter-label">متوسط السعر</span>
                        </div>
                        <div class="counter-divider"></div>
                        <div class="counter-card">
                            <span class="counter-num" id="counter-valuation">$0</span>
                            <span class="counter-label">قيمة المخزون</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main Content Layout -->
        <main class="main-layout container">
            
            <!-- Sidebar Filters -->
            <aside class="sidebar-filters" id="sidebar-filters">
                <div class="filter-header">
                    <h3><i class="fa-solid fa-sliders"></i> تصفية السيارات</h3>
                    <button type="button" class="btn-reset-filters" id="reset-filters-btn">
                        <i class="fa-solid fa-rotate-left"></i> إعادة ضبط
                    </button>
                </div>

                <!-- Filter: Brand -->
                <div class="filter-group">
                    <label for="filter-brand"><i class="fa-solid fa-copyright"></i> ماركة السيارة</label>
                    <select id="filter-brand" class="form-select">
                        <option value="">جميع الماركات</option>
                    </select>
                </div>

                <!-- Filter: Model -->
                <div class="filter-group">
                    <label for="filter-model"><i class="fa-solid fa-car-side"></i> موديل السيارة</label>
                    <select id="filter-model" class="form-select">
                        <option value="">جميع الموديلات</option>
                    </select>
                </div>

                <!-- Filter: Transmission -->
                <div class="filter-group">
                    <label for="filter-transmission"><i class="fa-solid fa-gears"></i> ناقل الحركة</label>
                    <select id="filter-transmission" class="form-select">
                        <option value="">جميع أنواع الناقل</option>
                    </select>
                </div>

                <!-- Filter: Fuel -->
                <div class="filter-group">
                    <label for="filter-fuel"><i class="fa-solid fa-gas-pump"></i> نوع الوقود</label>
                    <select id="filter-fuel" class="form-select">
                        <option value="">جميع أنواع الوقود</option>
                    </select>
                </div>

                <!-- Filter: City -->
                <div class="filter-group">
                    <label for="filter-city"><i class="fa-solid fa-location-dot"></i> المدينة</label>
                    <select id="filter-city" class="form-select">
                        <option value="">جميع المدن</option>
                    </select>
                </div>

                <!-- Filter: Showroom -->
                <div class="filter-group">
                    <label for="filter-showroom"><i class="fa-solid fa-store"></i> اسم المعرض</label>
                    <select id="filter-showroom" class="form-select">
                        <option value="">جميع المعارض</option>
                    </select>
                </div>

                <!-- Filter: Price Range -->
                <div class="filter-group">
                    <div class="range-header">
                        <label><i class="fa-solid fa-dollar-sign"></i> السعر (بالدولار)</label>
                    </div>
                    <div class="range-inputs">
                        <input type="number" id="min-price-input" placeholder="الأدنى" class="form-input">
                        <input type="number" id="max-price-input" placeholder="الأقصى" class="form-input">
                    </div>
                </div>

                <!-- Filter: Year Range -->
                <div class="filter-group">
                    <div class="range-header">
                        <label><i class="fa-solid fa-calendar-days"></i> سنة الصنع</label>
                    </div>
                    <div class="range-inputs">
                        <input type="number" id="min-year-input" placeholder="من سنة" class="form-input">
                        <input type="number" id="max-year-input" placeholder="إلى سنة" class="form-input">
                    </div>
                </div>

                <!-- Filter: Mileage Range -->
                <div class="filter-group">
                    <div class="range-header">
                        <label><i class="fa-solid fa-gauge-high"></i> الممشى (كم)</label>
                    </div>
                    <div class="range-inputs">
                        <input type="number" id="min-km-input" placeholder="الأدنى كم" class="form-input">
                        <input type="number" id="max-km-input" placeholder="الأقصى كم" class="form-input">
                    </div>
                </div>
            </aside>

            <!-- Main Cars Section -->
            <section class="cars-section">
                
                <!-- Controls Bar -->
                <div class="controls-bar">
                    <div class="results-info">
                        تم العثور على <strong id="results-count">0</strong> سيارة
                    </div>

                    <div class="sort-and-view">
                        <div class="sort-box">
                            <label for="sort-select"><i class="fa-solid fa-arrow-down-short-wide"></i> الترتيب حسب:</label>
                            <select id="sort-select" class="form-select-sm">
                                <option value="default">الأحدث في النظام</option>
                                <option value="price_asc">السعر: من الأقل للأعلى</option>
                                <option value="price_desc">السعر: من الأعلى للأقل</option>
                                <option value="year_desc">سنة الصنع: الأحدث أولاً</option>
                                <option value="year_asc">سنة الصنع: الأقدم أولاً</option>
                                <option value="km_asc">الممشى: الأقل ممشى</option>
                                <option value="km_desc">الممشى: الأكثر ممشى</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Active Filter Pills -->
                <div class="active-pills-container" id="active-pills"></div>

                <!-- Cars Grid Container -->
                <div class="cars-grid" id="cars-grid">
                    <div class="skeleton-card"></div>
                    <div class="skeleton-card"></div>
                    <div class="skeleton-card"></div>
                    <div class="skeleton-card"></div>
                    <div class="skeleton-card"></div>
                    <div class="skeleton-card"></div>
                </div>

                <!-- Empty State -->
                <div class="empty-state hidden" id="empty-state">
                    <div class="empty-icon"><i class="fa-solid fa-car-burst"></i></div>
                    <h3>لم يتم العثور على سيارات تطابق معاييرك</h3>
                    <p>قم بتعديل أو مسح خيارات الفلترة لمشاهدة باقي نتائج السيارات المتاحة في المعرض.</p>
                    <button type="button" class="btn btn-primary" id="empty-reset-btn">مسح كافة الفلاتر</button>
                </div>

                <!-- Pagination Container -->
                <div class="pagination-container" id="pagination-container">
                    <button class="page-btn" id="prev-page-btn" disabled>
                        <i class="fa-solid fa-chevron-right"></i> السابق
                    </button>
                    <div class="page-numbers" id="page-numbers"></div>
                    <button class="page-btn" id="next-page-btn">
                        التالي <i class="fa-solid fa-chevron-left"></i>
                    </button>
                </div>

            </section>

        </main>
    </div>

    <!-- PAGE VIEW 2: DEDICATED DECISION-MAKING ANALYTICS PAGE -->
    <div id="page-analytics" class="page-view-container hidden">
        <section class="analytics-page-section container">
            
            <div class="analytics-page-header">
                <div class="hero-tag">
                    <i class="fa-solid fa-lightbulb"></i> لوحة التحليل ورؤى اتخاذ القرار التنفيذي
                </div>
                <h1 class="page-title">رسومات بيانية وتحليلات متقدمة لتسهيل اتخاذ القرارات</h1>
                <p class="page-subtitle">رؤية كاملة لمؤشرات مخزون المعرض، القيمة المالكة، توزيع الماركات والمدن، واتجاهات الأسعار بالسوق.</p>
            </div>

            <!-- Executive KPIs Row -->
            <div class="analytics-page-kpis">
                <div class="analytics-kpi-box">
                    <div class="kpi-icon-box blue"><i class="fa-solid fa-car-side"></i></div>
                    <div class="kpi-info">
                        <div class="kpi-num" id="page-kpi-total">0</div>
                        <div class="kpi-name">إجمالي السيارات بالمخزون</div>
                    </div>
                </div>

                <div class="analytics-kpi-box gold">
                    <div class="kpi-icon-box gold"><i class="fa-solid fa-vault"></i></div>
                    <div class="kpi-info">
                        <div class="kpi-num" id="page-kpi-valuation">$0</div>
                        <div class="kpi-name">القيمة المالكة للمخزون</div>
                    </div>
                </div>

                <div class="analytics-kpi-box emerald">
                    <div class="kpi-icon-box emerald"><i class="fa-solid fa-hand-holding-dollar"></i></div>
                    <div class="kpi-info">
                        <div class="kpi-num" id="page-kpi-avgprice">$0</div>
                        <div class="kpi-name">متوسط سعر السيارة</div>
                    </div>
                </div>

                <div class="analytics-kpi-box purple">
                    <div class="kpi-icon-box purple"><i class="fa-solid fa-gauge-high"></i></div>
                    <div class="kpi-info">
                        <div class="kpi-num" id="page-kpi-avgkm">0 كم</div>
                        <div class="kpi-name">متوسط ممشى السيارات</div>
                    </div>
                </div>
            </div>

            <!-- Executive Insights & Recommendations Box -->
            <div class="executive-insights-card">
                <h3><i class="fa-solid fa-brain"></i> توصيات ورؤى اتخاذ القرار الفوري</h3>
                <div class="insights-list">
                    <div class="insight-item">
                        <i class="fa-solid fa-circle-check text-success"></i>
                        <div><strong>تنوع الماركات:</strong> تويوتا وهيونداي ومرسيدس تشكل الحصة الأكبر من المخزون، مما يضمن سرعة دوران رأس المال ورغبة عالية لدى المشترين.</div>
                    </div>
                    <div class="insight-item">
                        <i class="fa-solid fa-circle-check text-success"></i>
                        <div><strong>استراتيجية التسعير:</strong> متوسط الأسعار للسيارات الحديثة يتناسب مع نطاق التمويل البنكي، مما يرفع معدلات إتمام الحجوزات والمعاينات.</div>
                    </div>
                    <div class="insight-item">
                        <i class="fa-solid fa-circle-check text-success"></i>
                        <div><strong>التوزيع الجغرافي:</strong> تركز السيارات في المدن الرئيسية يوفر تغطية جغرافية ممتازة لخدمة جميع المعارض الشريكة.</div>
                    </div>
                </div>
            </div>

            <!-- Decision-Making Charts Grid -->
            <div class="analytics-page-charts-grid">
                <div class="chart-card-lg">
                    <div class="chart-header">
                        <h4><i class="fa-solid fa-chart-pie"></i> حصص سوق الماركات الأكثر تواًجداً</h4>
                    </div>
                    <div class="chart-wrapper-lg">
                        <canvas id="pageBrandChart"></canvas>
                    </div>
                </div>

                <div class="chart-card-lg">
                    <div class="chart-header">
                        <h4><i class="fa-solid fa-chart-line"></i> اتجاهات متوسط الأسعار حسب سنة الصنع</h4>
                    </div>
                    <div class="chart-wrapper-lg">
                        <canvas id="pagePriceChart"></canvas>
                    </div>
                </div>

                <div class="chart-card-lg">
                    <div class="chart-header">
                        <h4><i class="fa-solid fa-city"></i> توزيع مخزون السيارات حسب المدن</h4>
                    </div>
                    <div class="chart-wrapper-lg">
                        <canvas id="pageCityChart"></canvas>
                    </div>
                </div>

                <div class="chart-card-lg">
                    <div class="chart-header">
                        <h4><i class="fa-solid fa-gas-pump"></i> تفكيك أنواع الوقود ونواقل الحركة</h4>
                    </div>
                    <div class="chart-wrapper-lg">
                        <canvas id="pageFuelChart"></canvas>
                    </div>
                </div>
            </div>

        </section>
    </div>

    <!-- MODAL 1: Car Details & Actions -->
    <div class="modal-backdrop hidden" id="car-modal">
        <div class="modal-card modal-md">
            <button class="modal-close-btn" id="close-modal-btn">&times;</button>
            <div class="modal-body" id="car-modal-body">
                <!-- Loaded dynamically -->
            </div>
        </div>
    </div>

    <!-- MODAL 2: Test Drive / Inspection Booking -->
    <div class="modal-backdrop hidden" id="booking-modal">
        <div class="modal-card">
            <div class="modal-header">
                <h2><i class="fa-solid fa-calendar-plus"></i> طلب حجز موعد معاينة / تجربة قيادة</h2>
                <button class="modal-close-btn" id="close-booking-modal-btn">&times;</button>
            </div>
            <form id="booking-form" class="modal-form">
                <input type="hidden" id="booking-car-id">
                <div class="form-group">
                    <label>السيارة المطلوبة</label>
                    <input type="text" id="booking-car-title" class="form-input" readonly>
                </div>
                <div class="form-group">
                    <label>الاسم الكامل *</label>
                    <input type="text" id="booking-name" class="form-input" placeholder="أدخل اسمك الكامل" required>
                </div>
                <div class="form-group">
                    <label>رقم الهاتف / الواتساب *</label>
                    <input type="tel" id="booking-phone" class="form-input" placeholder="مثال: 770000000" required>
                </div>
                <div class="form-group">
                    <label>الموعد المفضل للمعاينة</label>
                    <input type="date" id="booking-date" class="form-input">
                </div>
                <div class="form-group">
                    <label>ملاحظات أو استفسارات إضافية</label>
                    <textarea id="booking-notes" class="form-input" rows="3" placeholder="أضف أي تفاصيل تود الاستفسار عنها..."></textarea>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-full">
                        <i class="fa-solid fa-paper-plane"></i> إرسال طلب الحجز
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 3: Car Comparison View -->
    <div class="modal-backdrop hidden" id="compare-modal">
        <div class="modal-card modal-xl">
            <div class="modal-header">
                <h2><i class="fa-solid fa-scale-balanced"></i> أداة مقارنة مواصفات السيارات</h2>
                <button class="modal-close-btn" id="close-compare-modal-btn">&times;</button>
            </div>
            <div class="compare-container" id="compare-container">
                <!-- Comparison matrix loaded dynamically -->
            </div>
        </div>
    </div>

    <!-- MODAL 4: Favorites Drawer/Modal -->
    <div class="modal-backdrop hidden" id="favorites-modal">
        <div class="modal-card modal-lg">
            <div class="modal-header">
                <h2><i class="fa-solid fa-heart text-danger"></i> قائمتك المفضلة من السيارات</h2>
                <button class="modal-close-btn" id="close-fav-modal-btn">&times;</button>
            </div>
            <div class="favorites-grid" id="favorites-grid">
                <!-- Loaded dynamically -->
            </div>
        </div>
    </div>

    <!-- MODAL 5: Add / Edit Car Form (Admin) -->
    <div class="modal-backdrop hidden" id="car-form-modal">
        <div class="modal-card modal-lg">
            <div class="modal-header">
                <h2 id="car-form-title"><i class="fa-solid fa-car-circle-plus"></i> إضافة سيارة جديدة للمخزون</h2>
                <button class="modal-close-btn" id="close-car-form-btn">&times;</button>
            </div>
            <form id="car-crud-form" class="crud-form">
                <input type="hidden" id="crud-car-id">
                
                <div class="form-row">
                    <div class="form-group">
                        <label>اسم السيارة المكتمل *</label>
                        <input type="text" id="crud-name" class="form-input" placeholder="مثال: تويوتا كامري GLE" required>
                    </div>
                    <div class="form-group">
                        <label>ماركة السيارة *</label>
                        <input type="text" id="crud-brand" class="form-input" placeholder="مثال: تويوتا" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>موديل السيارة</label>
                        <input type="text" id="crud-model" class="form-input" placeholder="مثال: كامري">
                    </div>
                    <div class="form-group">
                        <label>سنة الصنع *</label>
                        <input type="number" id="crud-year" class="form-input" min="1990" max="2026" value="2022" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>السعر ($ دولار أمريكي) *</label>
                        <input type="number" id="crud-price" class="form-input" placeholder="مثال: 15000" min="0" required>
                    </div>
                    <div class="form-group">
                        <label>الممشى (كم) *</label>
                        <input type="number" id="crud-km" class="form-input" placeholder="مثال: 45000" min="0" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>ناقل الحركة</label>
                        <select id="crud-transmission" class="form-select">
                            <option value="أوتوماتيك">أوتوماتيك</option>
                            <option value="عادي">عادي (مانيوال)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>نوع الوقود</label>
                        <select id="crud-fuel" class="form-select">
                            <option value="بنزين">بنزين</option>
                            <option value="ديزل">ديزل</option>
                            <option value="هاجين (هايبرد)">هايبرد</option>
                            <option value="كهرباء">كهرباء</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>المدينة</label>
                        <input type="text" id="crud-city" class="form-input" placeholder="مثال: صنعاء" value="صنعاء">
                    </div>
                    <div class="form-group">
                        <label>اسم المعرض</label>
                        <input type="text" id="crud-showroom" class="form-input" placeholder="مثال: معرض المتميز" value="معرض المتميز">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>لون السيارة</label>
                        <input type="text" id="crud-color" class="form-input" placeholder="مثال: أبيض لؤلؤي" value="أبيض">
                    </div>
                    <div class="form-group">
                        <label>حالة السيارة</label>
                        <select id="crud-condition" class="form-select">
                            <option value="مستعمل ممتاز">مستعمل ممتاز</option>
                            <option value="مستعمل بحالة جيدة">مستعمل بحالة جيدة</option>
                            <option value="جديد 0 كم">جديد (زيرو)</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>رقم هاتف التواصل والمعرض</label>
                    <input type="tel" id="crud-phone" class="form-input" placeholder="مثال: 770000000" value="770000000">
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-full" id="crud-submit-btn">
                        <i class="fa-solid fa-save"></i> حفظ السيارة في المخزون
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 6: Bookings & Requests Admin View -->
    <div class="modal-backdrop hidden" id="bookings-admin-modal">
        <div class="modal-card modal-xl">
            <div class="modal-header">
                <h2><i class="fa-solid fa-clipboard-list"></i> إدارة طلبات المعاينة والحجوزات</h2>
                <button class="modal-close-btn" id="close-bookings-admin-btn">&times;</button>
            </div>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>السيارة المطلوبة</th>
                            <th>اسم العميل</th>
                            <th>رقم الهاتف</th>
                            <th>تاريخ المعاينة</th>
                            <th>الملاحظات</th>
                            <th>حالة الطلب</th>
                            <th>تاريخ الطلب</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody id="bookings-table-body">
                        <!-- Loaded dynamically -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL 7: Analytics Quick View Modal -->
    <div class="modal-backdrop hidden" id="analytics-modal">
        <div class="modal-card modal-xl">
            <div class="modal-header">
                <h2><i class="fa-solid fa-chart-line"></i> لوحة الإحصائيات السريعة للمعرض</h2>
                <button class="modal-close-btn" id="close-analytics-btn">&times;</button>
            </div>
            
            <div class="analytics-kpi-row">
                <div class="kpi-card">
                    <div class="kpi-icon"><i class="fa-solid fa-car-side"></i></div>
                    <div class="kpi-val" id="analytics-kpi-total">0</div>
                    <div class="kpi-label">إجمالي السيارات</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-icon"><i class="fa-solid fa-vault"></i></div>
                    <div class="kpi-val" id="analytics-kpi-valuation">$0</div>
                    <div class="kpi-label">إجمالي قيمة المعرض</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-icon"><i class="fa-solid fa-hand-holding-dollar"></i></div>
                    <div class="kpi-val" id="analytics-kpi-avgprice">$0</div>
                    <div class="kpi-label">متوسط سعر السيارة</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-icon"><i class="fa-solid fa-calendar-check"></i></div>
                    <div class="kpi-val" id="analytics-kpi-bookings">0</div>
                    <div class="kpi-label">إجمالي الحجوزات</div>
                </div>
            </div>

            <div class="analytics-grid">
                <div class="chart-card">
                    <h4><i class="fa-solid fa-pie-chart"></i> توزيع أكثر الماركات المطلوبة</h4>
                    <div class="chart-wrapper">
                        <canvas id="brandChart"></canvas>
                    </div>
                </div>

                <div class="chart-card">
                    <h4><i class="fa-solid fa-chart-area"></i> متوسط الأسعار حسب سنة الصنع</h4>
                    <div class="chart-wrapper">
                        <canvas id="priceYearChart"></canvas>
                    </div>
                </div>

                <div class="chart-card">
                    <h4><i class="fa-solid fa-city"></i> توزيع السيارات حسب المدن</h4>
                    <div class="chart-wrapper">
                        <canvas id="cityChart"></canvas>
                    </div>
                </div>

                <div class="chart-card">
                    <h4><i class="fa-solid fa-gas-pump"></i> نسبة توزيع أنواع الوقود والناقل</h4>
                    <div class="chart-wrapper">
                        <canvas id="fuelChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container footer-container">
            <p>© 2026 جميع الحقوق محفوظة | النظام المتكامل لإدارة ومعارض السيارات</p>
        </div>
    </footer>

    <!-- Custom Frontend Application Script -->
    <script src="js/app.js"></script>
</body>
</html>
