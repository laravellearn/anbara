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
