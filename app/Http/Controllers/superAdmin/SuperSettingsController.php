<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SuperSettingsController extends Controller
{
    public function index()
    {
        $settings = [
            'app_name'           => config('app.name'),
            'app_url'            => config('app.url'),
            'app_env'            => config('app.env'),
            'mail_mailer'        => config('mail.default'),
            'mail_from_address'  => config('mail.from.address'),
            'mail_from_name'     => config('mail.from.name'),
            'cache_driver'       => config('cache.default'),
            'queue_driver'       => config('queue.default'),
            'db_connection'      => config('database.default'),
        ];

        $dbStatus = $this->checkDbConnection();

        return view('super-admin.settings.index', compact('settings', 'dbStatus'));
    }

    public function clearCache()
    {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('view:clear');
        return back()->with('success', 'کش سیستم با موفقیت پاک شد.');
    }

    public function syncPermissions()
    {
        Artisan::call('db:seed', ['--class' => 'PermissionSeeder', '--force' => true]);
        return back()->with('success', 'مجوزها با موفقیت همگام‌سازی شدند.');
    }

    private function checkDbConnection(): bool
    {
        try {
            DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
