<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\superAdmin\{
    SuperDashboardController,
    SuperTenantController,
    SuperPlanController,
    SuperSubscriptionController,
    SuperActivityLogController,
    SuperAdminRoleController,
    ToolController,
    SuperUserController,
    ImpersonateController
};


use App\Http\Controllers\Core\{
    UserController,
    CompanySwitcherController,
    FiscalYearSwitcherController,
    BillingController,
    FiscalYearController,
    CompanyController,
    ActivityLogController,
    RoleController,
    PermissionController,
    OrganizationalUnitController,
    ContactController,
    EmployeeController,
    LocationController
};

use App\Http\Controllers\Warehouse\{
    ProductAttributeController,
    ProductController,
    WarehouseController,
    WarehouseLocationController,
    MeasurementUnitController,
    CategoryController,
    CostCenterController,
    ProductTypeController,
    StockTransactionController,
    InventoryController,
    BrandController,
    OpeningBalanceController,
    WarehouseDocumentController,
    ReportController,
    PurchaseOrderController,
    ApprovalInboxController,
    AttachmentController,
    ProductImportController,
    PriceHistoryController,
    CompanySettingsController,
    DashboardController,
};



use App\Http\Controllers\Auth\{
    LoginController,
    RegisterController,
    ForgotPasswordController
};
use App\Http\Controllers\{
    DashboardController,
    ProfileController
};


Route::prefix('super-admin')->name('super-admin.')->middleware(['auth', 'superadmin'])->group(function () {
    // چک Super Admin در یک Middleware اختصاصی (یا با استفاده از Controller)
    Route::get('/', [SuperDashboardController::class, 'index'])->name('dashboard');

    // مدیریت Tenantها
    Route::resource('tenants', SuperTenantController::class)->except(['show']);
    Route::resource('users', SuperUserController::class)->except(['show']);

    // مدیریت پلن‌ها
    Route::resource('plans', SuperPlanController::class)->except(['show']);

    // مدیریت اشتراک‌ها
    Route::get('subscriptions', [SuperSubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::post('subscriptions/{subscription}/cancel', [SuperSubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
    Route::post('subscriptions/{subscription}/renew', [SuperSubscriptionController::class, 'renew'])->name('subscriptions.renew');
    Route::get('/payments', fn() => view('super-admin.placeholder'))->name('payments.index');
    Route::get('/licenses', fn() => view('super-admin.placeholder'))->name('licenses.index');
    Route::get('activity-logs', [SuperActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::resource('roles', SuperAdminRoleController::class)->except('show');
    Route::get('/tickets', fn() => view('super-admin.placeholder'))->name('tickets.index');
    Route::get('/notifications', fn() => view('super-admin.placeholder'))->name('notifications.index');
    Route::get('/settings', fn() => view('super-admin.placeholder'))->name('settings.index');
    Route::post('/tools/sync-permissions', [ToolController::class, 'syncPermissions'])->name('tools.sync-permissions');
    Route::post('/tools/clear-cache', [ToolController::class, 'clearCache'])->name('tools.clear-cache');

    Route::post('/impersonate', [ImpersonateController::class, 'store'])->name('impersonate.store');
    Route::delete('/impersonate', [ImpersonateController::class, 'destroy'])->name('impersonate.destroy');
});

//Authentication-----------------
Route::middleware(['guest', 'throttle:7,1'])->group(function () {

    //Login-----------------
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
    // صفحه وارد کردن موبایل برای دریافت OTP
    Route::get('/login/otp/request', [LoginController::class, 'showOtpRequestForm'])->name('login.otp.request');
    // ارسال کد OTP
    Route::post('/login/otp/send', [LoginController::class, 'sendOtp'])->name('login.otp.send');
    // صفحه وارد کردن کد تأیید
    Route::get('/login/otp/verify', [LoginController::class, 'showOtpVerifyForm'])->name('login.otp.verify');
    // تأیید کد
    Route::post('/login/otp/verify', [LoginController::class, 'verifyOtp'])->name('login.otp.verify.post');
    // ارسال مجدد کد
    Route::post('/login/otp/resend', [LoginController::class, 'resendOtp'])->name('login.otp.resend');

    //Register-----------------
    Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.post');
    // ارسال کد OTP - ثبت نام
    Route::get('/register/otp/request', [RegisterController::class, 'showOtpRequestForm'])->name('register.otp.form');
    Route::post('/register/otp/send', [RegisterController::class, 'sendOtp'])->name('register.otp.send');
    Route::post('/register/otp/verify', [RegisterController::class, 'verifyOtp'])->name('register.otp.verify');
    Route::post('/register/otp/resend', [RegisterController::class, 'resendOtp'])->name('register.otp.resend');

    //Forget Password-----------------
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendOtp'])->name('password.send');
    Route::get('/forgot-password/otp', [ForgotPasswordController::class, 'showOtpForm'])->name('password.otp.form');
    Route::post('/forgot-password/otp', [ForgotPasswordController::class, 'verifyOtp'])->name('password.otp.verify');
    Route::post('/forgot-password/otp/resend', [ForgotPasswordController::class, 'resendOtp'])->name('password.otp.resend');
    Route::get('/forgot-password/reset', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset.form');
    Route::post('/forgot-password/reset', [ForgotPasswordController::class, 'resetPassword'])->name('password.reset');
});


Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('warehouse.dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'changePassword'])->name('profile.password');
    // خروج
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
});

// همهٔ مسیرهایی که نیاز به tenant دارند
Route::middleware(['auth', 'require.tenant'])->group(function () {
    Route::post('/switch-company', [CompanySwitcherController::class, 'switch'])->name('company.switch');
    Route::post('/switch-fiscal-year', [FiscalYearSwitcherController::class, 'switch'])->name('fiscal-year.switch');

    // مدیریت کاربران
    Route::resource('users', UserController::class);
    Route::post('users/import', [UserController::class, 'import'])->name('users.import');
    Route::get('users/export', [UserController::class, 'export'])->name('users.export');

    //سطوح دسترسی
    Route::resource('roles', RoleController::class)->except(['show']);

    //سال مالی
    Route::resource('fiscal-years', FiscalYearController::class)->except('show');
    Route::post('fiscal-years/{fiscal_year}/activate', [FiscalYearController::class, 'activate'])->name('fiscal-years.activate');
    Route::post('fiscal-years/{fiscal_year}/close', [FiscalYearController::class, 'close'])->name('fiscal-years.close');

    // واحدهای سازمانی (Organizational Units) – در Core
    Route::resource('organizational-units', OrganizationalUnitController::class)->except(['show', 'create', 'edit']);
    // کارمندان (Employees) – در Core
    Route::resource('employees', EmployeeController::class)->except(['show', 'create', 'edit']);
    // مخاطبین (Contacts) – طرف‌حساب‌های تجاری (مشتری/تامین‌کننده)، در Core
    Route::resource('contacts', ContactController::class)->except(['show']);

    Route::get('/api/countries/{country}/provinces', [LocationController::class, 'provinces'])
        ->name('api.countries.provinces');

});


Route::prefix('warehouse')->name('warehouse.')->middleware(['auth', 'require.tenant'])->group(function () {

    // =    واحدهای اندازه‌گیری (Measurement Units)
    Route::resource('measurement-units', MeasurementUnitController::class)->except(['show', 'create', 'edit']);
    // دسته‌بندی کالا
    Route::resource('categories', CategoryController::class)->except(['show', 'create', 'edit']);
    // ویژگی‌های کالا
    Route::resource('product-attributes', ProductAttributeController::class)->except(['show', 'create', 'edit']);
    // کالاها (با صفحه Create/Edit مجزا)
    Route::resource('products', ProductController::class);
    // انبارها (با صفحه Create/Edit مجزا)
    Route::resource('warehouses', WarehouseController::class);
    // موقعیت‌های انبار (با صفحه Create/Edit مجزا)
    Route::resource('warehouse-locations', WarehouseLocationController::class);

    //مراکز هزینه
    Route::resource('cost-centers', CostCenterController::class)->except(['show', 'create', 'edit']);
    // در گروه warehouse
    Route::resource('product-types', ProductTypeController::class)->except(['show']);
    Route::get('product-types/{productType}/attributes', [ProductTypeController::class, 'attributes'])->name('product-types.attributes');

    // ─── برندها ───────────────────────────────────────────────────────────────
    Route::resource('brands', BrandController::class)->except(['create', 'edit', 'show']);

    // ─── موجودی اولیه ────────────────────────────────────────────────────────
    Route::get('opening-balance',  [OpeningBalanceController::class, 'index'])->name('opening-balance.index');
    Route::post('opening-balance', [OpeningBalanceController::class, 'store'])->name('opening-balance.store');

    // ─── تراکنش‌های انبار (رسید / حواله / تعدیل / ...) ───────────────────────
    Route::resource('stock-transactions', StockTransactionController::class);
    Route::post('stock-transactions/{stockTransaction}/submit',  [StockTransactionController::class, 'submit'])->name('stock-transactions.submit');
    Route::post('stock-transactions/{stockTransaction}/approve', [StockTransactionController::class, 'approve'])->name('stock-transactions.approve');
    Route::post('stock-transactions/{stockTransaction}/reject',  [StockTransactionController::class, 'reject'])->name('stock-transactions.reject');
    // AJAX: موقعیت‌های یک انبار
    Route::get('warehouses/{warehouse}/locations', [StockTransactionController::class, 'locations'])->name('warehouses.locations');

    // ─── سفارش خرید (Purchase Order) ────────────────────────────────────────
    Route::resource('purchase-orders', PurchaseOrderController::class);
    Route::post('purchase-orders/{purchaseOrder}/confirm',   [PurchaseOrderController::class, 'confirm'])->name('purchase-orders.confirm');
    Route::post('purchase-orders/{purchaseOrder}/mark-sent', [PurchaseOrderController::class, 'markSent'])->name('purchase-orders.mark-sent');
    Route::get( 'purchase-orders/{purchaseOrder}/receive',   [PurchaseOrderController::class, 'receiveForm'])->name('purchase-orders.receive-form');
    Route::post('purchase-orders/{purchaseOrder}/receive',   [PurchaseOrderController::class, 'receive'])->name('purchase-orders.receive');
    Route::post('purchase-orders/{purchaseOrder}/close',     [PurchaseOrderController::class, 'close'])->name('purchase-orders.close');
    Route::post('purchase-orders/{purchaseOrder}/cancel',    [PurchaseOrderController::class, 'cancel'])->name('purchase-orders.cancel');
    Route::get( 'purchase-orders/{purchaseOrder}/print',     [PurchaseOrderController::class, 'print'])->name('purchase-orders.print');

    // ─── چاپ سند انبار ───────────────────────────────────────────────────────
    Route::get('documents/{document}/print', [WarehouseDocumentController::class, 'print'])->name('documents.print');

    // ─── گزارشات انبار ───────────────────────────────────────────────────────
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('inventory',           [ReportController::class, 'inventory'])->name('inventory');
        Route::get('ledger',              [ReportController::class, 'ledger'])->name('ledger');
        Route::get('in-out-summary',      [ReportController::class, 'inOutSummary'])->name('in-out-summary');
        Route::get('below-minimum',       [ReportController::class, 'belowMinimum'])->name('below-minimum');
        Route::get('purchase-orders-export', [ReportController::class, 'purchaseOrdersExport'])->name('purchase-orders-export');
    });

    // ─── اسناد انبار ────────────────────────────────────────────────────────────
    Route::resource('documents', WarehouseDocumentController::class);
    Route::post('documents/{document}/submit',  [WarehouseDocumentController::class, 'submit'])->name('documents.submit');
    Route::post('documents/{document}/approve', [WarehouseDocumentController::class, 'approve'])->name('documents.approve');
    Route::post('documents/{document}/reject',  [WarehouseDocumentController::class, 'reject'])->name('documents.reject');
    Route::post('documents/{document}/cancel',  [WarehouseDocumentController::class, 'cancel'])->name('documents.cancel');

    // ─── کارتابل تأیید ───────────────────────────────────────────────────────
    Route::get('approval-inbox', [ApprovalInboxController::class, 'index'])->name('approval-inbox.index');
    Route::post('approval-inbox/documents/{document}/approve', [ApprovalInboxController::class, 'approveDocument'])->name('approval-inbox.approve-document');
    Route::post('approval-inbox/documents/{document}/reject',  [ApprovalInboxController::class, 'rejectDocument'])->name('approval-inbox.reject-document');
    Route::post('approval-inbox/purchase-orders/{purchaseOrder}/approve', [ApprovalInboxController::class, 'approvePo'])->name('approval-inbox.approve-po');
    Route::post('approval-inbox/purchase-orders/{purchaseOrder}/reject',  [ApprovalInboxController::class, 'rejectPo'])->name('approval-inbox.reject-po');

    // ─── پیوست‌ها ─────────────────────────────────────────────────────────────
    Route::post('attachments',              [AttachmentController::class, 'store'])->name('attachments.store');
    Route::get('attachments/{attachment}/download', [AttachmentController::class, 'download'])->name('attachments.download');
    Route::delete('attachments/{attachment}', [AttachmentController::class, 'destroy'])->name('attachments.destroy');

    // ─── ایمپورت کالا ────────────────────────────────────────────────────────
    Route::get('products/import/form',     [ProductImportController::class, 'form'])->name('products.import.form');
    Route::post('products/import',         [ProductImportController::class, 'import'])->name('products.import');
    Route::get('products/import/template', [ProductImportController::class, 'template'])->name('products.import.template');

    // ─── تاریخچه قیمت ────────────────────────────────────────────────────────
    Route::get('price-history',                    [PriceHistoryController::class, 'index'])->name('price-history.index');
    Route::post('price-history',                   [PriceHistoryController::class, 'store'])->name('price-history.store');
    Route::delete('price-history/{priceHistory}',  [PriceHistoryController::class, 'destroy'])->name('price-history.destroy');
    Route::get('price-history/last-price',         [PriceHistoryController::class, 'lastPrice'])->name('price-history.last-price');

    // ─── تنظیمات شرکت ────────────────────────────────────────────────────────
    Route::get('settings/company',  [CompanySettingsController::class, 'index'])->name('settings.company.index');
    Route::put('settings/company',  [CompanySettingsController::class, 'update'])->name('settings.company.update');

    // ─── موجودی انبار (لایو) ──────────────────────────────────────────────────
    Route::get('inventory',                        [InventoryController::class, 'index'])->name('inventory.index');
    Route::get('inventory/below-minimum',          [InventoryController::class, 'belowMinimum'])->name('inventory.below-minimum');
    Route::get('inventory/products/{product}',     [InventoryController::class, 'productStock'])->name('inventory.product-stock');
    Route::get('inventory/ledger/{product}',       [InventoryController::class, 'ledger'])->name('inventory.ledger');
});


//مسیرهایی که مالک فقط باید دسترسی داشته باشد
Route::middleware(['auth', 'require.tenant', 'owner', 'check.subscription'])->group(function () {
    //مدیریت اشتراک ها
    Route::get('/billing/plans', [BillingController::class, 'plans'])->name('billing.plans');
    Route::post('/billing/subscribe', [BillingController::class, 'subscribe'])->name('billing.subscribe');
    Route::get('/billing/license', [BillingController::class, 'license'])->name('billing.license');
    Route::get('/billing/history', [BillingController::class, 'history'])->name('billing.history');

    //لاگ های سیستمی
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');

    //مدیریت سازمان
    Route::resource('companies', CompanyController::class)->except('show');
});
