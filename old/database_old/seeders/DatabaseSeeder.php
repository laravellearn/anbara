<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\Permission;
use App\Models\Plan;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        DB::table('permissions')->insert(
            [
                [
                    'id' => 1,
                    'title' => 'brands',
                    'description' => 'برند ها',
                    'isActive' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'id' => 2,
                    'title' => 'units',
                    'description' => 'واحد ها',
                    'isActive' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'id' => 3,
                    'title' => 'categories',
                    'description' => 'دسته بندی ها',
                    'isActive' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'id' => 4,
                    'title' => 'stores',
                    'description' => 'انبار ها',
                    'isActive' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'id' => 5,
                    'title' => 'permissions',
                    'description' => 'سطح دسترسی ها',
                    'isActive' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'id' => 6,
                    'title' => 'roles',
                    'description' => 'نقش ها',
                    'isActive' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'id' => 7,
                    'title' => 'users',
                    'description' => 'کاربران سیستم',
                    'isActive' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'id' => 8,
                    'title' => 'employees',
                    'description' => 'پرسنل',
                    'isActive' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'id' => 9,
                    'title' => 'entities',
                    'description' => 'موجودی ها',
                    'isActive' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'id' => 10,
                    'title' => 'end-product',
                    'description' => 'کالاهای تمام شده',
                    'isActive' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'id' => 11,
                    'title' => 'deliveries',
                    'description' => 'تحویل کالا',
                    'isActive' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'id' => 12,
                    'title' => 'stocks',
                    'description' => 'مدیریت کالای دست دوم',
                    'isActive' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ],

                [
                    'id' => 13,
                    'title' => 'invoices',
                    'description' => 'لیست فاکتور',
                    'isActive' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'id' => 14,
                    'title' => 'invoice-add',
                    'description' => 'ثبت فاکتور جدید',
                    'isActive' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'id' => 15,
                    'title' => 'invoice-product-list',
                    'description' => 'لیست اقلام خریداری شده',
                    'isActive' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'id' => 16,
                    'title' => 'product-add',
                    'description' => 'ثبت کالای جدید',
                    'isActive' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'id' => 17,
                    'title' => 'products',
                    'description' => 'لیست کالا ها',
                    'isActive' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'id' => 18,
                    'title' => 'organizations',
                    'description' => 'تعریف سازمان',
                    'isActive' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'id' => 19,
                    'title' => 'logs',
                    'description' => 'گزارشات سیستم',
                    'isActive' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'id' => 20,
                    'title' => 'abortions',
                    'description' => 'کالاهای اسقاطی',
                    'isActive' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'id' => 21,
                    'title' => 'transfers',
                    'description' => 'انتقال انبار به انبار',
                    'isActive' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'id' => 22,
                    'title' => 'reports',
                    'description' => 'گزارش مدیر',
                    'isActive' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'id' => 23,
                    'title' => 'histories',
                    'description' => 'تاریخچه موجودی ها',
                    'isActive' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'id' => 24,
                    'title' => 'repairs',
                    'description' => 'بخش تعمیرات',
                    'isActive' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'id' => 25,
                    'title' => 'settings',
                    'description' => 'بخش تنظیمات',
                    'isActive' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'id' => 26,
                    'title' => 'report-warehouse',
                    'description' => 'گزارش جامع انبار',
                    'isActive' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'id' => 27,
                    'title' => 'suppliers',
                    'description' => 'تامین کنندگان',
                    'isActive' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'id' => 28,
                    'title' => 'measures',
                    'description' => 'واحد شمارشی کالا',
                    'isActive' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'id' => 29,
                    'title' => 'havale',
                    'description' => 'حواله های ورود و خروج',
                    'isActive' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'id' => 30,
                    'title' => 'havale-report',
                    'description' => 'گزارش حواله ها',
                    'isActive' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'id' => 31,
                    'title' => 'havale-edit',
                    'description' => 'ویرایش حواله ها',
                    'isActive' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'id' => 32,
                    'title' => 'havale-delete',
                    'description' => 'حذف حواله ها',
                    'isActive' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ],


            ]

        );

        $tenant = Tenant::firstOrCreate(
            ['slug' => 'default-tenant'], // شرط یکتا
            [
                'name' => 'شرکت پیش‌ فرض',
                'email' => 'info@default-company.com',
                'phone' => '02112345678',
                'address' => 'شیراز، خیابان اصلی',
                'subscription_status' => 'active',
                'trial_ends_at' => now()->addYears(10), // عملاً بی‌نهایت
                'settings' => json_encode([
                    'locale' => 'fa',
                    'timezone' => 'Asia/Tehran',
                    'currency' => 'IRR',
                    'date_format' => 'jYYYY/jMM/jDD',
                ]),
            ]
        );


        $tenant2 = Tenant::firstOrCreate(
            ['slug' => 'second-tenant'], // شرط یکتا
            [
                'name' => 'شرکت پیش‌ فرض 2',
                'email' => 'info@second-company.com',
                'phone' => '02112345678',
                'address' => 'شیراز، خیابان فرعی',
                'subscription_status' => 'active',
                'trial_ends_at' => now()->addYears(10), // عملاً بی‌نهایت
                'settings' => json_encode([
                    'locale' => 'fa',
                    'timezone' => 'Asia/Tehran',
                    'currency' => 'IRR',
                    'date_format' => 'jYYYY/jMM/jDD',
                ]),
            ]
        );

        $organization = Organization::firstOrCreate(
            [
                'tenant_id' => $tenant->id,
                'parent_id' => null, // ریشه
                'title' => 'دفتر مرکزی',
            ],
            [] // سایر فیلدها در صورت نیاز
        );

        // ۳. ایجاد نقش‌های پیش‌فرض (admin و operator) مختص این Tenant
        $adminRole = Role::firstOrCreate(
            [
                'id' => 1,
                'title' => 'admin',
                'tenant_id' => $tenant->id,
            ],
            [
                'description' => 'دسترسی کامل به تمام بخش‌ها',
            ]
        );

        $operatorRole = Role::firstOrCreate(
            [
                'id' => 2,
                'title' => 'operator',
                'tenant_id' => $tenant->id,
            ],
            [
                'description' => 'دسترسی محدود به ثبت و مشاهده',
            ]
        );


        // ۴. اختصاص تمام مجوزهای موجود به نقش admin
        $allPermissions = Permission::pluck('id')->toArray();
        if (!empty($allPermissions)) {
            $adminRole->permissions()->sync($allPermissions);
        }

        DB::table('permission_role')->insert(
            [
                [
                    'permission_id' => 11,
                    'role_id' => 2,
                ],
                [
                    'permission_id' => 2,
                    'role_id' => 2,
                ],
                [
                    'permission_id' => 8,
                    'role_id' => 2,
                ],

                [
                    'permission_id' => 12,
                    'role_id' => 2,
                ],

                [
                    'permission_id' => 13,
                    'role_id' => 2,
                ],

                [
                    'permission_id' => 14,
                    'role_id' => 2,
                ],

                [
                    'permission_id' => 15,
                    'role_id' => 2,
                ],

                [
                    'permission_id' => 16,
                    'role_id' => 2,
                ],

                [
                    'permission_id' => 17,
                    'role_id' => 2,
                ],

                [
                    'permission_id' => 20,
                    'role_id' => 2,
                ],
                [
                    'permission_id' => 21,
                    'role_id' => 2,
                ],
                [
                    'permission_id' => 24,
                    'role_id' => 2,
                ],
                [
                    'permission_id' => 27,
                    'role_id' => 2,
                ],
                [
                    'permission_id' => 28,
                    'role_id' => 2,
                ],
                [
                    'permission_id' => 29,
                    'role_id' => 2,
                ],
                [
                    'permission_id' => 30,
                    'role_id' => 2,
                ],

            ]
        );


        // ۵. ایجاد کاربر مدیر پیش‌فرض
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'tenant_id' => $tenant->id,
                // سایر فیلدها در صورت نیاز
            ]
        );

        // ۵. ایجاد کاربر مدیر پیش‌فرض
        $operatorUser = User::firstOrCreate(
            ['email' => 'operator@example.com'],
            [
                'name' => 'Operator',
                'password' => Hash::make('password'),
                'tenant_id' => $tenant->id,
                // سایر فیلدها در صورت نیاز
            ]
        );


        // ۵. ایجاد کاربر مدیر پیش‌فرض
        $operatorUser2 = User::firstOrCreate(
            ['email' => 'operator2@example.com'],
            [
                'name' => 'Operator2',
                'password' => Hash::make('password'),
                'tenant_id' => $tenant2->id,
                // سایر فیلدها در صورت نیاز
            ]
        );


        // ۶. اتصال کاربر به سازمان با نقش admin (در جدول organization_user)
        if (!$adminUser->organizations()->where('organization_id', $organization->id)->exists()) {
            $adminUser->organizations()->attach($organization->id, ['role_id' => $adminRole->id]);
        }

        DB::table('role_user')->insert(
            [
                [
                    'role_id' => 1,
                    'user_id' => 1,
                ]

            ]
        );




        DB::table('settings')->insert(
            [
                [
                    'title' => 'sms',
                    'value' => 'no',
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'title' => 'sms-panel',
                    'value' => 'ippanel',
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'title' => 'sms-user',
                    'value' => '',
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'title' => 'sms-password',
                    'value' => '',
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'title' => 'logo',
                    'value' => '/img/core-img/logo.png',
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'title' => 'user-register',
                    'value' => 'yes',
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'title' => 'login-banner',
                    'value' => '/img/bg-img/1.png',
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'title' => 'payment',
                    'value' => 'zarinpal',
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'title' => 'merchant-code',
                    'value' => '',
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'title' => 'fav',
                    'value' => '/img/core-img/favicon.png',
                    'created_at' => now(),
                    'updated_at' => now()
                ],
            ]
        );



        // نقش سوپرادمین (بدون tenant_id)
        $superAdminRole = Role::firstOrCreate(
            [
                'title' => 'super_admin',
                'tenant_id' => null, // مهم: متعلق به هیچ مستأجری نیست
            ],
            [
                'description' => 'سوپر ادمین',
            ]
        );

        // اختصاص همه مجوزها به سوپرادمین
        $allPermissions = Permission::pluck('id')->toArray();
        if (!empty($allPermissions)) {
            $superAdminRole->permissions()->sync($allPermissions);
        }

        // ایجاد کاربر سوپرادمین (اگر وجود نداشته باشد)
        $superUser = User::firstOrCreate(
            ['email' => 'super@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'tenant_id' => null, // کاربر هم متعلق به هیچ مستأجری نیست
            ]
        );

        // اتصال نقش سوپرادمین به کاربر (از طریق role_user)
// جدول role_user باید tenant_id نداشته باشد یا nullable باشد؛ 
// اگر قبلاً در role_user فیلد tenant_id دارید، آن را هم nullable کنید.
        if (!$superUser->roles()->where('role_id', $superAdminRole->id)->exists()) {
            $superUser->roles()->attach($superAdminRole->id);
        }



        //TODO
        //پلن ها باید حرفه ای چیده شود
        Plan::insert([
            [
                'name' => 'پایه (رایگان)',
                'slug' => 'basic',
                'description' => 'مناسب استارت‌آپ‌ها و کسب‌وکارهای کوچک',
                'price' => 0,
                'currency' => 'IRT',
                'duration_days' => null,  // مدت‌دار نیست (تا وقتی کنسل نکنی)
                'limits' => json_encode([
                    'max_products' => 100,    // مجموع کالاها (عادی، دست‌دوم، اسقاطی)
                    'max_users' => 1,      // فقط یک کاربر
                    'max_warehouses' => 1,      // فقط یک انبار
                    'max_invoices_month' => 30,     // ۳۰ فاکتور در ماه
                    'max_organizations' => 1,      // بدون شعب
                    'storage_mb' => 50,      // فضای آپلود تصاویر
                    'support' => 'ticket', // پشتیبانی از طریق تیکت
                    'custom_domain' => false,
                    'export_excel' => true,
                    'export_pdf' => false,
                ]),
                'features' => json_encode([
                    'core_inventory',   // موجودی پایه، ثبت کالا، دسته‌بندی، برند
                    'invoices',         // فاکتور فروش/خرید
                    'havale',           // حواله ورود و خروج
                    'basic_reports',    // گزارش‌های ساده
                    'repairs' => false,  // بخش تعمیرات ندارد
                    'scrap' => false,   // کالاهای اسقاطی ندارد
                    'api' => false,
                ]),
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'حرفه‌ای',
                'slug' => 'pro',
                'description' => 'مناسب شرکت‌های در حال رشد',
                'price' => 390000,
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
                ]),
                'features' => json_encode([
                    'core_inventory',
                    'invoices',
                    'havale',
                    'advanced_reports',   // گزارش‌های پیشرفته (کارتکس، گردش کالا، ارزش موجودی)
                    'repairs' => true,
                    'scrap' => true,
                    'notifications' => true, // اعلان حد سفارش
                    'api' => false,
                ]),
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'تجاری',
                'slug' => 'business',
                'description' => 'مناسب کسب‌وکارهای متوسط با چند شعبه',
                'price' => 990000,
                'currency' => 'IRT',
                'duration_days' => 30,
                'limits' => json_encode([
                    'max_products' => 5000,
                    'max_users' => 15,
                    'max_warehouses' => 10,
                    'max_invoices_month' => 1000,
                    'max_organizations' => 10,    // تا ۱۰ شعبه
                    'storage_mb' => 500,
                    'support' => 'priority',
                    'custom_domain' => true,
                    'export_excel' => true,
                    'export_pdf' => true,
                ]),
                'features' => json_encode([
                    'core_inventory',
                    'invoices',
                    'havale',
                    'advanced_reports',
                    'repairs',
                    'scrap',
                    'notifications',
                    'api' => true,    // دسترسی به API
                ]),
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'سازمانی',
                'slug' => 'enterprise',
                'description' => 'برای هلدینگ‌ها و سازمان‌های بزرگ؛ بدون محدودیت',
                'price' => 1890000,
                'currency' => 'IRT',
                'duration_days' => 30,
                'limits' => json_encode([
                    'max_products' => null,  // نامحدود
                    'max_users' => null,
                    'max_warehouses' => null,
                    'max_invoices_month' => null,
                    'max_organizations' => null,
                    'storage_mb' => 2048,
                    'support' => 'dedicated', // پشتیبانی اختصاصی
                    'custom_domain' => true,
                    'export_excel' => true,
                    'export_pdf' => true,
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
                    'white_label',    // حذف برند الماس
                    'dedicated_server' => false, // فعلاً نه
                ]),
                'is_active' => true,
                'sort_order' => 4,
            ],
        ]);

        $this->command->info(' با موفقیت اجرا شد.');
        $this->command->info('ایمیل: admin@example.com');
        $this->command->info('رمز عبور: password');
        $this->command->info('-------------------');
        $this->command->info('ایمیل: super@example.com');
        $this->command->info('رمز عبور: password');
        $this->command->info('-------------------');
        $this->command->info('ایمیل: operator@example.com');
        $this->command->info('رمز عبور: password');
        $this->command->info('-------------------');
        $this->command->info('ایمیل: operator2@example.com');
        $this->command->info('رمز عبور: password');


    }
}
