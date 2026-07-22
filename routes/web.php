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
    SuperSettingsController,
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
    PurchaseRequestController,
    PurchaseInvoiceController,
    ItemRequestController,
    SettingsController,
    FixedAssetController,
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

    // ─── سازمان‌ها ─────────────────────────────────────────────────────────
    Route::resource('tenants', SuperTenantController::class);
    Route::post('tenants/{tenant}/toggle-status', [SuperTenantController::class, 'toggleStatus'])->name('tenants.toggle-status');
    Route::post('tenants/{tenant}/assign-plan',   [SuperTenantController::class, 'assignPlan'])->name('tenants.assign-plan');

    // ─── کاربران ───────────────────────────────────────────────────────────
    Route::resource('users', SuperUserController::class);
    Route::post('users/{user}/toggle-status', [SuperUserController::class, 'toggleStatus'])->name('users.toggle-status');

    // ─── پلن‌ها ────────────────────────────────────────────────────────────
    Route::resource('plans', SuperPlanController::class)->except(['show']);
    Route::post('plans/{plan}/toggle-status', [SuperPlanController::class, 'toggleStatus'])->name('plans.toggle-status');

    // ─── اشتراک‌ها ─────────────────────────────────────────────────────────
    Route::get('subscriptions',                           [SuperSubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::get('subscriptions/create',                    [SuperSubscriptionController::class, 'create'])->name('subscriptions.create');
    Route::post('subscriptions',                          [SuperSubscriptionController::class, 'store'])->name('subscriptions.store');
    Route::post('subscriptions/{subscription}/cancel',    [SuperSubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
    Route::post('subscriptions/{subscription}/renew',     [SuperSubscriptionController::class, 'renew'])->name('subscriptions.renew');

    Route::get('/payments',  fn() => view('super-admin.placeholder'))->name('payments.index');
    Route::get('/licenses',  fn() => view('super-admin.placeholder'))->name('licenses.index');

    // ─── لاگ‌های سیستمی ────────────────────────────────────────────────────
    Route::get('activity-logs', [SuperActivityLogController::class, 'index'])->name('activity-logs.index');

    // ─── نقش‌ها ────────────────────────────────────────────────────────────
    Route::resource('roles', SuperAdminRoleController::class)->except('show');

    Route::get('/tickets',       fn() => view('super-admin.placeholder'))->name('tickets.index');
    Route::get('/notifications', fn() => view('super-admin.placeholder'))->name('notifications.index');

    // ─── تنظیمات ──────────────────────────────────────────────────────────
    Route::get('/settings',                      [SuperSettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/clear-cache',         [SuperSettingsController::class, 'clearCache'])->name('settings.clear-cache');
    Route::post('/settings/sync-permissions',    [SuperSettingsController::class, 'syncPermissions'])->name('settings.sync-permissions');

    // ─── ابزارها (backward-compat) ─────────────────────────────────────────
    Route::post('/tools/sync-permissions', [ToolController::class, 'syncPermissions'])->name('tools.sync-permissions');
    Route::post('/tools/clear-cache',      [ToolController::class, 'clearCache'])->name('tools.clear-cache');

    // ─── جعل هویت ─────────────────────────────────────────────────────────
    Route::post('/impersonate',    [ImpersonateController::class, 'store'])->name('impersonate.store');
    Route::delete('/impersonate',  [ImpersonateController::class, 'destroy'])->name('impersonate.destroy');
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
    Route::get('/dashboard/stats', [DashboardController::class, 'stats'])->name('dashboard.stats');
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
    // ─── دارایی ثابت ─────────────────────────────────────────────────────────
    Route::prefix('fixed-assets')->name('fixed-assets.')->group(function () {
        Route::get('/',                              [FixedAssetController::class, 'index'])->name('index');
        Route::get('/create',                        [FixedAssetController::class, 'create'])->name('create');
        Route::post('/',                             [FixedAssetController::class, 'store'])->name('store');
        Route::get('/{fixedAsset}',                  [FixedAssetController::class, 'show'])->name('show');
        Route::get('/{fixedAsset}/edit',             [FixedAssetController::class, 'edit'])->name('edit');
        Route::put('/{fixedAsset}',                  [FixedAssetController::class, 'update'])->name('update');
        Route::delete('/{fixedAsset}',               [FixedAssetController::class, 'destroy'])->name('destroy');
        Route::post('/{fixedAsset}/assign',          [FixedAssetController::class, 'assign'])->name('assign');
        Route::post('/{fixedAsset}/return',          [FixedAssetController::class, 'returnAsset'])->name('return');
        Route::post('/{fixedAsset}/maintenance',     [FixedAssetController::class, 'addMaintenance'])->name('maintenance');
        Route::post('/{fixedAsset}/scrap',           [FixedAssetController::class, 'scrap'])->name('scrap');
    });

    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('inventory',              [ReportController::class, 'inventory'])->name('inventory');
        Route::get('inventory/pdf',          [ReportController::class, 'inventoryPdf'])->name('inventory.pdf');
        Route::get('ledger',                 [ReportController::class, 'ledger'])->name('ledger');
        Route::get('in-out-summary',         [ReportController::class, 'inOutSummary'])->name('in-out-summary');
        Route::get('below-minimum',          [ReportController::class, 'belowMinimum'])->name('below-minimum');
        Route::get('stock-value',            [ReportController::class, 'stockValue'])->name('stock-value');
        Route::get('purchase-summary',       [ReportController::class, 'purchaseSummary'])->name('purchase-summary');
        Route::get('supplier-performance',   [ReportController::class, 'supplierPerformance'])->name('supplier-performance');
    });

    // ─── اسناد انبار ────────────────────────────────────────────────────────────
    Route::resource('documents', WarehouseDocumentController::class);
    Route::post('documents/{document}/submit',  [WarehouseDocumentController::class, 'submit'])->name('documents.submit');
    Route::post('documents/{document}/approve', [WarehouseDocumentController::class, 'approve'])->name('documents.approve');
    Route::post('documents/{document}/reject',  [WarehouseDocumentController::class, 'reject'])->name('documents.reject');
    Route::post('documents/{document}/cancel',  [WarehouseDocumentController::class, 'cancel'])->name('documents.cancel');

    // ─── موجودی انبار (لایو) ──────────────────────────────────────────────────
    Route::get('inventory',                        [InventoryController::class, 'index'])->name('inventory.index');
    Route::get('inventory/below-minimum',          [InventoryController::class, 'belowMinimum'])->name('inventory.below-minimum');
    Route::get('inventory/products/{product}',     [InventoryController::class, 'productStock'])->name('inventory.product-stock');
    Route::get('inventory/ledger/{product}',       [InventoryController::class, 'ledger'])->name('inventory.ledger');

    // ─── درخواست خرید (Purchase Request) ────────────────────────────────────
    Route::resource('purchase-requests', PurchaseRequestController::class);
    Route::post('purchase-requests/{purchaseRequest}/submit',  [PurchaseRequestController::class, 'submit'])->name('purchase-requests.submit');
    Route::post('purchase-requests/{purchaseRequest}/approve', [PurchaseRequestController::class, 'approve'])->name('purchase-requests.approve');
    Route::post('purchase-requests/{purchaseRequest}/reject',  [PurchaseRequestController::class, 'reject'])->name('purchase-requests.reject');
    Route::post('purchase-requests/{purchaseRequest}/convert', [PurchaseRequestController::class, 'convertToPo'])->name('purchase-requests.convert');

    // ─── فاکتور خرید (Purchase Invoice) ─────────────────────────────────────
    Route::resource('purchase-invoices', PurchaseInvoiceController::class);
    Route::post('purchase-invoices/{purchaseInvoice}/register',  [PurchaseInvoiceController::class, 'register'])->name('purchase-invoices.register');
    Route::post('purchase-invoices/{purchaseInvoice}/mark-paid', [PurchaseInvoiceController::class, 'markPaid'])->name('purchase-invoices.mark-paid');
    Route::post('purchase-invoices/{purchaseInvoice}/cancel',    [PurchaseInvoiceController::class, 'cancel'])->name('purchase-invoices.cancel');

    // ─── درخواست کالا از انبار (Item Request) ────────────────────────────────
    Route::resource('item-requests', ItemRequestController::class);
    Route::post('item-requests/{itemRequest}/submit',  [ItemRequestController::class, 'submit'])->name('item-requests.submit');
    Route::post('item-requests/{itemRequest}/approve', [ItemRequestController::class, 'approve'])->name('item-requests.approve');
    Route::post('item-requests/{itemRequest}/reject',  [ItemRequestController::class, 'reject'])->name('item-requests.reject');
    Route::post('item-requests/{itemRequest}/issue',   [ItemRequestController::class, 'issueDocument'])->name('item-requests.issue');

    // ─── تنظیمات سیستم (Settings) ────────────────────────────────────────────
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('organization',       [SettingsController::class, 'organization'])->name('organization');
        Route::put('organization',       [SettingsController::class, 'updateOrganization'])->name('organization.update');
        Route::get('warehouse',          [SettingsController::class, 'warehouse'])->name('warehouse');
        Route::put('warehouse',          [SettingsController::class, 'updateWarehouse'])->name('warehouse.update');
        Route::get('workflow',           [SettingsController::class, 'workflow'])->name('workflow');
        Route::put('workflow',           [SettingsController::class, 'updateWorkflow'])->name('workflow.update');
        Route::get('numbering',          [SettingsController::class, 'numbering'])->name('numbering');
        Route::put('numbering',          [SettingsController::class, 'updateNumbering'])->name('numbering.update');
        Route::get('notifications',      [SettingsController::class, 'notifications'])->name('notifications');
        Route::put('notifications',      [SettingsController::class, 'updateNotifications'])->name('notifications.update');
    });
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
