<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Services\TenantManager;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\CompanyUser;

class CompanySwitcherController extends Controller
{
    public function switch(Request $request, TenantManager $manager)
    {
        $user = auth()->user();
        $tenantId = $manager->getTenantId();

        $request->validate([
            'company_id' => 'required|exists:companies,id',
        ]);

        // چک مستقیم در جدول company_user (بدون استفاده از رابطه)
        $exists = CompanyUser::where('tenant_id', $tenantId)
            ->where('company_id', $request->company_id)
            ->where('user_id', $user->id)
            ->exists();

        if (!$exists) {
            abort(403, 'شما به این سازمان دسترسی ندارید.');
        }

        $company = \App\Models\Company::findOrFail($request->company_id);

        session(['current_company_id' => $company->id]);
        $manager->setCompany($company);

        // تنظیم سال مالی پیش‌فرض سازمان جدید
        $fiscalYear = \App\Models\FiscalYear::where('company_id', $company->id)
            ->where('is_active', true)
            ->first();

        if ($fiscalYear) {
            $manager->setFiscalYear($fiscalYear);
        } else {
            // اگر هیچ سال فعالی وجود نداشت، می‌توانید اولین سال موجود را انتخاب کنید یا null بگذارید
            $fallback = \App\Models\FiscalYear::where('company_id', $company->id)->latest()->first();
            $manager->setFiscalYear($fallback);
        }

        return redirect()->back()->with('toast', [
            'message' => 'سازمان به ' . $company->name . ' تغییر کرد.',
            'type'    => 'success',
            'title'   => 'تغییر سازمان'
        ]);
    }
}
