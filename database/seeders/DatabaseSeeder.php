<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $mobile = env('SUPER_ADMIN_MOBILE', '09171063364');
        $password = env('SUPER_ADMIN_PASSWORD', 'password');

        // جلوگیری از ایجاد تکراری
        if (User::where('mobile', $mobile)->exists()) {
            $this->command->warn("کاربر با موبایل {$mobile} از قبل وجود دارد.");
            return;
        }

        User::create([
            'name' => 'مدیر کل',
            'mobile' => $mobile,
            'mobile_verified_at' => now(),
            'password' => Hash::make($password),
            'is_active' => true,  // اگر از فیلد boolean استفاده می‌کنی
            // 'role' => 'super_admin',   // اگر از فیلد رشته‌ای استفاده می‌کنی
        ]);

        $this->command->info("✅ سوپر ادمین با موبایل {$mobile} و رمز عبور {$password} ایجاد شد.");


        //TODO
        //پلن ها باید حرفه ای چیده شود
        //Plan Seeder
        Plan::insert([
            [
                'name' => 'نسخه آزمایشی',
                'code' => 'trial',
                'slug' => 'trial',
                'description' => 'نسخه نامحدود 14 روزه',
                'monthly_price' => 0,
                'yearly_price' => 0,
                'currency' => 'IRT',
                'duration_days' => 14,  // مدت‌دار نیست (تا وقتی کنسل نکنی)
                'limits' => json_encode([
                    'max_products' => null,
                    'max_users' => 5,
                    'max_warehouses' => 5,
                    'max_invoices_month' => null,
                    'max_organizations' => 3,
                    'storage_mb' => 200,
                    'support' => 'ticket',
                    'custom_domain' => false,
                    'export_excel' => true,
                    'export_pdf' => true,
                    'max_categories' => null,
                    'max_brands' => null,
                    'max_suppliers' => 500,
                    'max_product_images' => 5,
                    'max_stock_locations' => 50,
                    'max_import_rows' => 1000,
                    'activity_log_days' => 30,
                    'max_organizational_units_per_org' => 5,
                ]),
                'features' => json_encode([
                    'core_inventory',
                    'invoices',
                    'havale',
                    'advanced_reports',
                    'repairs',
                    'scrap',
                    'notifications',
                    'api',
                ]),
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'نسخه پایه',
                'code' => 'basic',
                'slug' => 'basic',
                'description' => 'مناسب استارت‌آپ‌ها و کسب‌وکارهای کوچک',
                'monthly_price' => 0,
                'yearly_price' => 0,
                'currency' => 'IRT',
                'duration_days' => null,  // مدت‌دار نیست (تا وقتی کنسل نکنی)
                'limits' => json_encode([
                    'max_products' => 100,
                    'max_users' => 1,
                    'max_warehouses' => 1,
                    'max_invoices_month' => 30,
                    'max_organizations' => 1,
                    'storage_mb' => 50,
                    'support' => 'ticket',
                    'custom_domain' => false,
                    'export_excel' => true,
                    'export_pdf' => false,
                    'max_categories' => 20,
                    'max_brands' => 10,
                    'max_suppliers' => 50,
                    'max_product_images' => 2,
                    'max_stock_locations' => 10,
                    'max_import_rows' => 100,
                    'activity_log_days' => 7,
                    'max_organizational_units_per_org' => 1,
                ]),
                'features' => json_encode([
                    'core_inventory',
                    'invoices',
                    'havale',
                    'basic_reports',
                ]),
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'نسخه حرفه ای',
                'code' => 'pro',
                'slug' => 'pro',
                'description' => 'مناسب شرکت‌های در حال رشد',
                'monthly_price' => 390000,
                'yearly_price' => 3900000,
                'currency' => 'IRT',
                'duration_days' => 30,
                'limits' => json_encode([
                    'max_products' => 1000,
                    'max_users' => 5,
                    'max_warehouses' => 3,
                    'max_invoices_month' => 200,
                    'max_organizations' => 3,
                    'storage_mb' => 200,
                    'support' => 'priority',
                    'custom_domain' => false,
                    'export_excel' => true,
                    'export_pdf' => true,
                    'max_categories' => 50,
                    'max_brands' => 30,
                    'max_suppliers' => 200,
                    'max_product_images' => 4,
                    'max_stock_locations' => 30,
                    'max_import_rows' => 500,
                    'activity_log_days' => 30,
                    'max_organizational_units_per_org' => 3,
                ]),
                'features' => json_encode([
                    'core_inventory',
                    'invoices',
                    'havale',
                    'advanced_reports',
                    'repairs',
                    'scrap',
                    'notifications',
                ]),
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'نسخه تجاری',
                'code' => 'business',
                'slug' => 'business',
                'description' => 'مناسب کسب‌وکارهای متوسط با چند شعبه',
                'monthly_price' => 990000,
                'yearly_price' => 9900000,
                'currency' => 'IRT',
                'duration_days' => 30,
                'limits' => json_encode([
                    'max_products' => 5000,
                    'max_users' => 15,
                    'max_warehouses' => 10,
                    'max_invoices_month' => 1000,
                    'max_organizations' => 10,
                    'storage_mb' => 500,
                    'support' => 'priority',
                    'custom_domain' => true,
                    'export_excel' => true,
                    'export_pdf' => true,
                    'max_categories' => 200,
                    'max_brands' => 100,
                    'max_suppliers' => 1000,
                    'max_product_images' => 6,
                    'max_stock_locations' => 100,
                    'max_import_rows' => 2000,
                    'activity_log_days' => 90,
                    'max_organizational_units_per_org' => 10,
                ]),
                'features' => json_encode([
                    'core_inventory',
                    'invoices',
                    'havale',
                    'advanced_reports',
                    'repairs',
                    'scrap',
                    'notifications',
                    'api',
                ]),
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'نسخه سازمانی',
                'code' => 'enterprise',
                'slug' => 'enterprise',
                'description' => 'برای هلدینگ‌ها و سازمان‌های بزرگ؛ بدون محدودیت',
                'monthly_price' => 1890000,
                'yearly_price' => 18900000,
                'currency' => 'IRT',
                'duration_days' => 30,
                'limits' => json_encode([
                    'max_products' => null,
                    'max_users' => null,
                    'max_warehouses' => null,
                    'max_invoices_month' => null,
                    'max_organizations' => null,
                    'storage_mb' => 2048,
                    'support' => 'dedicated',
                    'custom_domain' => true,
                    'export_excel' => true,
                    'export_pdf' => true,
                    'max_categories' => null,
                    'max_brands' => null,
                    'max_suppliers' => null,
                    'max_product_images' => 10,
                    'max_stock_locations' => null,
                    'max_import_rows' => null,
                    'activity_log_days' => 365,
                    'max_organizational_units_per_org' => null,
                ]),
                'features' => json_encode([
                    'core_inventory',
                    'invoices',
                    'havale',
                    'advanced_reports',
                    'repairs',
                    'scrap',
                    'notifications',
                    'api',
                    'white_label',
                ]),
                'is_active' => true,
                'sort_order' => 5,
            ],
        ]);


        //Permissions

        $permissions = [

            // ==================== ۱. مدیریت سازمان‌ها ====================
            ['name' => 'companies.view', 'title' => 'مشاهده لیست سازمان‌ها', 'group' => 'سازمان‌ها'],
            ['name' => 'companies.create', 'title' => 'ایجاد سازمان جدید', 'group' => 'سازمان‌ها'],
            ['name' => 'companies.edit', 'title' => 'ویرایش سازمان', 'group' => 'سازمان‌ها'],
            ['name' => 'companies.delete', 'title' => 'حذف سازمان', 'group' => 'سازمان‌ها'],

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
