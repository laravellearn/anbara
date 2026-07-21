<?php

namespace App\Http\Controllers\Warehouse;

use App\Models\TenantSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class SettingsController extends BaseController
{
    public function __construct(\App\Services\TenantManager $manager)
    {
        parent::__construct($manager);
    }

    // ─── صفحه اطلاعات سازمان ─────────────────────────────────────────────────
    public function organization()
    {
        Gate::authorize('access', 'settings.organization');
        $tenantId  = $this->manager->getTenantId();
        $settings  = TenantSetting::where('tenant_id', $tenantId)
            ->pluck('value', 'key')
            ->toArray();
        return view('warehouse.settings.organization', compact('settings'));
    }

    public function updateOrganization(Request $request)
    {
        Gate::authorize('access', 'settings.organization');
        $data = $request->validate([
            'org_name'               => 'required|string|max:200',
            'org_address'            => 'nullable|string|max:500',
            'org_phone'              => 'nullable|string|max:50',
            'org_email'              => 'nullable|email|max:150',
            'org_website'            => 'nullable|url|max:200',
            'org_national_code'      => 'nullable|string|max:20',
            'org_economic_code'      => 'nullable|string|max:20',
            'org_registration_number'=> 'nullable|string|max:50',
            'org_brand_color'        => 'nullable|string|max:20',
            'org_logo'               => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
        ]);

        $tenantId  = $this->manager->getTenantId();
        $companyId = $this->manager->getCompanyId();

        // آپلود لوگو
        if ($request->hasFile('org_logo')) {
            // حذف لوگوی قبلی
            $oldLogo = TenantSetting::get($tenantId, 'org_logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }
            $path = $request->file('org_logo')->store("logos/tenant_{$tenantId}", 'public');
            TenantSetting::set($tenantId, $companyId, 'organization', 'org_logo', $path);
        }

        // ذخیره بقیه فیلدها
        $textFields = ['org_name', 'org_address', 'org_phone', 'org_email',
                       'org_website', 'org_national_code', 'org_economic_code',
                       'org_registration_number', 'org_brand_color'];

        foreach ($textFields as $key) {
            if (array_key_exists($key, $data)) {
                TenantSetting::set($tenantId, $companyId, 'organization', $key, $data[$key] ?? '');
            }
        }

        return back()->with('success', 'اطلاعات و هویت بصری سازمان ذخیره شد.');
    }

    // ─── حذف لوگو ────────────────────────────────────────────────────────────
    public function deleteLogo()
    {
        Gate::authorize('access', 'settings.organization');
        $tenantId  = $this->manager->getTenantId();
        $companyId = $this->manager->getCompanyId();

        $oldLogo = TenantSetting::get($tenantId, 'org_logo');
        if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
            Storage::disk('public')->delete($oldLogo);
        }
        TenantSetting::set($tenantId, $companyId, 'organization', 'org_logo', '');

        return back()->with('success', 'لوگو حذف شد.');
    }

    // ─── تنظیمات انبار ────────────────────────────────────────────────────────
    public function warehouse()
    {
        Gate::authorize('access', 'settings.warehouse');
        $tenantId = $this->manager->getTenantId();
        $settings = TenantSetting::where('tenant_id', $tenantId)
            ->where('group', 'warehouse')
            ->pluck('value', 'key')
            ->toArray();
        return view('warehouse.settings.warehouse', compact('settings'));
    }

    public function updateWarehouse(Request $request)
    {
        Gate::authorize('access', 'settings.warehouse');
        $data = $request->validate([
            'default_valuation_method'    => 'required|in:fifo,lifo,average',
            'auto_approve_documents'      => 'nullable|boolean',
            'allow_negative_stock'        => 'nullable|boolean',
            'require_reason_for_adjustment' => 'nullable|boolean',
            'low_stock_alert_enabled'     => 'nullable|boolean',
            'low_stock_alert_threshold'   => 'nullable|integer|min:0',
        ]);

        $tenantId  = $this->manager->getTenantId();
        $companyId = $this->manager->getCompanyId();

        $booleans = ['auto_approve_documents', 'allow_negative_stock', 'require_reason_for_adjustment', 'low_stock_alert_enabled'];

        foreach ($data as $key => $value) {
            $type = in_array($key, $booleans) ? 'boolean' : (is_numeric($value) ? 'integer' : 'string');
            TenantSetting::set($tenantId, $companyId, 'warehouse', $key, $value ?? '0', $type);
        }

        return back()->with('success', 'تنظیمات انبار ذخیره شد.');
    }

    // ─── گردش‌کار و تأییدیه‌ها ────────────────────────────────────────────────
    public function workflow()
    {
        Gate::authorize('access', 'settings.workflow');
        $tenantId = $this->manager->getTenantId();
        $settings = TenantSetting::where('tenant_id', $tenantId)
            ->where('group', 'workflow')
            ->pluck('value', 'key')
            ->toArray();
        return view('warehouse.settings.workflow', compact('settings'));
    }

    public function updateWorkflow(Request $request)
    {
        Gate::authorize('access', 'settings.workflow');
        $data = $request->validate([
            'pr_requires_approval'  => 'nullable|boolean',
            'ir_requires_approval'  => 'nullable|boolean',
            'po_requires_approval'  => 'nullable|boolean',
            'doc_requires_approval' => 'nullable|boolean',
            'max_approval_amount'   => 'nullable|numeric|min:0',
        ]);

        $tenantId  = $this->manager->getTenantId();
        $companyId = $this->manager->getCompanyId();

        foreach ($data as $key => $value) {
            $type = is_numeric($value) && !in_array($key, ['max_approval_amount']) ? 'boolean' : 'string';
            if ($key === 'max_approval_amount') $type = 'float';
            TenantSetting::set($tenantId, $companyId, 'workflow', $key, $value ?? '0', $type);
        }

        return back()->with('success', 'تنظیمات گردش‌کار ذخیره شد.');
    }

    // ─── شماره‌گذاری اسناد ────────────────────────────────────────────────────
    public function numbering()
    {
        Gate::authorize('access', 'settings.numbering');
        $tenantId = $this->manager->getTenantId();
        $settings = TenantSetting::where('tenant_id', $tenantId)
            ->where('group', 'numbering')
            ->pluck('value', 'key')
            ->toArray();
        return view('warehouse.settings.numbering', compact('settings'));
    }

    public function updateNumbering(Request $request)
    {
        Gate::authorize('access', 'settings.numbering');
        $data = $request->validate([
            'po_prefix'   => 'nullable|string|max:10',
            'pr_prefix'   => 'nullable|string|max:10',
            'ir_prefix'   => 'nullable|string|max:10',
            'inv_prefix'  => 'nullable|string|max:10',
            'doc_prefix'  => 'nullable|string|max:10',
            'number_length' => 'required|integer|min:3|max:10',
            'include_year'  => 'nullable|boolean',
            'include_month' => 'nullable|boolean',
        ]);

        $tenantId  = $this->manager->getTenantId();
        $companyId = $this->manager->getCompanyId();

        foreach ($data as $key => $value) {
            $type = in_array($key, ['include_year', 'include_month']) ? 'boolean'
                  : ($key === 'number_length' ? 'integer' : 'string');
            TenantSetting::set($tenantId, $companyId, 'numbering', $key, $value ?? '', $type);
        }

        return back()->with('success', 'تنظیمات شماره‌گذاری ذخیره شد.');
    }

    // ─── اعلان‌ها ──────────────────────────────────────────────────────────────
    public function notifications()
    {
        Gate::authorize('access', 'settings.notifications');
        $tenantId = $this->manager->getTenantId();
        $settings = TenantSetting::where('tenant_id', $tenantId)
            ->where('group', 'notifications')
            ->pluck('value', 'key')
            ->toArray();
        return view('warehouse.settings.notifications', compact('settings'));
    }

    public function updateNotifications(Request $request)
    {
        Gate::authorize('access', 'settings.notifications');
        $data = $request->validate([
            'notify_low_stock'     => 'nullable|boolean',
            'notify_po_approved'   => 'nullable|boolean',
            'notify_pr_approved'   => 'nullable|boolean',
            'notify_ir_approved'   => 'nullable|boolean',
            'notify_doc_pending'   => 'nullable|boolean',
            'notify_channel_email' => 'nullable|boolean',
            'notify_channel_sms'   => 'nullable|boolean',
            'admin_email'          => 'nullable|email|max:150',
            'admin_mobile'         => 'nullable|string|max:15',
        ]);

        $tenantId  = $this->manager->getTenantId();
        $companyId = $this->manager->getCompanyId();

        $booleans = ['notify_low_stock', 'notify_po_approved', 'notify_pr_approved',
                     'notify_ir_approved', 'notify_doc_pending', 'notify_channel_email', 'notify_channel_sms'];

        foreach ($data as $key => $value) {
            $type = in_array($key, $booleans) ? 'boolean' : 'string';
            TenantSetting::set($tenantId, $companyId, 'notifications', $key, $value ?? '0', $type);
        }

        return back()->with('success', 'تنظیمات اعلان‌ها ذخیره شد.');
    }
}
