/**
 * Integrated Car Showroom Platform - Master Application JS
 * Handles Search, Multi-Filter, Dynamic Brand Badges (No-Image Design System),
 * Car Comparison Matrix, Loan Calculator, Test Drive Bookings, Favorites,
 * Admin Inventory CRUD, Page Tab Navigation, and Decision-Making Analytics Dashboard.
 */

document.addEventListener('DOMContentLoaded', () => {

    // Application State
    const state = {
        page: 1,
        limit: 12,
        search: '',
        brand: '',
        model: '',
        city: '',
        fuel: '',
        transmission: '',
        showroom: '',
        min_price: null,
        max_price: null,
        min_year: null,
        max_year: null,
        min_km: null,
        max_km: null,
        sort_by: 'default',
        totalPages: 1,
        totalCount: 0,
        activeTab: 'cars',
        favorites: JSON.parse(localStorage.getItem('car_showroom_favs') || '[]'),
        compareList: JSON.parse(localStorage.getItem('car_showroom_compare') || '[]')
    };

    // SVG Brand Icon Badges & Theme Colors Configuration
    const brandThemes = {
        'تويوتا': { icon: 'fa-solid fa-car', color: '#ef4444', gradient: 'linear-gradient(135deg, #ef4444, #991b1b)', tag: 'TOYOTA' },
        'مرسيدس': { icon: 'fa-solid fa-star', color: '#e2e8f0', gradient: 'linear-gradient(135deg, #94a3b8, #334155)', tag: 'MERCEDES' },
        'بي إم دبليو': { icon: 'fa-solid fa-circle-dot', color: '#3b82f6', gradient: 'linear-gradient(135deg, #3b82f6, #1e3a8a)', tag: 'BMW' },
        'هيونداي': { icon: 'fa-solid fa-h', color: '#06b6d4', gradient: 'linear-gradient(135deg, #06b6d4, #164e63)', tag: 'HYUNDAI' },
        'لكزس': { icon: 'fa-solid fa-gem', color: '#f59e0b', gradient: 'linear-gradient(135deg, #f59e0b, #78350f)', tag: 'LEXUS' },
        'نيسان': { icon: 'fa-solid fa-compass', color: '#f43f5e', gradient: 'linear-gradient(135deg, #f43f5e, #881337)', tag: 'NISSAN' },
        'كيا': { icon: 'fa-solid fa-bolt', color: '#10b981', gradient: 'linear-gradient(135deg, #10b981, #064e3b)', tag: 'KIA' },
        'شيفروليه': { icon: 'fa-solid fa-crosshairs', color: '#eab308', gradient: 'linear-gradient(135deg, #eab308, #713f12)', tag: 'CHEVROLET' },
        'فورد': { icon: 'fa-solid fa-shield-halved', color: '#2563eb', gradient: 'linear-gradient(135deg, #2563eb, #1e40af)', tag: 'FORD' },
        'أودي': { icon: 'fa-solid fa-rings-wedding', color: '#a855f7', gradient: 'linear-gradient(135deg, #a855f7, #581c87)', tag: 'AUDI' },
        'لاند روفر': { icon: 'fa-solid fa-mountain', color: '#15803d', gradient: 'linear-gradient(135deg, #15803d, #14532d)', tag: 'LAND ROVER' }
    };

    function getBrandTheme(brandName) {
        if (!brandName) return { icon: 'fa-solid fa-car-side', color: '#3b82f6', gradient: 'linear-gradient(135deg, #3b82f6, #1d4ed8)', tag: 'CAR' };
        for (let key in brandThemes) {
            if (brandName.includes(key)) return brandThemes[key];
        }
        return { icon: 'fa-solid fa-car-side', color: '#6366f1', gradient: 'linear-gradient(135deg, #6366f1, #4338ca)', tag: brandName.toUpperCase() };
    }

    // DOM Elements Mapping
    const elements = {
        globalSearch: document.getElementById('global-search'),
        clearSearchBtn: document.getElementById('clear-search-btn'),
        
        counterTotal: document.getElementById('counter-total'),
        counterShowrooms: document.getElementById('counter-showrooms'),
        counterAvgPrice: document.getElementById('counter-avg-price'),
        counterValuation: document.getElementById('counter-valuation'),

        // Tab Switchers
        tabBtnCars: document.getElementById('tab-btn-cars'),
        tabBtnAnalytics: document.getElementById('tab-btn-analytics'),
        pageCars: document.getElementById('page-cars'),
        pageAnalytics: document.getElementById('page-analytics'),

        openAddCarBtn: document.getElementById('open-add-car-btn'),
        openBookingsBtn: document.getElementById('open-bookings-btn'),
        bookingsCount: document.getElementById('bookings-count'),
        openCompareBtn: document.getElementById('open-compare-btn'),
        compareCount: document.getElementById('compare-count'),
        openFavoritesBtn: document.getElementById('open-favorites-btn'),
        favCount: document.getElementById('fav-count'),
        openAnalyticsBtn: document.getElementById('open-analytics-btn'),

        filterBrand: document.getElementById('filter-brand'),
        filterModel: document.getElementById('filter-model'),
        filterTransmission: document.getElementById('filter-transmission'),
        filterFuel: document.getElementById('filter-fuel'),
        filterCity: document.getElementById('filter-city'),
        filterShowroom: document.getElementById('filter-showroom'),
        minPriceInput: document.getElementById('min-price-input'),
        maxPriceInput: document.getElementById('max-price-input'),
        minYearInput: document.getElementById('min-year-input'),
        maxYearInput: document.getElementById('max-year-input'),
        minKmInput: document.getElementById('min-km-input'),
        maxKmInput: document.getElementById('max-km-input'),
        resetFiltersBtn: document.getElementById('reset-filters-btn'),

        sortSelect: document.getElementById('sort-select'),
        resultsCount: document.getElementById('results-count'),
        activePills: document.getElementById('active-pills'),
        carsGrid: document.getElementById('cars-grid'),
        emptyState: document.getElementById('empty-state'),
        emptyResetBtn: document.getElementById('empty-reset-btn'),

        prevPageBtn: document.getElementById('prev-page-btn'),
        nextPageBtn: document.getElementById('next-page-btn'),
        pageNumbers: document.getElementById('page-numbers'),
        paginationContainer: document.getElementById('pagination-container'),

        // Modals
        carModal: document.getElementById('car-modal'),
        carModalBody: document.getElementById('car-modal-body'),
        closeModalBtn: document.getElementById('close-modal-btn'),

        bookingModal: document.getElementById('booking-modal'),
        bookingForm: document.getElementById('booking-form'),
        closeBookingModalBtn: document.getElementById('close-booking-modal-btn'),
        bookingCarId: document.getElementById('booking-car-id'),
        bookingCarTitle: document.getElementById('booking-car-title'),
        bookingName: document.getElementById('booking-name'),
        bookingPhone: document.getElementById('booking-phone'),
        bookingDate: document.getElementById('booking-date'),
        bookingNotes: document.getElementById('booking-notes'),

        compareModal: document.getElementById('compare-modal'),
        compareContainer: document.getElementById('compare-container'),
        closeCompareModalBtn: document.getElementById('close-compare-modal-btn'),

        favoritesModal: document.getElementById('favorites-modal'),
        favoritesGrid: document.getElementById('favorites-grid'),
        closeFavModalBtn: document.getElementById('close-fav-modal-btn'),

        carFormModal: document.getElementById('car-form-modal'),
        carCrudForm: document.getElementById('car-crud-form'),
        carFormTitle: document.getElementById('car-form-title'),
        closeCarFormBtn: document.getElementById('close-car-form-btn'),
        crudCarId: document.getElementById('crud-car-id'),
        crudName: document.getElementById('crud-name'),
        crudBrand: document.getElementById('crud-brand'),
        crudModel: document.getElementById('crud-model'),
        crudYear: document.getElementById('crud-year'),
        crudPrice: document.getElementById('crud-price'),
        crudKm: document.getElementById('crud-km'),
        crudTransmission: document.getElementById('crud-transmission'),
        crudFuel: document.getElementById('crud-fuel'),
        crudCity: document.getElementById('crud-city'),
        crudShowroom: document.getElementById('crud-showroom'),
        crudColor: document.getElementById('crud-color'),
        crudCondition: document.getElementById('crud-condition'),
        crudPhone: document.getElementById('crud-phone'),

        bookingsAdminModal: document.getElementById('bookings-admin-modal'),
        bookingsTableBody: document.getElementById('bookings-table-body'),
        closeBookingsAdminBtn: document.getElementById('close-bookings-admin-btn'),

        analyticsModal: document.getElementById('analytics-modal'),
        closeAnalyticsBtn: document.getElementById('close-analytics-btn')
    };

    let searchDebounceTimer = null;
    let chartInstances = {};
    let pageChartInstances = {};

    // Initialization
    init();

    async function init() {
        updateBadgesUI();
        setupEventListeners();
        await loadFilters();
        await loadAnalyticsOverview();
        await fetchCars();
    }

    function updateBadgesUI() {
        elements.favCount.textContent = state.favorites.length;
        elements.favCount.classList.toggle('hidden', state.favorites.length === 0);

        elements.compareCount.textContent = state.compareList.length;
        elements.compareCount.classList.toggle('hidden', state.compareList.length === 0);

        localStorage.setItem('car_showroom_favs', JSON.stringify(state.favorites));
        localStorage.setItem('car_showroom_compare', JSON.stringify(state.compareList));
    }

    // Setup Event Listeners
    function setupEventListeners() {
        // Tab Page Switchers
        elements.tabBtnCars.addEventListener('click', () => switchTab('cars'));
        elements.tabBtnAnalytics.addEventListener('click', () => switchTab('analytics'));

        // Global Search
        elements.globalSearch.addEventListener('input', (e) => {
            const val = e.target.value.trim();
            elements.clearSearchBtn.style.display = val ? 'block' : 'none';
            clearTimeout(searchDebounceTimer);
            searchDebounceTimer = setTimeout(() => {
                state.search = val;
                state.page = 1;
                fetchCars();
            }, 300);
        });

        elements.clearSearchBtn.addEventListener('click', () => {
            elements.globalSearch.value = '';
            elements.clearSearchBtn.style.display = 'none';
            state.search = '';
            state.page = 1;
            fetchCars();
        });

        // Dropdown Filters
        elements.filterBrand.addEventListener('change', async (e) => {
            state.brand = e.target.value;
            state.model = '';
            state.page = 1;
            await updateModelsDropdown(state.brand);
            fetchCars();
        });

        elements.filterModel.addEventListener('change', (e) => { state.model = e.target.value; state.page = 1; fetchCars(); });
        elements.filterTransmission.addEventListener('change', (e) => { state.transmission = e.target.value; state.page = 1; fetchCars(); });
        elements.filterFuel.addEventListener('change', (e) => { state.fuel = e.target.value; state.page = 1; fetchCars(); });
        elements.filterCity.addEventListener('change', (e) => { state.city = e.target.value; state.page = 1; fetchCars(); });
        elements.filterShowroom.addEventListener('change', (e) => { state.showroom = e.target.value; state.page = 1; fetchCars(); });

        // Range Filters
        elements.minPriceInput.addEventListener('change', (e) => { state.min_price = e.target.value ? parseFloat(e.target.value) : null; state.page = 1; fetchCars(); });
        elements.maxPriceInput.addEventListener('change', (e) => { state.max_price = e.target.value ? parseFloat(e.target.value) : null; state.page = 1; fetchCars(); });
        elements.minYearInput.addEventListener('change', (e) => { state.min_year = e.target.value ? parseInt(e.target.value) : null; state.page = 1; fetchCars(); });
        elements.maxYearInput.addEventListener('change', (e) => { state.max_year = e.target.value ? parseInt(e.target.value) : null; state.page = 1; fetchCars(); });
        elements.minKmInput.addEventListener('change', (e) => { state.min_km = e.target.value ? parseFloat(e.target.value) : null; state.page = 1; fetchCars(); });
        elements.maxKmInput.addEventListener('change', (e) => { state.max_km = e.target.value ? parseFloat(e.target.value) : null; state.page = 1; fetchCars(); });

        // Sorting
        elements.sortSelect.addEventListener('change', (e) => {
            state.sort_by = e.target.value;
            state.page = 1;
            fetchCars();
        });

        // Reset Filters
        elements.resetFiltersBtn.addEventListener('click', resetAllFilters);
        elements.emptyResetBtn.addEventListener('click', resetAllFilters);

        // Pagination Buttons
        elements.prevPageBtn.addEventListener('click', () => {
            if (state.page > 1) {
                state.page--;
                fetchCars();
                window.scrollTo({ top: 400, behavior: 'smooth' });
            }
        });
        elements.nextPageBtn.addEventListener('click', () => {
            if (state.page < state.totalPages) {
                state.page++;
                fetchCars();
                window.scrollTo({ top: 400, behavior: 'smooth' });
            }
        });

        // Modals Controls
        elements.closeModalBtn.addEventListener('click', () => elements.carModal.classList.add('hidden'));
        elements.carModal.addEventListener('click', (e) => { if (e.target === elements.carModal) elements.carModal.classList.add('hidden'); });

        elements.closeBookingModalBtn.addEventListener('click', () => elements.bookingModal.classList.add('hidden'));
        elements.bookingModal.addEventListener('click', (e) => { if (e.target === elements.bookingModal) elements.bookingModal.classList.add('hidden'); });

        elements.openCompareBtn.addEventListener('click', openCompareModal);
        elements.closeCompareModalBtn.addEventListener('click', () => elements.compareModal.classList.add('hidden'));

        elements.openFavoritesBtn.addEventListener('click', openFavoritesModal);
        elements.closeFavModalBtn.addEventListener('click', () => elements.favoritesModal.classList.add('hidden'));

        elements.openAddCarBtn.addEventListener('click', () => openCarCrudModal(null));
        elements.closeCarFormBtn.addEventListener('click', () => elements.carFormModal.classList.add('hidden'));

        elements.openBookingsBtn.addEventListener('click', openBookingsAdminModal);
        elements.closeBookingsAdminBtn.addEventListener('click', () => elements.bookingsAdminModal.classList.add('hidden'));

        elements.openAnalyticsBtn.addEventListener('click', () => {
            elements.analyticsModal.classList.remove('hidden');
            renderAnalyticsCharts();
        });
        elements.closeAnalyticsBtn.addEventListener('click', () => elements.analyticsModal.classList.add('hidden'));

        // Booking Form Submit
        elements.bookingForm.addEventListener('submit', handleBookingSubmit);

        // CRUD Form Submit
        elements.carCrudForm.addEventListener('submit', handleCarCrudSubmit);
    }

    // Switch between Page 1 (Cars) and Page 2 (Decision Analytics Page)
    function switchTab(tabName) {
        state.activeTab = tabName;
        if (tabName === 'cars') {
            elements.tabBtnCars.classList.add('active');
            elements.tabBtnAnalytics.classList.remove('active');
            elements.pageCars.classList.remove('hidden');
            elements.pageAnalytics.classList.add('hidden');
        } else {
            elements.tabBtnAnalytics.classList.add('active');
            elements.tabBtnCars.classList.remove('active');
            elements.pageAnalytics.classList.remove('hidden');
            elements.pageCars.classList.add('hidden');
            renderPageDecisionAnalytics();
        }
    }

    // Load Filter Options
    async function loadFilters() {
        try {
            const res = await fetch('api/filters.php');
            const data = await res.json();

            populateSelect(elements.filterBrand, data.brands);
            populateSelect(elements.filterModel, data.models);
            populateSelect(elements.filterTransmission, data.transmissions);
            populateSelect(elements.filterFuel, data.fuels);
            populateSelect(elements.filterCity, data.cities);
            populateSelect(elements.filterShowroom, data.showrooms);
            
            elements.minPriceInput.placeholder = `الأدنى ($${data.min_price.toLocaleString()})`;
            elements.maxPriceInput.placeholder = `الأقصى ($${data.max_price.toLocaleString()})`;
            elements.minYearInput.placeholder = `من (${data.min_year})`;
            elements.maxYearInput.placeholder = `إلى (${data.max_year})`;
            elements.minKmInput.placeholder = `الأدنى كم (${data.min_km.toLocaleString()})`;
            elements.maxKmInput.placeholder = `الأقصى كم (${data.max_km.toLocaleString()})`;
        } catch (err) {
            console.error('Error loading filters:', err);
        }
    }

    async function updateModelsDropdown(brand) {
        try {
            const res = await fetch(`api/models.php?brand=${encodeURIComponent(brand)}`);
            const data = await res.json();
            elements.filterModel.innerHTML = '<option value="">جميع الموديلات</option>';
            populateSelect(elements.filterModel, data.models);
        } catch (err) {
            console.error('Error updating models:', err);
        }
    }

    function populateSelect(selectElem, items) {
        items.forEach(item => {
            const opt = document.createElement('option');
            opt.value = item;
            opt.textContent = item;
            selectElem.appendChild(opt);
        });
    }

    // Load Analytics KPI Overview
    async function loadAnalyticsOverview() {
        try {
            const res = await fetch('api/analytics.php');
            const data = await res.json();
            
            elements.counterTotal.textContent = data.total_cars.toLocaleString();
            elements.counterShowrooms.textContent = data.total_showrooms.toLocaleString();
            elements.counterAvgPrice.textContent = `$${Math.round(data.avg_price).toLocaleString()}`;
            elements.counterValuation.textContent = `$${Math.round(data.total_valuation).toLocaleString()}`;

            if (data.pending_bookings > 0) {
                elements.bookingsCount.textContent = data.pending_bookings;
                elements.bookingsCount.classList.remove('hidden');
            } else {
                elements.bookingsCount.classList.add('hidden');
            }
        } catch (err) {
            console.error('Error loading analytics overview:', err);
        }
    }

    // Fetch Cars Data
    async function fetchCars() {
        renderSkeletons();
        updateActivePills();

        const params = new URLSearchParams({
            page: state.page,
            limit: state.limit,
            search: state.search,
            brand: state.brand,
            model: state.model,
            city: state.city,
            fuel: state.fuel,
            transmission: state.transmission,
            showroom: state.showroom,
            sort_by: state.sort_by
        });

        if (state.min_price !== null) params.append('min_price', state.min_price);
        if (state.max_price !== null) params.append('max_price', state.max_price);
        if (state.min_year !== null) params.append('min_year', state.min_year);
        if (state.max_year !== null) params.append('max_year', state.max_year);
        if (state.min_km !== null) params.append('min_km', state.min_km);
        if (state.max_km !== null) params.append('max_km', state.max_km);

        try {
            const res = await fetch(`api/cars.php?${params.toString()}`);
            const data = await res.json();

            state.totalCount = data.total_count;
            state.totalPages = data.total_pages;

            elements.resultsCount.textContent = data.total_count.toLocaleString();

            if (data.cars.length === 0) {
                elements.carsGrid.innerHTML = '';
                elements.emptyState.classList.remove('hidden');
                elements.paginationContainer.classList.add('hidden');
            } else {
                elements.emptyState.classList.add('hidden');
                elements.paginationContainer.classList.remove('hidden');
                renderCarCards(data.cars);
                renderPagination();
            }
        } catch (err) {
            console.error('Error fetching cars:', err);
        }
    }

    // Render Skeletons
    function renderSkeletons() {
        elements.carsGrid.innerHTML = Array(6).fill('<div class="skeleton-card"></div>').join('');
    }

    // Render Luxury Image-Free Car Cards
    function renderCarCards(cars) {
        elements.carsGrid.innerHTML = cars.map(car => {
            const theme = getBrandTheme(car.ماركة_السيارة);
            const isFav = state.favorites.includes(car.رقم_السجل);
            const isCompared = state.compareList.includes(car.رقم_السجل);

            return `
                <div class="car-card">
                    <!-- Brand Header Badge Banner (No Image Needed) -->
                    <div class="car-card-header" style="background: ${theme.gradient};">
                        <div class="brand-header-top">
                            <span class="brand-tag-name"><i class="${theme.icon}"></i> ${car.ماركة_السيارة}</span>
                            <div class="card-quick-actions">
                                <button class="btn-fav-icon ${isFav ? 'active' : ''}" onclick="toggleFavorite(${car.رقم_السجل})" title="إضافة للمفضلة">
                                    <i class="fa-solid fa-heart"></i>
                                </button>
                                <button class="btn-compare-icon ${isCompared ? 'active' : ''}" onclick="toggleCompare(${car.رقم_السجل})" title="إضافة للمقارنة">
                                    <i class="fa-solid fa-scale-balanced"></i>
                                </button>
                            </div>
                        </div>
                        <div class="brand-header-body">
                            <span class="car-badge-year">${car.سنة_الصنع}</span>
                            <span class="car-badge-condition">${car.حالة_السيارة || 'مستعمل ممتاز'}</span>
                        </div>
                        <div class="brand-header-price">$${parseInt(car.السعر_بالدولار).toLocaleString()}</div>
                    </div>

                    <!-- Car Spec Content -->
                    <div class="car-card-content">
                        <h3 class="car-title">${car.اسم_السيارة}</h3>
                        <div class="car-sub-info">${car.موديل_السيارة ? 'موديل ' + car.موديل_السيارة : 'فئة ممتازة'}</div>

                        <div class="car-specs-grid">
                            <div class="spec-item"><i class="fa-solid fa-gauge-high"></i> <span>${parseInt(car.الممشى_كم).toLocaleString()} كم</span></div>
                            <div class="spec-item"><i class="fa-solid fa-gears"></i> <span>${car.ناقل_الحركة}</span></div>
                            <div class="spec-item"><i class="fa-solid fa-gas-pump"></i> <span>${car.نوع_الوقود}</span></div>
                            <div class="spec-item"><i class="fa-solid fa-location-dot"></i> <span>${car.المدينة}</span></div>
                        </div>

                        <div class="car-showroom-info">
                            <span class="showroom-name"><i class="fa-solid fa-store"></i> ${car.اسم_المعرض}</span>
                            <span class="car-color-badge"><i class="fa-solid fa-palette"></i> ${car.لون_السيارة}</span>
                        </div>

                        <div class="card-footer-buttons">
                            <button class="btn-card-primary" onclick="openCarModal(${car.رقم_السجل})">
                                <i class="fa-solid fa-circle-info"></i> التفاصيل والحجز
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }

    // Toggle Favorites
    window.toggleFavorite = function(carId) {
        const idx = state.favorites.indexOf(carId);
        if (idx > -1) {
            state.favorites.splice(idx, 1);
        } else {
            state.favorites.push(carId);
        }
        updateBadgesUI();
        fetchCars();
    };

    // Toggle Compare
    window.toggleCompare = function(carId) {
        const idx = state.compareList.indexOf(carId);
        if (idx > -1) {
            state.compareList.splice(idx, 1);
        } else {
            if (state.compareList.length >= 3) {
                alert('يمكنك مقارنة حتى 3 سيارات كحد أقصى بنفس الوقت.');
                return;
            }
            state.compareList.push(carId);
        }
        updateBadgesUI();
        fetchCars();
    };

    // Open Car Details Modal
    window.openCarModal = async function(carId) {
        try {
            const res = await fetch(`api/car_detail.php?id=${carId}`);
            const car = await res.json();
            const theme = getBrandTheme(car.ماركة_السيارة);
            const isFav = state.favorites.includes(car.رقم_السجل);

            const waMsg = encodeURIComponent(`مرحباً، أود الاستفسار وحجز معاينة لسيارة ${car.اسم_السيارة} موديل ${car.سنة_الصنع} المعروضة في ${car.اسم_المعرض} بسعر $${parseInt(car.السعر_بالدولار).toLocaleString()}.`);

            elements.carModalBody.innerHTML = `
                <div class="modal-car-header" style="background: ${theme.gradient}">
                    <div class="modal-brand-badge"><i class="${theme.icon}"></i> ${car.ماركة_السيارة} ${car.موديل_السيارة ? ' - ' + car.موديل_السيارة : ''}</div>
                    <h2 class="modal-title">${car.اسم_السيارة}</h2>
                    <div class="modal-price-tag">$${parseInt(car.السعر_بالدولار).toLocaleString()}</div>
                </div>

                <div class="modal-car-content">
                    <div class="modal-specs-table">
                        <div class="modal-spec-row"><span class="modal-spec-label"><i class="fa-solid fa-store"></i> المعرض</span><span class="modal-spec-val">${car.اسم_المعرض}</span></div>
                        <div class="modal-spec-row"><span class="modal-spec-label"><i class="fa-solid fa-calendar"></i> سنة الصنع</span><span class="modal-spec-val">${car.سنة_الصنع}</span></div>
                        <div class="modal-spec-row"><span class="modal-spec-label"><i class="fa-solid fa-gauge"></i> الممشى</span><span class="modal-spec-val">${parseInt(car.الممشى_كم).toLocaleString()} كم</span></div>
                        <div class="modal-spec-row"><span class="modal-spec-label"><i class="fa-solid fa-location-dot"></i> المدينة</span><span class="modal-spec-val">${car.المدينة}</span></div>
                        <div class="modal-spec-row"><span class="modal-spec-label"><i class="fa-solid fa-gears"></i> ناقل الحركة</span><span class="modal-spec-val">${car.ناقل_الحركة}</span></div>
                        <div class="modal-spec-row"><span class="modal-spec-label"><i class="fa-solid fa-gas-pump"></i> نوع الوقود</span><span class="modal-spec-val">${car.نوع_الوقود}</span></div>
                        <div class="modal-spec-row"><span class="modal-spec-label"><i class="fa-solid fa-palette"></i> اللون</span><span class="modal-spec-val">${car.لون_السيارة}</span></div>
                        <div class="modal-spec-row"><span class="modal-spec-label"><i class="fa-solid fa-shield-halved"></i> حالة السيارة</span><span class="modal-spec-val">${car.حالة_السيارة || 'مستعمل ممتاز'}</span></div>
                    </div>

                    <!-- Calculator Widget -->
                    <div class="calculator-box">
                        <h3><i class="fa-solid fa-calculator"></i> حاسبة التمويل والأقساط الشهرية</h3>
                        <div class="calc-grid">
                            <div class="calc-field">
                                <label>الدفعة الأولى ($): <span id="calc-down-val">$${Math.round(car.السعر_بالدولار * 0.2).toLocaleString()}</span></label>
                                <input type="range" id="calc-down-range" min="0" max="${car.السعر_بالدولار}" step="500" value="${Math.round(car.السعر_بالدولار * 0.2)}">
                            </div>
                            <div class="calc-field">
                                <label>مدة التمويل (أشهر): <span id="calc-months-val">36 شهر (3 سنوات)</span></label>
                                <input type="range" id="calc-months-range" min="12" max="60" step="12" value="36">
                            </div>
                        </div>
                        <div class="calc-result">
                            <span>القسط الشهري المتوقع:</span>
                            <strong id="calc-monthly-price">$0</strong> / شهر
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="modal-actions-grid">
                        <button class="btn btn-booking-modal" onclick="openBookingFormModal(${car.رقم_السجل}, '${car.اسم_السيارة.replace(/'/g, "\\'")}')">
                            <i class="fa-solid fa-calendar-plus"></i> طلب حجز موعد معاينة
                        </button>
                        <a href="tel:${car.رقم_الهاتف}" class="btn btn-phone">
                            <i class="fa-solid fa-phone"></i> الاتصال (${car.رقم_الهاتف})
                        </a>
                        <a href="https://wa.me/967${car.رقم_الهاتف}?text=${waMsg}" target="_blank" class="btn btn-whatsapp">
                            <i class="fa-brands fa-whatsapp"></i> واتساب المعرض
                        </a>
                    </div>

                    <!-- Admin CRUD Buttons -->
                    <div class="admin-actions-bar">
                        <button class="btn btn-sm btn-warning" onclick="openCarCrudModal(${car.رقم_السجل})">
                            <i class="fa-solid fa-pen-to-square"></i> تعديل بيانات السيارة
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteCarConfirm(${car.رقم_السجل})">
                            <i class="fa-solid fa-trash-can"></i> حذف السيارة من المعرض
                        </button>
                    </div>
                </div>
            `;

            elements.carModal.classList.remove('hidden');

            initLoanCalculator(car.السعر_بالدولار);

        } catch (err) {
            console.error('Error loading car details:', err);
        }
    };

    function initLoanCalculator(carPrice) {
        const downRange = document.getElementById('calc-down-range');
        const downVal = document.getElementById('calc-down-val');
        const monthsRange = document.getElementById('calc-months-range');
        const monthsVal = document.getElementById('calc-months-val');
        const monthlyOutput = document.getElementById('calc-monthly-price');

        if (!downRange || !monthsRange) return;

        function updateCalc() {
            const down = parseFloat(downRange.value);
            const months = parseInt(monthsRange.value);
            downVal.textContent = `$${down.toLocaleString()}`;
            monthsVal.textContent = `${months} شهر (${months / 12} سنوات)`;

            const loanAmount = Math.max(0, carPrice - down);
            const interestRate = 0.05;
            const totalWithInterest = loanAmount * (1 + (interestRate * (months / 12)));
            const monthly = months > 0 ? Math.round(totalWithInterest / months) : 0;

            monthlyOutput.textContent = `$${monthly.toLocaleString()}`;
        }

        downRange.addEventListener('input', updateCalc);
        monthsRange.addEventListener('input', updateCalc);
        updateCalc();
    }

    // Open Booking Form Modal
    window.openBookingFormModal = function(carId, carTitle) {
        elements.carModal.classList.add('hidden');
        elements.bookingCarId.value = carId;
        elements.bookingCarTitle.value = carTitle;
        elements.bookingDate.value = new Date().toISOString().split('T')[0];
        elements.bookingModal.classList.remove('hidden');
    };

    async function handleBookingSubmit(e) {
        e.preventDefault();
        const payload = {
            car_id: elements.bookingCarId.value,
            car_title: elements.bookingCarTitle.value,
            customer_name: elements.bookingName.value.trim(),
            customer_phone: elements.bookingPhone.value.trim(),
            preferred_date: elements.bookingDate.value,
            notes: elements.bookingNotes.value.trim()
        };

        try {
            const res = await fetch('api/bookings.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            if (res.ok) {
                alert('✅ ' + data.message);
                elements.bookingForm.reset();
                elements.bookingModal.classList.add('hidden');
                await loadAnalyticsOverview();
            } else {
                alert('⚠️ ' + (data.error || 'حدث خطأ أثناء حفظ الطلب'));
            }
        } catch (err) {
            console.error('Error submitting booking:', err);
        }
    }

    // Open Compare Modal
    async function openCompareModal() {
        if (state.compareList.length === 0) {
            alert('لم تقم باختيار أي سيارة للمقارنة. اضغط على أيقونة الميزان في بطاقة أي سيارة لإضافتها لجدول المقارنة.');
            return;
        }

        try {
            const cars = await Promise.all(state.compareList.map(id => fetch(`api/car_detail.php?id=${id}`).then(r => r.json())));

            let html = `
                <table class="compare-table">
                    <thead>
                        <tr>
                            <th>المواصفة / السيارة</th>
                            ${cars.map(c => `
                                <th>
                                    <div class="compare-car-header">
                                        <div class="compare-title">${c.اسم_السيارة}</div>
                                        <div class="compare-price">$${parseInt(c.السعر_بالدولار).toLocaleString()}</div>
                                        <button class="btn-remove-compare" onclick="toggleCompare(${c.رقم_السجل}); openCompareModal();">&times; إزالة</button>
                                    </div>
                                </th>
                            `).join('')}
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>الماركة والموديل</td>${cars.map(c => `<td><strong>${c.ماركة_السيارة} ${c.موديل_السيارة}</strong></td>`).join('')}</tr>
                        <tr><td>سنة الصنع</td>${cars.map(c => `<td>${c.سنة_الصنع}</td>`).join('')}</tr>
                        <tr><td>السعر ($)</td>${cars.map(c => `<td><span class="highlight-val">$${parseInt(c.السعر_بالدولار).toLocaleString()}</span></td>`).join('')}</tr>
                        <tr><td>الممشى (كم)</td>${cars.map(c => `<td>${parseInt(c.الممشى_كم).toLocaleString()} كم</td>`).join('')}</tr>
                        <tr><td>ناقل الحركة</td>${cars.map(c => `<td>${c.ناقل_الحركة}</td>`).join('')}</tr>
                        <tr><td>نوع الوقود</td>${cars.map(c => `<td>${c.نوع_الوقود}</td>`).join('')}</tr>
                        <tr><td>المدينة</td>${cars.map(c => `<td>${c.المدينة}</td>`).join('')}</tr>
                        <tr><td>المعرض</td>${cars.map(c => `<td>${c.اسم_المعرض}</td>`).join('')}</tr>
                        <tr><td>اللون</td>${cars.map(c => `<td>${c.لون_السيارة}</td>`).join('')}</tr>
                        <tr><td>حالة السيارة</td>${cars.map(c => `<td>${c.حالة_السيارة}</td>`).join('')}</tr>
                    </tbody>
                </table>
            `;

            elements.compareContainer.innerHTML = html;
            elements.compareModal.classList.remove('hidden');

        } catch (err) {
            console.error('Error loading compare modal:', err);
        }
    }

    // Open Favorites Modal
    async function openFavoritesModal() {
        if (state.favorites.length === 0) {
            alert('قائمة المفضلة فارغة حالياً. اضغط على أيقونة القلب في بطاقة أي سيارة للوصول إليها لاحقاً.');
            return;
        }

        try {
            const cars = await Promise.all(state.favorites.map(id => fetch(`api/car_detail.php?id=${id}`).then(r => r.json())));

            elements.favoritesGrid.innerHTML = cars.map(car => `
                <div class="fav-item-card">
                    <div class="fav-info">
                        <h4>${car.اسم_السيارة}</h4>
                        <div class="fav-meta">${car.ماركة_السيارة} - ${car.سنة_الصنع} | ${car.المدينة}</div>
                        <div class="fav-price">$${parseInt(car.السعر_بالدولار).toLocaleString()}</div>
                    </div>
                    <div class="fav-actions">
                        <button class="btn btn-sm btn-primary" onclick="elements.favoritesModal.classList.add('hidden'); openCarModal(${car.رقم_السجل})">عرض</button>
                        <button class="btn btn-sm btn-danger" onclick="toggleFavorite(${car.رقم_السجل}); openFavoritesModal();">إزالة</button>
                    </div>
                </div>
            `).join('');

            elements.favoritesModal.classList.remove('hidden');
        } catch (err) {
            console.error('Error loading favorites modal:', err);
        }
    }

    // CRUD Admin Modal (Add/Edit Car)
    window.openCarCrudModal = async function(carId = null) {
        elements.carModal.classList.add('hidden');

        if (carId) {
            try {
                const res = await fetch(`api/car_detail.php?id=${carId}`);
                const car = await res.json();
                elements.crudCarId.value = car.رقم_السجل;
                elements.crudName.value = car.اسم_السيارة;
                elements.crudBrand.value = car.ماركة_السيارة;
                elements.crudModel.value = car.موديل_السيارة;
                elements.crudYear.value = car.سنة_الصنع;
                elements.crudPrice.value = car.السعر_بالدولار;
                elements.crudKm.value = car.الممشى_كم;
                elements.crudTransmission.value = car.ناقل_الحركة;
                elements.crudFuel.value = car.نوع_الوقود;
                elements.crudCity.value = car.المدينة;
                elements.crudShowroom.value = car.اسم_المعرض;
                elements.crudColor.value = car.لون_السيارة;
                elements.crudCondition.value = car.حالة_السيارة;
                elements.crudPhone.value = car.رقم_الهاتف;

                elements.carFormTitle.innerHTML = '<i class="fa-solid fa-pen-to-square"></i> تعديل بيانات السيارة';
            } catch (err) {
                console.error('Error loading car for edit:', err);
            }
        } else {
            elements.carCrudForm.reset();
            elements.crudCarId.value = '';
            elements.crudYear.value = 2023;
            elements.crudTransmission.value = 'أوتوماتيك';
            elements.crudFuel.value = 'بنزين';
            elements.crudCity.value = 'صنعاء';
            elements.crudShowroom.value = 'معرض المتميز';
            elements.crudCondition.value = 'مستعمل ممتاز';
            elements.crudPhone.value = '770000000';

            elements.carFormTitle.innerHTML = '<i class="fa-solid fa-car-circle-plus"></i> إضافة سيارة جديدة للمخزون';
        }

        elements.carFormModal.classList.remove('hidden');
    };

    async function handleCarCrudSubmit(e) {
        e.preventDefault();
        const carId = elements.crudCarId.value;
        const payload = {
            اسم_السيارة: elements.crudName.value.trim(),
            ماركة_السيارة: elements.crudBrand.value.trim(),
            موديل_السيارة: elements.crudModel.value.trim(),
            سنة_الصنع: parseInt(elements.crudYear.value),
            السعر_بالدولار: parseFloat(elements.crudPrice.value),
            الممشى_كم: parseFloat(elements.crudKm.value),
            ناقل_الحركة: elements.crudTransmission.value,
            نوع_الوقود: elements.crudFuel.value,
            المدينة: elements.crudCity.value.trim(),
            اسم_المعرض: elements.crudShowroom.value.trim(),
            لون_السيارة: elements.crudColor.value.trim(),
            حالة_السيارة: elements.crudCondition.value.trim(),
            رقم_الهاتف: elements.crudPhone.value.trim()
        };

        try {
            const url = carId ? `api/car_detail.php?id=${carId}` : 'api/cars.php';
            const method = carId ? 'PUT' : 'POST';

            const res = await fetch(url, {
                method: method,
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const data = await res.json();

            if (res.ok) {
                alert('✅ ' + data.message);
                elements.carFormModal.classList.add('hidden');
                await loadFilters();
                await loadAnalyticsOverview();
                fetchCars();
            } else {
                alert('⚠️ ' + (data.error || 'حدث خطأ أثناء التعديل'));
            }
        } catch (err) {
            console.error('Error saving car:', err);
        }
    }

    // Delete Car Confirm
    window.deleteCarConfirm = async function(carId) {
        if (!confirm('هل أنت تأكد من رغبتك في حذف هذه السيارة نهائياً من قاعدة بيانات المعرض؟')) return;

        try {
            const res = await fetch(`api/car_detail.php?id=${carId}`, { method: 'DELETE' });
            const data = await res.json();
            if (res.ok) {
                alert('✅ ' + data.message);
                elements.carModal.classList.add('hidden');
                await loadFilters();
                await loadAnalyticsOverview();
                fetchCars();
            } else {
                alert('⚠️ ' + (data.error || 'حدث خطأ في عملية الحذف'));
            }
        } catch (err) {
            console.error('Error deleting car:', err);
        }
    };

    // Open Bookings Admin Modal
    async function openBookingsAdminModal() {
        try {
            const res = await fetch('api/bookings.php');
            const data = await res.json();

            if (data.bookings.length === 0) {
                elements.bookingsTableBody.innerHTML = `<tr><td colspan="9" style="text-align:center; padding: 2rem;">لا يوجد طلبات معاينة مسجلة حتى الآن</td></tr>`;
            } else {
                elements.bookingsTableBody.innerHTML = data.bookings.map(b => `
                    <tr>
                        <td>#${b.id}</td>
                        <td><strong>${b.car_title}</strong></td>
                        <td>${b.customer_name}</td>
                        <td><a href="tel:${b.customer_phone}">${b.customer_phone}</a></td>
                        <td>${b.preferred_date || 'غير محدد'}</td>
                        <td>${b.notes || '-'}</td>
                        <td>
                            <select onchange="updateBookingStatus(${b.id}, this.value)" class="form-select-sm">
                                <option value="معلقة" ${b.status === 'معلقة' ? 'selected' : ''}>معلقة</option>
                                <option value="مؤكدة" ${b.status === 'مؤكدة' ? 'selected' : ''}>مؤكدة</option>
                                <option value="مكتملة" ${b.status === 'مكتملة' ? 'selected' : ''}>مكتملة</option>
                                <option value="ملغاة" ${b.status === 'ملغاة' ? 'selected' : ''}>ملغاة</option>
                            </select>
                        </td>
                        <td>${new Date(b.created_at).toLocaleDateString('ar-EG')}</td>
                        <td>
                            <button class="btn btn-sm btn-danger" onclick="deleteBooking(${b.id})"><i class="fa-solid fa-trash"></i></button>
                        </td>
                    </tr>
                `).join('');
            }

            elements.bookingsAdminModal.classList.remove('hidden');
        } catch (err) {
            console.error('Error loading bookings:', err);
        }
    }

    window.updateBookingStatus = async function(id, status) {
        try {
            await fetch(`api/booking_detail.php?id=${id}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ status })
            });
            await loadAnalyticsOverview();
        } catch (err) {
            console.error('Error updating status:', err);
        }
    };

    window.deleteBooking = async function(id) {
        if (!confirm('هل تريد حذف طلب الحجز هذا؟')) return;
        try {
            await fetch(`api/booking_detail.php?id=${id}`, { method: 'DELETE' });
            openBookingsAdminModal();
            await loadAnalyticsOverview();
        } catch (err) {
            console.error('Error deleting booking:', err);
        }
    };

    // Render Pagination
    function renderPagination() {
        elements.prevPageBtn.disabled = state.page === 1;
        elements.nextPageBtn.disabled = state.page === state.totalPages;

        let pagesHtml = '';
        let start = Math.max(1, state.page - 2);
        let end = Math.min(state.totalPages, state.page + 2);

        if (start > 1) {
            pagesHtml += `<button class="page-num" data-p="1">1</button>`;
            if (start > 2) pagesHtml += `<span style="color:var(--text-muted)">...</span>`;
        }

        for (let i = start; i <= end; i++) {
            pagesHtml += `<button class="page-num ${i === state.page ? 'active' : ''}" data-p="${i}">${i}</button>`;
        }

        if (end < state.totalPages) {
            if (end < state.totalPages - 1) pagesHtml += `<span style="color:var(--text-muted)">...</span>`;
            pagesHtml += `<button class="page-num" data-p="${state.totalPages}">${state.totalPages}</button>`;
        }

        elements.pageNumbers.innerHTML = pagesHtml;
        elements.pageNumbers.querySelectorAll('.page-num').forEach(btn => {
            btn.addEventListener('click', (e) => {
                state.page = parseInt(e.target.getAttribute('data-p'));
                fetchCars();
                window.scrollTo({ top: 400, behavior: 'smooth' });
            });
        });
    }

    // Active Filters Pills
    function updateActivePills() {
        const pills = [];
        if (state.search) pills.push({ key: 'search', label: `بحث: ${state.search}` });
        if (state.brand) pills.push({ key: 'brand', label: `ماركة: ${state.brand}` });
        if (state.model) pills.push({ key: 'model', label: `موديل: ${state.model}` });
        if (state.transmission) pills.push({ key: 'transmission', label: `ناقل: ${state.transmission}` });
        if (state.fuel) pills.push({ key: 'fuel', label: `وقود: ${state.fuel}` });
        if (state.city) pills.push({ key: 'city', label: `مدينة: ${state.city}` });
        if (state.showroom) pills.push({ key: 'showroom', label: `معرض: ${state.showroom}` });
        if (state.min_price) pills.push({ key: 'min_price', label: `سعر أدنى: $${state.min_price.toLocaleString()}` });
        if (state.max_price) pills.push({ key: 'max_price', label: `سعر أقصى: $${state.max_price.toLocaleString()}` });
        if (state.min_year) pills.push({ key: 'min_year', label: `من سنة: ${state.min_year}` });
        if (state.max_year) pills.push({ key: 'max_year', label: `إلى سنة: ${state.max_year}` });
        if (state.min_km) pills.push({ key: 'min_km', label: `ممشى أدنى: ${state.min_km.toLocaleString()} كم` });
        if (state.max_km) pills.push({ key: 'max_km', label: `ممشى أقصى: ${state.max_km.toLocaleString()} كم` });

        elements.activePills.innerHTML = pills.map(p => `
            <div class="active-pill">
                ${p.label} <span onclick="removePill('${p.key}')">&times;</span>
            </div>
        `).join('');
    }

    window.removePill = function(key) {
        if (key === 'search') { state.search = ''; elements.globalSearch.value = ''; elements.clearSearchBtn.style.display = 'none'; }
        if (key === 'brand') { state.brand = ''; elements.filterBrand.value = ''; updateModelsDropdown(''); }
        if (key === 'model') { state.model = ''; elements.filterModel.value = ''; }
        if (key === 'transmission') { state.transmission = ''; elements.filterTransmission.value = ''; }
        if (key === 'fuel') { state.fuel = ''; elements.filterFuel.value = ''; }
        if (key === 'city') { state.city = ''; elements.filterCity.value = ''; }
        if (key === 'showroom') { state.showroom = ''; elements.filterShowroom.value = ''; }
        if (key === 'min_price') { state.min_price = null; elements.minPriceInput.value = ''; }
        if (key === 'max_price') { state.max_price = null; elements.maxPriceInput.value = ''; }
        if (key === 'min_year') { state.min_year = null; elements.minYearInput.value = ''; }
        if (key === 'max_year') { state.max_year = null; elements.maxYearInput.value = ''; }
        if (key === 'min_km') { state.min_km = null; elements.minKmInput.value = ''; }
        if (key === 'max_km') { state.max_km = null; elements.maxKmInput.value = ''; }
        state.page = 1;
        fetchCars();
    };

    function resetAllFilters() {
        state.search = '';
        state.brand = '';
        state.model = '';
        state.transmission = '';
        state.fuel = '';
        state.city = '';
        state.showroom = '';
        state.min_price = null;
        state.max_price = null;
        state.min_year = null;
        state.max_year = null;
        state.min_km = null;
        state.max_km = null;
        state.sort_by = 'default';
        state.page = 1;

        elements.globalSearch.value = '';
        elements.clearSearchBtn.style.display = 'none';
        elements.filterBrand.value = '';
        elements.filterModel.value = '';
        elements.filterTransmission.value = '';
        elements.filterFuel.value = '';
        elements.filterCity.value = '';
        elements.filterShowroom.value = '';
        elements.minPriceInput.value = '';
        elements.maxPriceInput.value = '';
        elements.minYearInput.value = '';
        elements.maxYearInput.value = '';
        elements.minKmInput.value = '';
        elements.maxKmInput.value = '';
        elements.sortSelect.value = 'default';

        updateModelsDropdown('');
        fetchCars();
    }

    // PAGE 2: Decision-Making Analytics Dashboard Rendering
    async function renderPageDecisionAnalytics() {
        try {
            const res = await fetch('api/analytics.php');
            const data = await res.json();

            document.getElementById('page-kpi-total').textContent = data.total_cars.toLocaleString();
            document.getElementById('page-kpi-valuation').textContent = `$${Math.round(data.total_valuation).toLocaleString()}`;
            document.getElementById('page-kpi-avgprice').textContent = `$${Math.round(data.avg_price).toLocaleString()}`;
            document.getElementById('page-kpi-avgkm').textContent = `${Math.round(data.avg_km).toLocaleString()} كم`;

            Object.values(pageChartInstances).forEach(chart => chart.destroy());

            // 1. Page Brand Chart
            const ctxBrand = document.getElementById('pageBrandChart').getContext('2d');
            pageChartInstances.brand = new Chart(ctxBrand, {
                type: 'doughnut',
                data: {
                    labels: data.top_brands.map(b => b.brand),
                    datasets: [{
                        data: data.top_brands.map(b => b.count),
                        backgroundColor: ['#ef4444', '#3b82f6', '#06b6d4', '#f59e0b', '#10b981', '#a855f7', '#ec4899']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { labels: { color: '#f3f4f6', font: { family: 'Tajawal', size: 13 } } } }
                }
            });

            // 2. Page Price Trend Chart
            const ctxPrice = document.getElementById('pagePriceChart').getContext('2d');
            pageChartInstances.price = new Chart(ctxPrice, {
                type: 'line',
                data: {
                    labels: data.year_prices.map(yp => yp.year),
                    datasets: [{
                        label: 'متوسط السعر ($)',
                        data: data.year_prices.map(yp => yp.avg_price),
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.2)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: { ticks: { color: '#9ca3af' } },
                        y: { ticks: { color: '#9ca3af' } }
                    },
                    plugins: { legend: { labels: { color: '#f3f4f6', font: { family: 'Tajawal', size: 13 } } } }
                }
            });

            // 3. Page City Chart
            const ctxCity = document.getElementById('pageCityChart').getContext('2d');
            pageChartInstances.city = new Chart(ctxCity, {
                type: 'bar',
                data: {
                    labels: data.top_cities.map(c => c.city),
                    datasets: [{
                        label: 'عدد السيارات في المدينة',
                        data: data.top_cities.map(c => c.count),
                        backgroundColor: '#10b981'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: { ticks: { color: '#9ca3af' } },
                        y: { ticks: { color: '#9ca3af' } }
                    },
                    plugins: { legend: { labels: { color: '#f3f4f6', font: { family: 'Tajawal', size: 13 } } } }
                }
            });

            // 4. Page Fuel Pie Chart
            const ctxFuel = document.getElementById('pageFuelChart').getContext('2d');
            pageChartInstances.fuel = new Chart(ctxFuel, {
                type: 'pie',
                data: {
                    labels: data.fuel_dist.map(f => f.fuel),
                    datasets: [{
                        data: data.fuel_dist.map(f => f.count),
                        backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#8b5cf6']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { labels: { color: '#f3f4f6', font: { family: 'Tajawal', size: 13 } } } }
                }
            });

        } catch (err) {
            console.error('Error rendering page decision analytics:', err);
        }
    }

    // Modal Chart.js Analytics Dashboard
    async function renderAnalyticsCharts() {
        try {
            const res = await fetch('api/analytics.php');
            const data = await res.json();

            document.getElementById('analytics-kpi-total').textContent = data.total_cars.toLocaleString();
            document.getElementById('analytics-kpi-valuation').textContent = `$${Math.round(data.total_valuation).toLocaleString()}`;
            document.getElementById('analytics-kpi-avgprice').textContent = `$${Math.round(data.avg_price).toLocaleString()}`;
            document.getElementById('analytics-kpi-bookings').textContent = data.total_bookings.toLocaleString();

            Object.values(chartInstances).forEach(chart => chart.destroy());

            // 1. Brand Distribution
            const ctxBrand = document.getElementById('brandChart').getContext('2d');
            chartInstances.brand = new Chart(ctxBrand, {
                type: 'doughnut',
                data: {
                    labels: data.top_brands.map(b => b.brand),
                    datasets: [{
                        data: data.top_brands.map(b => b.count),
                        backgroundColor: ['#ef4444', '#3b82f6', '#06b6d4', '#f59e0b', '#10b981', '#a855f7', '#ec4899']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { labels: { color: '#f3f4f6', font: { family: 'Tajawal' } } } }
                }
            });

            // 2. Average Price by Year
            const ctxPrice = document.getElementById('priceYearChart').getContext('2d');
            chartInstances.price = new Chart(ctxPrice, {
                type: 'line',
                data: {
                    labels: data.year_prices.map(yp => yp.year),
                    datasets: [{
                        label: 'متوسط السعر ($)',
                        data: data.year_prices.map(yp => yp.avg_price),
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.15)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: { ticks: { color: '#9ca3af' } },
                        y: { ticks: { color: '#9ca3af' } }
                    },
                    plugins: { legend: { labels: { color: '#f3f4f6', font: { family: 'Tajawal' } } } }
                }
            });

            // 3. City Distribution
            const ctxCity = document.getElementById('cityChart').getContext('2d');
            chartInstances.city = new Chart(ctxCity, {
                type: 'bar',
                data: {
                    labels: data.top_cities.map(c => c.city),
                    datasets: [{
                        label: 'عدد السيارات المعروضة',
                        data: data.top_cities.map(c => c.count),
                        backgroundColor: '#10b981'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: { ticks: { color: '#9ca3af' } },
                        y: { ticks: { color: '#9ca3af' } }
                    },
                    plugins: { legend: { labels: { color: '#f3f4f6', font: { family: 'Tajawal' } } } }
                }
            });

            // 4. Fuel & Transmission Pie
            const ctxFuel = document.getElementById('fuelChart').getContext('2d');
            chartInstances.fuel = new Chart(ctxFuel, {
                type: 'pie',
                data: {
                    labels: data.fuel_dist.map(f => f.fuel),
                    datasets: [{
                        data: data.fuel_dist.map(f => f.count),
                        backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#8b5cf6']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { labels: { color: '#f3f4f6', font: { family: 'Tajawal' } } } }
                }
            });

        } catch (err) {
            console.error('Error rendering analytics charts:', err);
        }
    }

});
