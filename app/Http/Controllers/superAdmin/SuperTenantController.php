<?php

namespace App\Http\Controllers\SuperAdmin;
use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;

class SuperTenantController extends Controller
{
    public function index()
    {
        $tenants = Tenant::latest()->paginate(20);
        return view('super-admin.tenants.index', compact('tenants'));
    }

    public function create()
    {
        return view('super-admin.tenants.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|alpha_dash|unique:tenants,slug',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        $data['data'] = json_encode([]);
        Tenant::create($data);
        return redirect()->route('super-admin.tenants.index')->with('success', 'Tenant ایجاد شد.');
    }

    public function edit(Tenant $tenant)
    {
        return view('super-admin.tenants.edit', compact('tenant'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $tenant->update($request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|alpha_dash|unique:tenants,slug,'.$tenant->id,
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'is_active' => 'boolean',
        ]));
        return redirect()->route('super-admin.tenants.index')->with('success', 'Tenant ویرایش شد.');
    }

    public function destroy(Tenant $tenant)
    {
        $tenant->delete(); // soft delete
        return back()->with('success', 'Tenant غیرفعال شد.');
    }
}