<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;   // ← این خط را اضافه کنید
use App\Services\TenantManager;
use Illuminate\Http\Request;
use App\Models\Company;

class CompanySwitcherController extends Controller
{
    public function switch(Request $request, TenantManager $manager)
    {
        $user = auth()->user();
        $tenantId = $manager->getTenantId();

        $request->validate([
            'company_id' => 'required|exists:companies,id',
        ]);

        $company = Company::where('tenant_id', $tenantId)->findOrFail($request->company_id);

        if (!$user->companies()->where('company_id', $company->id)->exists()) {
            abort(403);
        }

        session(['current_company_id' => $company->id]);
        $manager->setCompany($company);

        return redirect()->back()->with('swal_success', 'سازمان به ' . $company->name . ' تغییر کرد.');
    }
}