<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class WorldLocationSeeder extends Seeder
{
    /**
     * مسیر پوشه‌ای که فایل‌های SQL در آن قرار دارند.
     */
    protected string $sqlPath;

    /**
     * پیشوند نام دیتابیس موقت (با timestamp یکتا می‌شود).
     */
    protected string $tempDb;

    public function __construct()
    {
        $this->sqlPath = storage_path('app/world');
        $this->tempDb = 'world_temp_' . time();
    }

    public function run(): void
    {
        // افزایش زمان اجرا برای حجم بالای داده‌ها
        set_time_limit(600);

        // ۱. بررسی وجود فایل‌ها
        $requiredFiles = ['schema.sql', 'countries.sql', 'states.sql'];
        foreach ($requiredFiles as $file) {
            if (!file_exists($this->sqlPath . '/' . $file)) {
                $this->command?->error("فایل {$file} در مسیر {$this->sqlPath} یافت نشد.");
                return;
            }
        }

        // ۲. ساخت دیتابیس موقت
        DB::statement("CREATE DATABASE IF NOT EXISTS `{$this->tempDb}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

        try {
            // ۳. import فایل‌های SQL به دیتابیس موقت
            $this->importSqlFile('schema.sql');
            $this->importSqlFile('countries.sql');
            $this->importSqlFile('states.sql');
            // regions و subregions هم در صورت نیاز import شوند (اختیاری)
            if (file_exists($this->sqlPath . '/regions.sql')) {
                $this->importSqlFile('regions.sql');
            }
            if (file_exists($this->sqlPath . '/subregions.sql')) {
                $this->importSqlFile('subregions.sql');
            }

            // ۴. انتقال داده‌ها به جداول اصلی
            $this->transferData();

            $this->command?->info('✅ داده‌های موقعیت جغرافیایی با موفقیت وارد جداول اصلی شدند.');
        } catch (\Exception $e) {
            $this->command?->error('خطا: ' . $e->getMessage());
        } finally {
            // ۵. حذف دیتابیس موقت
            DB::statement("DROP DATABASE IF EXISTS `{$this->tempDb}`");
        }
    }

    /**
     * import یک فایل SQL به دیتابیس موقت با استفاده از mysql client.
     */
    protected function importSqlFile(string $fileName): void
    {
        $filePath = $this->sqlPath . '/' . $fileName;

        $host     = config('database.connections.mysql.host');
        $port     = config('database.connections.mysql.port') ?: 3306;
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');

        $command = sprintf(
            'mysql -h %s -P %s -u %s %s %s < "%s"',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            $password ? '-p' . escapeshellarg($password) : '',
            escapeshellarg($this->tempDb),
            $filePath
        );

        exec($command, $output, $exitCode);

        if ($exitCode !== 0) {
            throw new \RuntimeException("خطا در import فایل {$fileName}. کد خروج: {$exitCode}");
        }
    }

    /**
     * انتقال داده‌ها از دیتابیس موقت به جداول اصلی.
     */
    protected function transferData(): void
    {
        // توجه: نام جداول در دیتابیس موقت باید countries و states باشد (مطابق فایل‌های SQL).
        // در صورت تفاوت، نام‌ها را اصلاح کنید.

        // ۱. کشورها → provinces
        DB::statement("
            INSERT INTO `provinces` (name, slug, lat, `long`, is_active, created_at, updated_at)
            SELECT 
                name,
                LOWER(REPLACE(name, ' ', '-')),
                COALESCE(latitude, '0'),
                COALESCE(longitude, '0'),
                1,
                NOW(),
                NOW()
            FROM `{$this->tempDb}`.countries
        ");

        // ۲. ایالت‌ها → counties
        DB::statement("
            INSERT INTO `counties` (province_id, name, slug, lat, `long`, is_active, created_at, updated_at)
            SELECT 
                p.id,
                s.name,
                LOWER(REPLACE(s.name, ' ', '-')),
                COALESCE(s.latitude, '0'),
                COALESCE(s.longitude, '0'),
                1,
                NOW(),
                NOW()
            FROM `{$this->tempDb}`.states s
            JOIN `{$this->tempDb}`.countries c ON s.country_id = c.id
            JOIN `provinces` p ON p.name = c.name
        ");

        // ۳. برای cities (در صورتی که فایل شهرها را بعداً اضافه کردید)
        // if (file_exists($this->sqlPath . '/cities.sql')) {
        //     $this->importSqlFile('cities.sql');
        //     DB::statement("
        //         INSERT INTO `cities` (province_id, county_id, name, slug, lat, `long`, is_active, created_at, updated_at)
        //         SELECT 
        //             p.id,
        //             cnt.id,
        //             ct.name,
        //             LOWER(REPLACE(ct.name, ' ', '-')),
        //             COALESCE(ct.latitude, '0'),
        //             COALESCE(ct.longitude, '0'),
        //             1,
        //             NOW(),
        //             NOW()
        //         FROM `{$this->tempDb}`.cities ct
        //         JOIN `{$this->tempDb}`.states s ON ct.state_id = s.id
        //         JOIN `{$this->tempDb}`.countries c ON s.country_id = c.id
        //         JOIN `provinces` p ON p.name = c.name
        //         JOIN `counties` cnt ON cnt.name = s.name AND cnt.province_id = p.id
        //     ");
        // }
    }
}