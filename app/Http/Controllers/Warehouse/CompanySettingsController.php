<?php

namespace App\Http\Controllers\Warehouse;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

/**
 * تنظیمات شرکت — لوگو، سربرگ، مشخصات چاپی
 */
class CompanySettingsController extends BaseController
{
    private const GROUP = 'company';

    public function index()
    {
        Gate::authorize('access', 'settings.company');

        [$tenantId, $companyId] = [$this->manager->getTenantId(), $this->manager->getCompanyId()];

        $settings = Setting::where('tenant_id', $tenantId)
            ->where('company_id', $companyId)
            ->where('group', self::GROUP)
            ->get()
            ->keyBy('key');

        return view('warehouse.settings.company', compact('settings'));
    }

    public function update(Request $request)
    {
        Gate::authorize('access', 'settings.company');

        $request->validate([
            'company_name'    => ['nullable', 'string', 'max:255'],
            'company_address' => ['nullable', 'string', 'max:500'],
            'company_phone'   => ['nullable', 'string', 'max:50'],
            'company_email'   => ['nullable', 'email', 'max:150'],
            'company_website' => ['nullable', 'string', 'max:150'],
            'company_reg_no'  => ['nullable', 'string', 'max:50'],
            'company_tax_no'  => ['nullable', 'string', 'max:50'],
            'print_footer'    => ['nullable', 'string', 'max:500'],
            'logo'            => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,svg', 'max:1024'],
        ]);

        [$tenantId, $companyId] = [$this->manager->getTenantId(), $this->manager->getCompanyId()];

        $fields = [
            'company_name','company_address','company_phone','company_email',
            'company_website','company_reg_no','company_tax_no','print_footer',
        ];

        foreach ($fields as $key) {
            Setting::updateOrCreate(
                ['tenant_id' => $tenantId, 'company_id' => $companyId, 'group' => self::GROUP, 'key' => $key],
                ['type' => 'string', 'value' => $request->input($key, '')]
            );
        }

        // آپلود لوگو
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $path = "logos/{$tenantId}/{$companyId}/logo." . $file->getClientOriginalExtension();
            Storage::disk('public')->put($path, file_get_contents($file));

            Setting::updateOrCreate(
                ['tenant_id' => $tenantId, 'company_id' => $companyId, 'group' => self::GROUP, 'key' => 'company_logo'],
                ['type' => 'string', 'value' => $path]
            );
        }

        return back()->with('toast', ['type' => 'success', 'message' => 'تنظیمات شرکت ذخیره شد.']);
    }

    /** helper: خواندن یک تنظیم */
    public static function get(int $tenantId, int $companyId, string $key, mixed $default = null): mixed
    {
        return Setting::where('tenant_id', $tenantId)
            ->where('company_id', $companyId)
            ->where('group', 'company')
            ->where('key', $key)
            ->value('value') ?? $default;
    }
}
