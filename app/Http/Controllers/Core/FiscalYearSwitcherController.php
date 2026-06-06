<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\FiscalYear;
use App\Services\TenantManager;
use Illuminate\Http\Request;

class FiscalYearSwitcherController extends Controller
{
    public function switch(Request $request, TenantManager $manager)
    {
        $request->validate([
            'fiscal_year_id' => 'required|exists:fiscal_years,id',
        ]);

        $fiscalYear = FiscalYear::where('tenant_id', $manager->getTenantId())->findOrFail($request->fiscal_year_id);
        $manager->setFiscalYear($fiscalYear);

        return redirect()->back()->with('swal_error', 'سال مالی به ' . $fiscalYear->name . ' تغییر کرد.');
    }
}