<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // ==================== ۱. مدیریت سازمان‌ها ====================
            ['name' => 'companies.view', 'title' => 'مشاهده لیست سازمان‌ها', 'group' => 'مدیریت سازمان'],
            ['name' => 'companies.create', 'title' => 'ایجاد سازمان جدید', 'group' => 'مدیریت سازمان'],
            ['name' => 'companies.edit', 'title' => 'ویرایش سازمان', 'group' => 'مدیریت سازمان'],
            ['name' => 'companies.delete', 'title' => 'حذف سازمان', 'group' => 'مدیریت سازمان'],

            // ==================== ۲. مدیریت سال‌های مالی ====================
            ['name' => 'fiscal_years.view', 'title' => 'مشاهده سال‌های مالی', 'group' => 'سال‌های مالی'],
            ['name' => 'fiscal_years.create', 'title' => 'ایجاد سال مالی', 'group' => 'سال‌های مالی'],
            ['name' => 'fiscal_years.edit', 'title' => 'ویرایش سال مالی', 'group' => 'سال‌های مالی'],
            ['name' => 'fiscal_years.delete', 'title' => 'حذف سال مالی', 'group' => 'سال‌های مالی'],

            // ==================== ۳. گزارشات و لاگ‌ها ====================
            ['name' => 'activity_logs.view', 'title' => 'مشاهده لاگ فعالیت‌ها', 'group' => 'گزارشات'],

            // ==================== ۴. اشتراک و صورتحساب ====================
            ['name' => 'subscriptions.history', 'title' => 'مشاهده تاریخچه اشتراک', 'group' => 'اشتراک و صورتحساب'],
            ['name' => 'billing.history', 'title' => 'مشاهده تاریخچه اشتراک‌ها', 'group' => 'اشتراک و صورتحساب'],
            ['name' => 'license.view', 'title' => 'مشاهده وضعیت لایسنس', 'group' => 'اشتراک و صورتحساب'],

            // ==================== ۵. مدیریت کاربران (پیشنهادی) ====================
            ['name' => 'users.view', 'title' => 'مشاهده لیست کاربران', 'group' => 'مدیریت کاربران'],
            ['name' => 'users.create', 'title' => 'ایجاد کاربر جدید', 'group' => 'مدیریت کاربران'],
            ['name' => 'users.edit', 'title' => 'ویرایش کاربر', 'group' => 'مدیریت کاربران'],
            ['name' => 'users.delete', 'title' => 'حذف کاربر', 'group' => 'مدیریت کاربران'],
            ['name' => 'users.assign_role', 'title' => 'تخصیص نقش به کاربر', 'group' => 'مدیریت کاربران'],
            ['name' => 'users.import', 'title' => 'ایمپورت کاربران', 'group' => 'مدیریت کاربران'],
            ['name' => 'users.export', 'title' => 'خروجی کاربران', 'group' => 'مدیریت کاربران'],

            // ==================== ۶. مدیریت نقش‌ها (پیشنهادی) ====================
            ['name' => 'roles.view', 'title' => 'مشاهده نقش‌ها', 'group' => 'سطوح دسترسی'],
            ['name' => 'roles.create', 'title' => 'ایجاد نقش', 'group' => 'سطوح دسترسی'],
            ['name' => 'roles.edit', 'title' => 'ویرایش نقش', 'group' => 'سطوح دسترسی'],
            ['name' => 'roles.delete', 'title' => 'حذف نقش', 'group' => 'سطوح دسترسی'],
            ['name' => 'permissions.view', 'title' => 'مشاهده سطوح دسترسی', 'group' => 'سطوح دسترسی'],


            // ==================== ۷. کالا و اقلام ====================
            ['name' => 'products.view',    'title' => 'مشاهده لیست کالاها',            'group' => 'کالا و اقلام'],
            ['name' => 'products.create',  'title' => 'ایجاد کالا',                    'group' => 'کالا و اقلام'],
            ['name' => 'products.edit',    'title' => 'ویرایش کالا',                   'group' => 'کالا و اقلام'],
            ['name' => 'products.delete',  'title' => 'حذف کالا',                      'group' => 'کالا و اقلام'],

            ['name' => 'product-categories.view',   'title' => 'مشاهده دسته‌بندی‌ها',  'group' => 'کالا و اقلام'],
            ['name' => 'product-categories.create', 'title' => 'ایجاد دسته‌بندی',      'group' => 'کالا و اقلام'],
            ['name' => 'product-categories.edit',   'title' => 'ویرایش دسته‌بندی',     'group' => 'کالا و اقلام'],
            ['name' => 'product-categories.delete', 'title' => 'حذف دسته‌بندی',        'group' => 'کالا و اقلام'],

            ['name' => 'measurement-units.view',   'title' => 'مشاهده واحدهای اندازه‌گیری', 'group' => 'کالا و اقلام'],
            ['name' => 'measurement-units.create', 'title' => 'ایجاد واحد اندازه گیری',                'group' => 'کالا و اقلام'],
            ['name' => 'measurement-units.edit',   'title' => 'ویرایش واحد اندازه گیری',                'group' => 'کالا و اقلام'],
            ['name' => 'measurement-units.delete', 'title' => 'حذف واحد اندازه گیری',                  'group' => 'کالا و اقلام'],

            ['name' => 'product-attributes.view',   'title' => 'مشاهده ویژگی‌ها',      'group' => 'کالا و اقلام'],
            ['name' => 'product-attributes.create', 'title' => 'ایجاد ویژگی',          'group' => 'کالا و اقلام'],
            ['name' => 'product-attributes.edit',   'title' => 'ویرایش ویژگی',         'group' => 'کالا و اقلام'],
            ['name' => 'product-attributes.delete', 'title' => 'حذف ویژگی',            'group' => 'کالا و اقلام'],

            // ==================== ۸. انبار و مکان‌ها ====================
            ['name' => 'warehouses.view',   'title' => 'مشاهده انبارها',            'group' => 'انبار و مکان‌ها'],
            ['name' => 'warehouses.create', 'title' => 'ایجاد انبار',               'group' => 'انبار و مکان‌ها'],
            ['name' => 'warehouses.edit',   'title' => 'ویرایش انبار',              'group' => 'انبار و مکان‌ها'],
            ['name' => 'warehouses.delete', 'title' => 'حذف انبار',                 'group' => 'انبار و مکان‌ها'],

            ['name' => 'warehouse-locations.view',   'title' => 'مشاهده موقعیت‌ها',     'group' => 'انبار و مکان‌ها'],
            ['name' => 'warehouse-locations.create', 'title' => 'ایجاد موقعیت',         'group' => 'انبار و مکان‌ها'],
            ['name' => 'warehouse-locations.edit',   'title' => 'ویرایش موقعیت',        'group' => 'انبار و مکان‌ها'],
            ['name' => 'warehouse-locations.delete', 'title' => 'حذف موقعیت',           'group' => 'انبار و مکان‌ها'],

            // ==================== ۹. طرف حساب‌ها ====================
            ['name' => 'contacts.view',   'title' => 'مشاهده مخاطبین',     'group' => 'طرف حساب‌ها'],
            ['name' => 'contacts.create', 'title' => 'ایجاد مخاطب',        'group' => 'طرف حساب‌ها'],
            ['name' => 'contacts.edit',   'title' => 'ویرایش مخاطب',       'group' => 'طرف حساب‌ها'],
            ['name' => 'contacts.delete', 'title' => 'حذف مخاطب',         'group' => 'طرف حساب‌ها'],

            ['name' => 'organizational-units.view',   'title' => 'مشاهده واحدهای سازمانی',   'group' => 'مدیریت واحدهای سازمان'],
            ['name' => 'organizational-units.create', 'title' => 'ایجاد واحد سازمانی',        'group' => 'مدیریت واحدهای سازمان'],
            ['name' => 'organizational-units.edit',   'title' => 'ویرایش واحد سازمانی',       'group' => 'مدیریت واحدهای سازمان'],
            ['name' => 'organizational-units.delete', 'title' => 'حذف واحد سازمانی',          'group' => 'مدیریت واحدهای سازمان'],

            ['name' => 'employees.view',   'title' => 'مشاهده کارمندان',     'group' => 'مدیریت کارمندان'],
            ['name' => 'employees.create', 'title' => 'ایجاد کارمند',        'group' => 'مدیریت کارمندان'],
            ['name' => 'employees.edit',   'title' => 'ویرایش کارمند',       'group' => 'مدیریت کارمندان'],
            ['name' => 'employees.delete', 'title' => 'حذف کارمند',         'group' => 'مدیریت کارمندان'],

            ['name' => 'cost-centers.view',   'title' => 'مشاهده مراکز هزینه',   'group' => 'مراکز هزینه'],
            ['name' => 'cost-centers.create', 'title' => 'ایجاد مرکز هزینه',     'group' => 'مراکز هزینه'],
            ['name' => 'cost-centers.edit',   'title' => 'ویرایش مرکز هزینه',    'group' => 'مراکز هزینه'],
            ['name' => 'cost-centers.delete', 'title' => 'حذف مرکز هزینه',      'group' => 'مراکز هزینه'],


            ['name' => 'product-types.view',   'title' => 'مشاهده انواع کالا',   'group' => 'کالا و اقلام'],
            ['name' => 'product-types.create', 'title' => 'ایجاد نوع کالا',     'group' => 'کالا و اقلام'],
            ['name' => 'product-types.edit',   'title' => 'ویرایش نوع کالا',    'group' => 'کالا و اقلام'],
            ['name' => 'product-types.delete', 'title' => 'حذف نوع کالا',      'group' => 'کالا و اقلام'],

            // ==================== ۱۱. تراکنش‌های انبار ====================
            ['name' => 'stock-transactions.view',    'title' => 'مشاهده تراکنش‌های انبار',      'group' => 'تراکنش‌های انبار'],
            ['name' => 'stock-transactions.create',  'title' => 'ثبت تراکنش انبار جدید',        'group' => 'تراکنش‌های انبار'],
            ['name' => 'stock-transactions.edit',    'title' => 'ویرایش تراکنش انبار',          'group' => 'تراکنش‌های انبار'],
            ['name' => 'stock-transactions.delete',  'title' => 'حذف تراکنش انبار',             'group' => 'تراکنش‌های انبار'],
            ['name' => 'stock-transactions.submit',  'title' => 'ارسال تراکنش برای تأیید',      'group' => 'تراکنش‌های انبار'],
            ['name' => 'stock-transactions.approve', 'title' => 'تأیید / رد تراکنش انبار',      'group' => 'تراکنش‌های انبار'],

            // ==================== ۱۲. موجودی انبار ====================
            ['name' => 'inventory.view', 'title' => 'مشاهده موجودی انبار و کارتکس', 'group' => 'موجودی انبار'],

            // ==================== ۱۳. برندها ====================
            ['name' => 'brands.view',   'title' => 'مشاهده برندها',  'group' => 'کالا و اقلام'],
            ['name' => 'brands.create', 'title' => 'ایجاد برند',     'group' => 'کالا و اقلام'],
            ['name' => 'brands.edit',   'title' => 'ویرایش برند',    'group' => 'کالا و اقلام'],
            ['name' => 'brands.delete', 'title' => 'حذف برند',       'group' => 'کالا و اقلام'],

            // ==================== ۱۴. اسناد انبار ====================
            ['name' => 'warehouse-documents.view',    'title' => 'مشاهده اسناد انبار',          'group' => 'اسناد انبار'],
            ['name' => 'warehouse-documents.create',  'title' => 'ثبت سند انبار جدید',          'group' => 'اسناد انبار'],
            ['name' => 'warehouse-documents.edit',    'title' => 'ویرایش سند انبار (پیش‌نویس)', 'group' => 'اسناد انبار'],
            ['name' => 'warehouse-documents.delete',  'title' => 'حذف سند انبار (پیش‌نویس)',    'group' => 'اسناد انبار'],
            ['name' => 'warehouse-documents.submit',  'title' => 'ارسال سند برای تأیید',        'group' => 'اسناد انبار'],
            ['name' => 'warehouse-documents.approve', 'title' => 'تأیید / رد / لغو سند انبار', 'group' => 'اسناد انبار'],

            // ==================== ۱۵. گزارشات انبار ====================
            ['name' => 'reports.inventory', 'title' => 'گزارش موجودی لحظه‌ای',    'group' => 'گزارشات انبار'],
            ['name' => 'reports.ledger',    'title' => 'کارتکس کالا',             'group' => 'گزارشات انبار'],
            ['name' => 'reports.summary',   'title' => 'خلاصه ورود و خروج',       'group' => 'گزارشات انبار'],

            // ==================== ۱۶. سفارش خرید ====================
            ['name' => 'purchase-orders.view',    'title' => 'مشاهده سفارشات خرید',   'group' => 'سفارش خرید'],
            ['name' => 'purchase-orders.create',  'title' => 'ثبت سفارش خرید جدید',   'group' => 'سفارش خرید'],
            ['name' => 'purchase-orders.edit',    'title' => 'ویرایش سفارش خرید',     'group' => 'سفارش خرید'],
            ['name' => 'purchase-orders.delete',  'title' => 'حذف سفارش خرید',        'group' => 'سفارش خرید'],
            ['name' => 'purchase-orders.confirm', 'title' => 'تأیید / ارسال / بستن',  'group' => 'سفارش خرید'],
            ['name' => 'purchase-orders.receive', 'title' => 'ثبت دریافت کالا',       'group' => 'سفارش خرید'],

            // ==================== ۱۷. درخواست خرید ====================
            ['name' => 'purchase-requests.view',    'title' => 'مشاهده درخواست‌های خرید',   'group' => 'درخواست خرید'],
            ['name' => 'purchase-requests.create',  'title' => 'ثبت درخواست خرید جدید',     'group' => 'درخواست خرید'],
            ['name' => 'purchase-requests.edit',    'title' => 'ویرایش درخواست خرید',       'group' => 'درخواست خرید'],
            ['name' => 'purchase-requests.delete',  'title' => 'حذف درخواست خرید',          'group' => 'درخواست خرید'],
            ['name' => 'purchase-requests.submit',  'title' => 'ارسال درخواست خرید برای تأیید', 'group' => 'درخواست خرید'],
            ['name' => 'purchase-requests.approve', 'title' => 'تأیید / رد درخواست خرید',   'group' => 'درخواست خرید'],
            ['name' => 'purchase-requests.convert', 'title' => 'تبدیل درخواست خرید به PO',  'group' => 'درخواست خرید'],

            // ==================== ۱۸. فاکتور خرید ====================
            ['name' => 'purchase-invoices.view',    'title' => 'مشاهده فاکتورهای خرید',     'group' => 'فاکتور خرید'],
            ['name' => 'purchase-invoices.create',  'title' => 'ثبت فاکتور خرید جدید',      'group' => 'فاکتور خرید'],
            ['name' => 'purchase-invoices.edit',    'title' => 'ویرایش فاکتور خرید',        'group' => 'فاکتور خرید'],
            ['name' => 'purchase-invoices.delete',  'title' => 'حذف فاکتور خرید',           'group' => 'فاکتور خرید'],
            ['name' => 'purchase-invoices.approve', 'title' => 'تأیید / ثبت فاکتور خرید',  'group' => 'فاکتور خرید'],
            ['name' => 'purchase-invoices.pay',     'title' => 'ثبت پرداخت فاکتور',         'group' => 'فاکتور خرید'],

            // ==================== ۱۹. درخواست کالا ====================
            ['name' => 'item-requests.view',    'title' => 'مشاهده درخواست‌های کالا',       'group' => 'درخواست کالا'],
            ['name' => 'item-requests.create',  'title' => 'ثبت درخواست کالا جدید',         'group' => 'درخواست کالا'],
            ['name' => 'item-requests.edit',    'title' => 'ویرایش درخواست کالا',           'group' => 'درخواست کالا'],
            ['name' => 'item-requests.delete',  'title' => 'حذف درخواست کالا',              'group' => 'درخواست کالا'],
            ['name' => 'item-requests.submit',  'title' => 'ارسال درخواست کالا برای تأیید', 'group' => 'درخواست کالا'],
            ['name' => 'item-requests.approve', 'title' => 'تأیید / رد درخواست کالا',      'group' => 'درخواست کالا'],
            ['name' => 'item-requests.dispatch','title' => 'صدور حواله تحویل کالا',         'group' => 'درخواست کالا'],

            // ==================== ۲۰. تنظیمات ====================
            ['name' => 'settings.view',          'title' => 'مشاهده تنظیمات سیستم',              'group' => 'تنظیمات'],
            ['name' => 'settings.edit',          'title' => 'ویرایش تنظیمات سیستم',              'group' => 'تنظیمات'],
            ['name' => 'settings.organization',  'title' => 'تنظیمات سازمان و شرکت',             'group' => 'تنظیمات'],
            ['name' => 'settings.warehouse',     'title' => 'تنظیمات انبار',                     'group' => 'تنظیمات'],
            ['name' => 'settings.workflow',      'title' => 'تنظیمات گردش کار (workflow)',        'group' => 'تنظیمات'],
            ['name' => 'settings.numbering',     'title' => 'تنظیمات شماره‌گذاری اسناد',         'group' => 'تنظیمات'],
            ['name' => 'settings.notifications', 'title' => 'تنظیمات اعلان‌ها و هشدارها',        'group' => 'تنظیمات'],

            // ==================== ۲۱. گزارشات خرید و مدیریتی ====================
            ['name' => 'reports.purchase',          'title' => 'گزارش خلاصه خرید',           'group' => 'گزارشات خرید'],
            ['name' => 'reports.supplier',          'title' => 'گزارش عملکرد تامین‌کنندگان',  'group' => 'گزارشات خرید'],
            ['name' => 'reports.stock-value',       'title' => 'گزارش ارزش موجودی',          'group' => 'گزارشات انبار'],

            // ==================== ۲۲. دارایی ثابت ====================
            ['name' => 'fixed-assets.view',     'title' => 'مشاهده دارایی‌های ثابت',         'group' => 'دارایی ثابت'],
            ['name' => 'fixed-assets.create',   'title' => 'ثبت دارایی ثابت جدید',           'group' => 'دارایی ثابت'],
            ['name' => 'fixed-assets.edit',     'title' => 'ویرایش دارایی ثابت',             'group' => 'دارایی ثابت'],
            ['name' => 'fixed-assets.delete',   'title' => 'حذف دارایی ثابت',                'group' => 'دارایی ثابت'],
            ['name' => 'fixed-assets.assign',   'title' => 'تخصیص دارایی به پرسنل / عودت',  'group' => 'دارایی ثابت'],
            ['name' => 'fixed-assets.maintain', 'title' => 'ثبت تعمیر و نگهداری دارایی',    'group' => 'دارایی ثابت'],
            ['name' => 'fixed-assets.scrap',    'title' => 'اسقاط و خروج از خدمت دارایی',   'group' => 'دارایی ثابت'],
        ];


        foreach ($permissions as $perm) {
            Permission::firstOrCreate(
                ['name' => $perm['name']],
                [
                    'title' => $perm['title'],
                    'group' => $perm['group'],
                    'is_active' => true,
                ]
            );
        }
    }
}
