<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TenantManager;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        /** @var TenantManager $manager */
        $manager = app(TenantManager::class);
        $companyId = $manager->getCompanyId();

        if (auth()->user()?->isSuperAdmin()) {
            return redirect()->route('super-admin.dashboard');
        }

        return view('dashboard', compact('user', 'companyId'));
    }

}