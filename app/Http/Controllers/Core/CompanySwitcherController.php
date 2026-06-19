<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Services\TenantManager;
use Illuminate\Http\Request;
use App\Models\Company;

class CompanySwitcherController extends Controller
{
    public function switch(Request $request, TenantManager $manager)
    {
        $user = auth()->user();
        $tenantId = $manager->getTenantId();

        try {
            $request->validate([
                'company_id' => 'required|exists:companies,id',
            ]);

            $company = Company::where('tenant_id', $tenantId)->findOrFail($request->company_id);

            if (!$user->companies()->where('company_id', $company->id)->exists()) {
                abort(403, 'شما به این سازمان دسترسی ندارید.');
            }

            session(['current_company_id' => $company->id]);
            $manager->setCompany($company);

            return redirect()->back()->with('toast', [
                'message' => 'سازمان به ' . $company->name . ' تغییر کرد.',
                'type'    => 'success',
                'title'   => 'تغییر سازمان'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors());
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'خطا در تغییر سازمان: ' . $e->getMessage()]);
        }
    }
}