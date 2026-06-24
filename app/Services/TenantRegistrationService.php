<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Company;
use App\Models\Role;
use App\Models\CompanyUser;
use App\Models\FiscalYear;
use App\Models\Plan;
use App\Models\Subscription;
use Hekmatinasser\Verta\Facades\Verta;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TenantRegistrationService
{
    /**
     * پس از تأیید OTP کاربر، Tenant و وابستگی‌ها را می‌سازد.
     *
     * @param User  $user  کاربری که قبلاً ثبت‌نام کرده ولی غیرفعال است
     * @param array $data  اطلاعات سازمان (organization_name, slug, ...)
     * @return array
     */
    public function finalizeRegistration(User $user, array $data): array
    {
        return DB::transaction(function () use ($user, $data) {

            // تولید اسلاگ خودکار اگر در آرایه داده وجود نداشت یا خالی بود
            $slug = $data['slug'] ?? null;
            if (empty($slug)) {
                $slug = Str::slug($data['organization_name']);
            }
            // یکتا کردن اسلاگ
            $originalSlug = $slug;
            $counter = 1;
            while (Tenant::withTrashed()->where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            // 1. ایجاد Tenant
            $tenant = Tenant::create([
                'name'          => $data['organization_name'],
                'slug'          => $slug,
                'email'         => $user->email ?? null,
                'phone'         => $user->mobile ?? null,
                'is_active'     => true,
                'trial_ends_at' => now()->addDays(config('app.trial_days', 14)),
                'data'          => json_encode([]),
            ]);

            // بخش ایجاد سال مالی - بعد از ایجاد Tenant و قبل از return
            $month = (int) ($tenant->fiscal_year_start_month ?: 1);
            $day   = (int) ($tenant->fiscal_year_start_day ?: 1);

            $fiscalStart = Verta::now()->month($month)->day($day);

            if (Verta::now()->lessThan($fiscalStart)) {
                $fiscalStart->subYear();
            }

            $fiscalEnd = (clone $fiscalStart)->addYear()->subDay();

            // 2. اتصال کاربر به Tenant و فعال‌سازی
            $user->update([
                'tenant_id'          => $tenant->id,
                'is_active'          => true,
                'mobile_verified_at' => now(),
            ]);


            // 7. ایجاد اشتراک آزمایشی ۱۴ روزه (Plan trial)
            $trialPlan = Plan::where('code', 'trial')->first();
            if ($trialPlan) {
                $subscription = Subscription::create([
                    'tenant_id'  => $tenant->id,
                    'plan_id'    => $trialPlan->id,
                    'starts_at'  => now(),
                    'ends_at'    => now()->addDays(config('app.trial_days', 14)),
                    'status'     => 'active',
                    'auto_renew' => false,
                ]);

                // مقداردهی مصرف اولیه
                foreach ($trialPlan->features as $feature) {
                    $subscription->usages()->create([
                        'feature_key' => $feature,
                        'used_value'  => 0,
                    ]);
                }
            }

            // 3. ایجاد شرکت پیش‌فرض
            $company = Company::create([
                'tenant_id' => $tenant->id,
                'name'      => $data['organization_name'],
                'code'      => 'MAIN-' . $tenant->id,
                'is_active' => true,
            ]);

            // 4. ارتباط کاربر با شرکت
            $companyUser = CompanyUser::create([
                'tenant_id'  => $tenant->id,
                'company_id' => $company->id,
                'user_id'    => $user->id,
                'is_default' => true,
            ]);

            FiscalYear::create([
                'tenant_id'  => $tenant->id,
                'company_id' => $company->id,   // ← اضافه شود
                'name'       => $fiscalStart->year,
                'is_active'  => true,
                'start_date' => $fiscalStart->toCarbon(),
                'end_date'   => $fiscalEnd->toCarbon(),
            ]);


            // 5. Role ادمین Tenant (اگر وجود ندارد)
            $role = Role::firstOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'code'      => 'tenant_admin',
                ],
                [
                    'title'       => 'مدیر سازمان',
                    'description' => 'دسترسی کامل به تمام بخش‌ها',
                    'is_system'   => true,
                    'is_active'   => true,
                ]
            );

            // تخصیص همه permissionها
            $allPermissions = \App\Models\Permission::all();
            $role->permissions()->sync($allPermissions->pluck('id'));

            // 6. تخصیص نقش به کاربر در شرکت
            $companyUser->roles()->attach($role->id);

            return compact('user', 'tenant', 'company');
        });
    }
}
