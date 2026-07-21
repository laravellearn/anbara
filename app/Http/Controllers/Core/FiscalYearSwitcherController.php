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
        try {
            $request->validate([
                'fiscal_year_id' => 'required|exists:fiscal_years,id',
            ]);

            $fiscalYear = FiscalYear::where('tenant_id', $manager->getTenantId())
                ->findOrFail($request->fiscal_year_id);

            $manager->setFiscalYear($fiscalYear);

            return redirect()->back()->with('toast', [
                'message' => 'سال مالی به ' . $fiscalYear->name . ' تغییر کرد.',
                'type'    => 'success',
                'title'   => 'تغییر سال مالی'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors());
        } catch (\Exception $e) {
            \Log::error($e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            return redirect()->back()
                ->withErrors(['error' => 'خطا در تغییر سال مالی']);
        }
    }
}