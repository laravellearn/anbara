<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
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
    }
}
