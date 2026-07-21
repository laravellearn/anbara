<?php

namespace App\Http\Controllers\Warehouse;

use App\Models\Employee;
use App\Models\FixedAsset;
use App\Models\FixedAssetAssignment;
use App\Models\FixedAssetMaintenance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class FixedAssetController extends BaseController
{
    // ─── لیست دارایی‌ها ──────────────────────────────────────────────────────
    public function index(Request $request)
    {
        Gate::authorize('access', 'fixed-assets.view');

        [$tenantId, $companyId] = [$this->manager->getTenantId(), $this->manager->getCompanyId()];

        $query = FixedAsset::forTenant($tenantId, $companyId)
            ->with('activeAssignment.employee');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('title', 'like', "%{$s}%")
                ->orWhere('asset_code', 'like', "%{$s}%")
                ->orWhere('serial_number', 'like', "%{$s}%"));
        }

        $assets = $query->orderBy('asset_code')->paginate(20)->withQueryString();

        $stats = [
            'total'            => FixedAsset::forTenant($tenantId, $companyId)->count(),
            'active'           => FixedAsset::forTenant($tenantId, $companyId)->where('status', 'active')->count(),
            'assigned'         => FixedAsset::forTenant($tenantId, $companyId)->where('status', 'assigned')->count(),
            'under_maintenance'=> FixedAsset::forTenant($tenantId, $companyId)->where('status', 'under_maintenance')->count(),
            'total_value'      => FixedAsset::forTenant($tenantId, $companyId)->sum('current_value'),
        ];

        return view('warehouse.fixed-assets.index', compact('assets', 'stats'));
    }

    // ─── فرم ثبت ─────────────────────────────────────────────────────────────
    public function create()
    {
        Gate::authorize('access', 'fixed-assets.create');
        return view('warehouse.fixed-assets.create', [
            'statuses'   => FixedAsset::STATUSES,
            'categories' => FixedAsset::CATEGORIES,
        ]);
    }

    // ─── ذخیره ───────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        Gate::authorize('access', 'fixed-assets.create');

        $validated = $request->validate([
            'asset_code'     => 'required|string|max:50',
            'title'          => 'required|string|max:255',
            'serial_number'  => 'nullable|string|max:100',
            'description'    => 'nullable|string',
            'category'       => 'nullable|string',
            'location'       => 'nullable|string|max:255',
            'purchase_price' => 'nullable|numeric|min:0',
            'current_value'  => 'nullable|numeric|min:0',
            'purchase_date'  => 'nullable|date',
            'warranty_expiry'=> 'nullable|date',
            'status'         => 'required|string|in:' . implode(',', array_keys(FixedAsset::STATUSES)),
        ]);

        [$tenantId, $companyId] = [$this->manager->getTenantId(), $this->manager->getCompanyId()];

        FixedAsset::create(array_merge($validated, [
            'tenant_id'  => $tenantId,
            'company_id' => $companyId,
            'created_by' => auth()->id(),
        ]));

        return redirect()->route('warehouse.fixed-assets.index')
            ->with('success', 'دارایی با موفقیت ثبت شد.');
    }

    // ─── نمایش جزئیات ────────────────────────────────────────────────────────
    public function show(FixedAsset $fixedAsset)
    {
        Gate::authorize('access', 'fixed-assets.view');
        $this->authorizeTenant($fixedAsset);

        $fixedAsset->load([
            'assignments.employee',
            'assignments.assignedBy',
            'maintenances',
            'createdBy',
        ]);

        $employees = Employee::where('tenant_id', $this->manager->getTenantId())->orderBy('name')->get();

        return view('warehouse.fixed-assets.show', compact('fixedAsset', 'employees'));
    }

    // ─── فرم ویرایش ──────────────────────────────────────────────────────────
    public function edit(FixedAsset $fixedAsset)
    {
        Gate::authorize('access', 'fixed-assets.edit');
        $this->authorizeTenant($fixedAsset);

        return view('warehouse.fixed-assets.edit', [
            'fixedAsset' => $fixedAsset,
            'statuses'   => FixedAsset::STATUSES,
            'categories' => FixedAsset::CATEGORIES,
        ]);
    }

    // ─── به‌روزرسانی ──────────────────────────────────────────────────────────
    public function update(Request $request, FixedAsset $fixedAsset)
    {
        Gate::authorize('access', 'fixed-assets.edit');
        $this->authorizeTenant($fixedAsset);

        $validated = $request->validate([
            'asset_code'     => 'required|string|max:50',
            'title'          => 'required|string|max:255',
            'serial_number'  => 'nullable|string|max:100',
            'description'    => 'nullable|string',
            'category'       => 'nullable|string',
            'location'       => 'nullable|string|max:255',
            'purchase_price' => 'nullable|numeric|min:0',
            'current_value'  => 'nullable|numeric|min:0',
            'purchase_date'  => 'nullable|date',
            'warranty_expiry'=> 'nullable|date',
            'status'         => 'required|string|in:' . implode(',', array_keys(FixedAsset::STATUSES)),
        ]);

        $fixedAsset->update($validated);

        return redirect()->route('warehouse.fixed-assets.show', $fixedAsset)
            ->with('success', 'اطلاعات دارایی به‌روزرسانی شد.');
    }

    // ─── حذف ─────────────────────────────────────────────────────────────────
    public function destroy(FixedAsset $fixedAsset)
    {
        Gate::authorize('access', 'fixed-assets.delete');
        $this->authorizeTenant($fixedAsset);

        $fixedAsset->delete();

        return redirect()->route('warehouse.fixed-assets.index')
            ->with('success', 'دارایی حذف شد.');
    }

    // ─── تخصیص به پرسنل ──────────────────────────────────────────────────────
    public function assign(Request $request, FixedAsset $fixedAsset)
    {
        Gate::authorize('access', 'fixed-assets.assign');
        $this->authorizeTenant($fixedAsset);

        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'assigned_at' => 'required|date',
            'notes'       => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $fixedAsset) {
            // پایان تخصیص قبلی
            FixedAssetAssignment::where('fixed_asset_id', $fixedAsset->id)
                ->where('status', 'active')
                ->update(['status' => 'returned', 'returned_at' => now()]);

            FixedAssetAssignment::create([
                'tenant_id'      => $this->manager->getTenantId(),
                'fixed_asset_id' => $fixedAsset->id,
                'employee_id'    => $request->employee_id,
                'assigned_by'    => auth()->id(),
                'assigned_at'    => $request->assigned_at,
                'status'         => 'active',
                'notes'          => $request->notes,
            ]);

            $fixedAsset->update(['status' => 'assigned']);
        });

        return back()->with('success', 'دارایی با موفقیت تخصیص یافت.');
    }

    // ─── عودت دارایی ─────────────────────────────────────────────────────────
    public function returnAsset(Request $request, FixedAsset $fixedAsset)
    {
        Gate::authorize('access', 'fixed-assets.assign');
        $this->authorizeTenant($fixedAsset);

        $request->validate(['returned_at' => 'required|date', 'notes' => 'nullable|string']);

        DB::transaction(function () use ($request, $fixedAsset) {
            FixedAssetAssignment::where('fixed_asset_id', $fixedAsset->id)
                ->where('status', 'active')
                ->update([
                    'status'      => 'returned',
                    'returned_at' => $request->returned_at,
                    'notes'       => $request->notes,
                ]);

            $fixedAsset->update(['status' => 'active']);
        });

        return back()->with('success', 'دارایی با موفقیت عودت داده شد.');
    }

    // ─── ثبت تعمیر/نگهداری ───────────────────────────────────────────────────
    public function addMaintenance(Request $request, FixedAsset $fixedAsset)
    {
        Gate::authorize('access', 'fixed-assets.maintain');
        $this->authorizeTenant($fixedAsset);

        $request->validate([
            'maintenance_date'      => 'required|date',
            'type'                  => 'required|in:repair,service,inspection',
            'description'           => 'nullable|string',
            'cost'                  => 'nullable|numeric|min:0',
            'performed_by'          => 'nullable|string|max:255',
            'next_maintenance_date' => 'nullable|date',
        ]);

        DB::transaction(function () use ($request, $fixedAsset) {
            FixedAssetMaintenance::create(array_merge($request->only([
                'maintenance_date', 'type', 'description', 'cost',
                'performed_by', 'next_maintenance_date',
            ]), [
                'tenant_id'      => $this->manager->getTenantId(),
                'fixed_asset_id' => $fixedAsset->id,
                'created_by'     => auth()->id(),
            ]));

            // اگر تعمیر است → وضعیت تغییر می‌کند
            if ($request->type === 'repair') {
                $fixedAsset->update(['status' => 'under_maintenance']);
            }
        });

        return back()->with('success', 'سابقه تعمیر/نگهداری ثبت شد.');
    }

    // ─── اسقاط دارایی ────────────────────────────────────────────────────────
    public function scrap(Request $request, FixedAsset $fixedAsset)
    {
        Gate::authorize('access', 'fixed-assets.scrap');
        $this->authorizeTenant($fixedAsset);

        DB::transaction(function () use ($fixedAsset) {
            FixedAssetAssignment::where('fixed_asset_id', $fixedAsset->id)
                ->where('status', 'active')
                ->update(['status' => 'returned', 'returned_at' => now()]);

            $fixedAsset->update(['status' => 'scrapped']);
        });

        return back()->with('success', 'دارایی اسقاط شد.');
    }

    // ─── helper: بررسی تنانت ─────────────────────────────────────────────────
    private function authorizeTenant(FixedAsset $asset): void
    {
        abort_if(
            $asset->tenant_id !== $this->manager->getTenantId(),
            403,
            'دسترسی غیرمجاز'
        );
    }
}
