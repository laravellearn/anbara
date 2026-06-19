<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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
                    'max_users' => null,
                    'max_warehouses' => null,
                    'max_invoices_month' => null,
                    'max_employees' => null,
                    'max_organizations' => 1,
                    'storage_mb' => 200,
                    'support' => 'ticket',
                ]),
                'features' => json_encode([
                    'core_inventory',
                    'invoices',
                    'havale',
                    'advanced_reports',
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
                    'max_employees' => 5,
                    'max_invoices_month' => 30,
                    'max_organizations' => 1,
                    'storage_mb' => 50,
                    'support' => 'ticket',
                ]),
                'features' => json_encode([
                    'core_inventory',
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
                    'max_employees' => 30,
                    'max_warehouses' => 3,
                    'max_invoices_month' => 200,
                    'max_organizations' => 3,
                    'storage_mb' => 200,
                    'support' => 'priority',
                ]),
                'features' => json_encode([
                    'core_inventory',
                    'invoices',
                    'havale',
                    'basic_reports',
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
                    'max_employees' => 100,
                    'max_warehouses' => 10,
                    'max_invoices_month' => 1000,
                    'max_organizations' => 10,
                    'storage_mb' => 500,
                    'support' => 'priority',
                ]),
                'features' => json_encode([
                    'core_inventory',
                    'invoices',
                    'havale',
                    'advanced_reports',
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
                    'max_employees' => null,
                    'max_warehouses' => null,
                    'max_invoices_month' => null,
                    'max_organizations' => null,
                    'storage_mb' => 2048,
                    'support' => 'dedicated',
                ]),
                'features' => json_encode([
                    'core_inventory',
                    'invoices',
                    'havale',
                    'advanced_reports',
                    'notifications',
                    'api',
                    'white_label',
                ]),
                'is_active' => true,
                'sort_order' => 5,
            ],
        ]);
    }
}
