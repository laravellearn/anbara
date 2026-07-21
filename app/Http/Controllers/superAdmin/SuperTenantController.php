<?php

namespace App\Http\Controllers\SuperAdmin;
use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Http\Requests\SuperAdmin\StoreTenantRequest;
use App\Http\Requests\SuperAdmin\UpdateTenantRequest;
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

    public function store(StoreTenantRequest $request)
    {
        $data = $request->validated();
        $data['data'] = json_encode([]);
        Tenant::create($data);
        return redirect()->route('super-admin.tenants.index')->with('success', 'Tenant ایجاد شد.');
    }

    public function edit(Tenant $tenant)
    {
        return view('super-admin.tenants.edit', compact('tenant'));
    }

    public function update(UpdateTenantRequest $request, Tenant $tenant)
    {
        $tenant->update($request->validated());
        return redirect()->route('super-admin.tenants.index')->with('success', 'Tenant ویرایش شد.');
    }

    public function destroy(Tenant $tenant)
    {
        $tenant->delete(); // soft delete
        return back()->with('success', 'Tenant غیرفعال شد.');
    }
}