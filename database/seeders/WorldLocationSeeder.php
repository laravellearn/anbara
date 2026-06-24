<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorldLocationSeeder extends Seeder
{
    protected string $sqlPath;
    protected string $tempDb;

    public function __construct()
    {
        $this->sqlPath = storage_path('app/world');
        $this->tempDb = 'world_temp_' . time();
    }

    public function run(): void
    {
        set_time_limit(600);

        $requiredFiles = ['schema.sql', 'countries.sql', 'states.sql'];
        foreach ($requiredFiles as $file) {
            if (!file_exists($this->sqlPath . '/' . $file)) {
                $this->command?->error("فایل {$file} یافت نشد.");
                return;
            }
        }

        DB::statement("CREATE DATABASE IF NOT EXISTS `{$this->tempDb}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

        try {
            $this->importSql('schema.sql');
            $this->importSql('countries.sql');
            $this->importSql('states.sql');

            if (file_exists($this->sqlPath . '/cities.sql')) {
                $this->importSql('cities.sql');
            }

            $this->transferData();

            $this->command?->info('✅ داده‌های موقعیت جغرافیایی با موفقیت وارد جداول اصلی شدند.');
        } catch (\Exception $e) {
            $this->command?->error('خطا: ' . $e->getMessage());
        } finally {
            DB::statement("DROP DATABASE IF EXISTS `{$this->tempDb}`");
        }
    }

    protected function importSql(string $file): void
    {
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port') ?: 3306;
        $user = config('database.connections.mysql.username');
        $pass = config('database.connections.mysql.password');

        $cmd = sprintf(
            'mysql -h %s -P %s -u %s %s %s < "%s"',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($user),
            $pass ? '-p' . escapeshellarg($pass) : '',
            escapeshellarg($this->tempDb),
            $this->sqlPath . '/' . $file
        );

        exec($cmd, $output, $code);
        if ($code !== 0) {
            throw new \RuntimeException("خطا در import فایل {$file}. کد خروج: {$code}");
        }
    }

    protected function transferData(): void
    {
        // ۱. کشورها → countries
        DB::statement("
            INSERT INTO `countries` (name, slug, lat, `long`, is_active, created_at, updated_at)
            SELECT name, LOWER(REPLACE(name,' ','-')), COALESCE(latitude,'0'), COALESCE(longitude,'0'), 1, NOW(), NOW()
            FROM `{$this->tempDb}`.countries
        ");

        // ۲. ایالت‌ها → provinces (با country_id)
        DB::statement("
            INSERT INTO `provinces` (country_id, name, slug, lat, `long`, is_active, created_at, updated_at)
            SELECT c.id, s.name, LOWER(REPLACE(s.name,' ','-')), COALESCE(s.latitude,'0'), COALESCE(s.longitude,'0'), 1, NOW(), NOW()
            FROM `{$this->tempDb}`.states s
            JOIN `{$this->tempDb}`.countries tc ON s.country_id = tc.id
            JOIN `countries` c ON c.name = tc.name
        ");

        // ۳. شهرها → cities (در صورت وجود فایل cities.sql)
        if (file_exists($this->sqlPath . '/cities.sql')) {
            DB::statement("
                INSERT INTO `cities` (country_id, province_id, name, slug, lat, `long`, is_active, created_at, updated_at)
                SELECT c.id, p.id, ct.name, LOWER(REPLACE(ct.name,' ','-')), COALESCE(ct.latitude,'0'), COALESCE(ct.longitude,'0'), 1, NOW(), NOW()
                FROM `{$this->tempDb}`.cities ct
                JOIN `{$this->tempDb}`.states ts ON ct.state_id = ts.id
                JOIN `{$this->tempDb}`.countries tc ON ts.country_id = tc.id
                JOIN `countries` c ON c.name = tc.name
                JOIN `provinces` p ON p.name = ts.name AND p.country_id = c.id
            ");
        }
    }
}