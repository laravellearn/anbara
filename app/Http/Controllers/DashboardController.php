<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
    
        $companyId = CompanyContext::getId();
    
        return view('dashboard', compact('user', 'companyId'));
    }
    
    public function switchCompany($companyId)
{
    $user = auth()->user();

    abort_unless(
        $user->companies()->where('company_id', $companyId)->exists(),
        403
    );

    session(['current_company_id' => $companyId]);

    return redirect()->back();
}
}
