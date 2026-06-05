<?php

return [

    /*
    |--------------------------------------------------------------------------
    | داشبورد
    |--------------------------------------------------------------------------
    */
    [
        'title' => 'داشبوردها',
        'icon' => 'bx-home-circle',
        'children' => [
            [
                'title' => 'داشبورد اصلی',
                'route' => 'dashboard',
                'permission' => null,
            ],
            [
                'title' => 'شاخص‌های کلیدی',
                'route' => 'dashboard.kpi',
                'permission' => null,
            ],
            [
                'title' => 'هشدارها',
                'route' => 'dashboard.alerts',
                'permission' => null,
            ],
            [
                'title' => 'فعالیت‌های اخیر',
                'route' => 'dashboard.activities',
                'permission' => null,
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | کالا و اقلام
    |--------------------------------------------------------------------------
    */
    [
        'title' => 'کالا و اقلام',
        'icon' => 'bx-package',
        'children' => [

            [
                'title' => 'کالاها',
                'route' => 'items.index',
                'permission' => 'items.view',
            ],
            [
                'title' => 'دسته‌بندی کالا',
                'route' => 'categories.index',
                'permission' => 'categories.view',
            ],
            [
                'title' => 'واحدهای اندازه‌گیری',
                'route' => 'units.index',
                'permission' => 'units.view',
            ],

            [
                'title' => 'بارکدها',
                'route' => 'barcodes.index',
                'permission' => 'items.view',
            ],
            [
                'title' => 'ویژگی‌های کالا',
                'route' => 'item.attributes',
                'permission' => 'items.view',
            ],
            [
                'title' => 'فایل‌ها و مستندات',
                'route' => 'item.documents',
                'permission' => 'items.view',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | انبارها
    |--------------------------------------------------------------------------
    */
    [
        'title' => 'انبارها',
        'icon' => 'bx-store',
        'children' => [

            [
                'title' => 'انبارها',
                'route' => 'warehouses.index',
                'permission' => 'warehouses.view',
            ],
            [
                'title' => 'بخش‌ها و قفسه‌ها',
                'route' => 'warehouse.sections',
                'permission' => 'warehouses.view',
            ],
            [
                'title' => 'موقعیت‌های نگهداری',
                'route' => 'warehouse.locations',
                'permission' => 'warehouses.view',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | مدیریت ظرفیت
    |--------------------------------------------------------------------------
    */
    [
        'title' => 'مدیریت ظرفیت',
        'icon' => 'bx-pie-chart-alt',
        'children' => [
            [
                'title' => 'وضعیت اشغال',
                'route' => 'capacity.occupancy',
                'permission' => 'warehouses.view',
            ],
            [
                'title' => 'ظرفیت انبار',
                'route' => 'capacity.total',
                'permission' => 'warehouses.view',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | عملیات انبار
    |--------------------------------------------------------------------------
    */
    [
        'title' => 'عملیات انبار',
        'icon' => 'bx-transfer',
        'children' => [

            [
                'title' => 'رسید انبار',
                'route' => 'stock.receipts',
                'permission' => 'stock.in',
            ],
            [
                'title' => 'ورود خرید',
                'route' => 'stock.purchase',
                'permission' => 'stock.in',
            ],
            [
                'title' => 'ورود امانی',
                'route' => 'stock.consignment.in',
                'permission' => 'stock.in',
            ],
            [
                'title' => 'ورود انتقالی',
                'route' => 'stock.transfer.in',
                'permission' => 'stock.in',
            ],

            [
                'title' => 'حواله خروج',
                'route' => 'stock.outbound',
                'permission' => 'stock.out',
            ],
            [
                'title' => 'خروج مصرفی',
                'route' => 'stock.consume',
                'permission' => 'stock.out',
            ],
            [
                'title' => 'خروج امانی',
                'route' => 'stock.consignment.out',
                'permission' => 'stock.out',
            ],
            [
                'title' => 'خروج انتقالی',
                'route' => 'stock.transfer.out',
                'permission' => 'stock.out',
            ],

            [
                'title' => 'انتقال بین انبارها',
                'route' => 'stock.transfer.warehouse',
                'permission' => 'stock.transfer',
            ],
            [
                'title' => 'انتقال داخلی',
                'route' => 'stock.transfer.internal',
                'permission' => 'stock.transfer',
            ],

            [
                'title' => 'اصلاح موجودی',
                'route' => 'stock.adjustment',
                'permission' => 'stock.adjust',
            ],
            [
                'title' => 'ثبت ضایعات',
                'route' => 'stock.scrap',
                'permission' => 'stock.adjust',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | درخواست‌ها
    |--------------------------------------------------------------------------
    */
    [
        'title' => 'درخواست‌ها',
        'icon' => 'bx-receipt',
        'children' => [

            [
                'title' => 'درخواست جدید',
                'route' => 'requests.create',
                'permission' => 'requests.create',
            ],
            [
                'title' => 'تایید درخواست‌ها',
                'route' => 'requests.approval',
                'permission' => 'requests.approve',
            ],
            [
                'title' => 'پیگیری درخواست‌ها',
                'route' => 'requests.track',
                'permission' => 'requests.view',
            ],
        ],
    ],
/*
|--------------------------------------------------------------------------
| انبارگردانی
|--------------------------------------------------------------------------
*/
[
    'title' => 'انبارگردانی',
    'icon' => 'bx-clipboard',
    'children' => [

        [
            'title' => 'دوره‌های انبارگردانی',
            'route' => 'stocktakes.index',
            'permission' => 'stocktakes.view',
        ],

        [
            'title' => 'شمارش موجودی',
            'route' => 'stocktakes.counts',
            'permission' => 'stocktakes.count',
        ],

        [
            'title' => 'مغایرت‌ها',
            'route' => 'stocktakes.discrepancies',
            'permission' => 'stocktakes.discrepancies',
        ],

        [
            'title' => 'گزارش انبارگردانی',
            'route' => 'stocktakes.reports',
            'permission' => 'stocktakes.report',
        ],

    ],
],
    /*
    |--------------------------------------------------------------------------
    | موجودی
    |--------------------------------------------------------------------------
    */
    [
        'title' => 'موجودی',
        'icon' => 'bx-layer',
        'children' => [

            [
                'title' => 'موجودی لحظه‌ای',
                'route' => 'inventory.current',
                'permission' => 'inventory.view',
            ],
            [
                'title' => 'موجودی رزرو شده',
                'route' => 'inventory.reserved',
                'permission' => 'inventory.view',
            ],
            [
                'title' => 'موجودی در گردش',
                'route' => 'inventory.movement',
                'permission' => 'inventory.view',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | هشدارها
    |--------------------------------------------------------------------------
    */
    [
        'title' => 'هشدارها',
        'icon' => 'bx-bell',
        'children' => [

            [
                'title' => 'حداقل موجودی',
                'route' => 'alerts.min-stock',
                'permission' => 'alerts.view',
            ],
            [
                'title' => 'نقطه سفارش',
                'route' => 'alerts.reorder',
                'permission' => 'alerts.view',
            ],
            [
                'title' => 'کالاهای راکد',
                'route' => 'alerts.idle-items',
                'permission' => 'alerts.view',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | دارایی‌ها
    |--------------------------------------------------------------------------
    */
    [
        'title' => 'دارایی‌ها',
        'icon' => 'bx-briefcase',
        'children' => [

            [
                'title' => 'لیست دارایی',
                'route' => 'assets.index',
                'permission' => 'assets.view',
            ],
            [
                'title' => 'ثبت دارایی',
                'route' => 'assets.create',
                'permission' => 'assets.create',
            ],
            [
                'title' => 'تخصیص دارایی',
                'route' => 'assets.assign',
                'permission' => 'assets.assign',
            ],
            [
                'title' => 'عودت دارایی',
                'route' => 'assets.return',
                'permission' => 'assets.assign',
            ],
            [
                'title' => 'درخواست تعمیر',
                'route' => 'assets.repair.request',
                'permission' => 'assets.repair',
            ],
            [
                'title' => 'سوابق تعمیر',
                'route' => 'assets.repair.history',
                'permission' => 'assets.repair',
            ],
            [
                'title' => 'اسقاط دارایی',
                'route' => 'assets.scrap',
                'permission' => 'assets.scrap',
            ],
        ],
    ],


    
    /*
    |--------------------------------------------------------------------------
    | طرف حساب‌ها
    |--------------------------------------------------------------------------
    */
    [
        'title' => 'طرف حساب‌ها',
        'icon' => 'bx-group',
        'children' => [

            [
                'title' => 'تامین‌کنندگان',
                'route' => 'suppliers.index',
                'permission' => 'suppliers.view',
            ],
            [
                'title' => 'دپارتمان‌ها',
                'route' => 'departments.index',
                'permission' => 'departments.view',
            ],
            [
                'title' => 'پرسنل دریافت‌کننده',
                'route' => 'receivers.index',
                'permission' => 'receivers.view',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | گزارشات
    |--------------------------------------------------------------------------
    */
    [
        'title' => 'گزارشات',
        'icon' => 'bx-bar-chart-alt-2',
        'children' => [

            [
                'title' => 'موجودی',
                'route' => 'reports.inventory',
                'permission' => 'reports.view',
            ],
            [
                'title' => 'گردش کالا',
                'route' => 'reports.movement',
                'permission' => 'reports.view',
            ],
            [
                'title' => 'ورود و خروج',
                'route' => 'reports.inout',
                'permission' => 'reports.view',
            ],
            [
                'title' => 'عملکرد انبارها',
                'route' => 'reports.warehouse.performance',
                'permission' => 'reports.view',
            ],
            [
                'title' => 'موجودی انبارها',
                'route' => 'reports.warehouse.stock',
                'permission' => 'reports.view',
            ],
            [
                'title' => 'گزارش مدیریتی',
                'route' => 'reports.management',
                'permission' => 'reports.view',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | کاربران و دسترسی
    |--------------------------------------------------------------------------
    */
    [
        'title' => 'کاربران و دسترسی',
        'icon' => 'bx-user',
        'children' => [

            [
                'title' => 'کاربران سیستم',
                'route' => 'users.index',
                'permission' => 'users.view',
            ],
            [
                'title' => 'نقش‌ها',
                'route' => 'roles.index',
                'permission' => 'roles.view',
            ],
            [
                'title' => 'سطوح دسترسی',
                'route' => 'permissions.index',
                'permission' => 'permissions.view',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | تنظیمات
    |--------------------------------------------------------------------------
    */
    [
        'title' => 'تنظیمات',
        'icon' => 'bx-cog',
        'children' => [

            [
                'title' => 'اشتراک‌ها',
                'route' => 'settings.subscriptions',
                'permission' => 'settings.view',
            ],
            [
                'title' => 'تنظیمات سامانه',
                'route' => 'settings.system',
                'permission' => 'settings.view',
            ],
            [
                'title' => 'تنظیمات سازمان',
                'route' => 'settings.organization',
                'permission' => 'settings.view',
            ],
            [
                'title' => 'تنظیمات انبار',
                'route' => 'settings.warehouse',
                'permission' => 'settings.view',
            ],
            [
                'title' => 'شماره‌گذاری اسناد',
                'route' => 'settings.numbering',
                'permission' => 'settings.view',
            ],
            [
                'title' => 'قالب کد کالا',
                'route' => 'settings.item-code',
                'permission' => 'settings.view',
            ],
            [
                'title' => 'اعلان‌ها',
                'route' => 'settings.notifications',
                'permission' => 'settings.view',
            ],
            [
                'title' => 'لاگ‌ها',
                'route' => 'logs.index',
                'permission' => 'logs.view',
            ],
        ],
    ],

];