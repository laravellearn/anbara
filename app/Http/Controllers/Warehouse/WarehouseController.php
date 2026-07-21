<?php

namespace App\Http\Controllers\Warehouse;

use App\Models\Warehouse;
use App\Models\User;
use App\Http\Requests\Warehouse\StoreWarehouseRequest;
use App\Http\Requests\Warehouse\UpdateWarehouseRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class WarehouseController extends BaseController
{
    public function index(Request $request)
    {
        Gate::authorize('access', 'warehouses.view');
        $tenantId = $this->manager->getTenantId();

        $query = Warehouse::with('company')->where('tenant_id', $tenantId);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $query->latest();
        $warehouses = $query->paginate($request->per_page ?? 20);

        $stats = [
            'total'    => Warehouse::where('tenant_id', $tenantId)->count(),
            'active'   => Warehouse::where('tenant_id', $tenantId)->where('is_active', true)->count(),
            'inactive' => Warehouse::where('tenant_id', $tenantId)->where('is_active', false)->count(),
        ];

        if ($request->ajax() || $request->input('ajax')) {
            return response()->json([
                'html'      => view('warehouse.warehouses._table', compact('warehouses'))->render(),
                'statsHtml' => view('warehouse.warehouses._stats', compact('stats'))->render(),
                'total'     => $warehouses->total(),
            ]);
        }

        return view('warehouse.warehouses.index', compact('warehouses', 'stats'));
    }

    public function create()
    {
        Gate::authorize('access', 'warehouses.create');
        $users = User::where('tenant_id', $this->manager->getTenantId())->get();
        return view('warehouse.warehouses.create', compact('users'));
    }

    public function store(StoreWarehouseRequest $request)
    {
        Gate::authorize('access', 'warehouses.create');

        try {
            $data = $request->validated();

            $data['tenant_id']  = $this->manager->getTenantId();
            $data['company_id'] = $this->manager->getCompanyId();

            $warehouse = Warehouse::create($data);

            if ($request->has('users')) {
                $tenantId = $this->manager->getTenantId();
                $syncData = collect($request->users)->mapWithKeys(fn($userId) => [
                    $userId => ['tenant_id' => $tenantId],
                ])->all();
                $warehouse->users()->sync($syncData);
            }

            return redirect()->route('warehouse.warehouses.index')->with('toast', [
                'message' => 'انبار با موفقیت ایجاد شد.',
                'type'    => 'success',
                'title'   => 'ایجاد انبار'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            \Log::error($e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            return redirect()->back()
                ->withErrors(['error' => 'خطا در ایجاد انبار'])
                ->withInput();
        }
    }

    public function edit(Warehouse $warehouse)
    {
        Gate::authorize('access', 'warehouses.edit');
        $users = User::where('tenant_id', $this->manager->getTenantId())->get();
        $warehouse->load('users');
        return view('warehouse.warehouses.edit', compact('warehouse', 'users'));
    }

    public function update(UpdateWarehouseRequest $request, Warehouse $warehouse)
    {
        Gate::authorize('access', 'warehouses.edit');

        try {
            $data = $request->validated();

            $warehouse->update($data);

            if ($request->has('users')) {
                $tenantId = $this->manager->getTenantId();
                $syncData = collect($request->users)->mapWithKeys(fn($userId) => [
                    $userId => ['tenant_id' => $tenantId],
                ])->all();
                $warehouse->users()->sync($syncData);
            }

            return redirect()->route('warehouse.warehouses.index')->with('toast', [
                'message' => 'انبار با موفقیت ویرایش شد.',
                'type'    => 'success',
                'title'   => 'ویرایش انبار'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            \Log::error($e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            return redirect()->back()
                ->withErrors(['error' => 'خطا در ویرایش انبار'])
                ->withInput();
        }
    }

    public function destroy(Warehouse $warehouse)
    {
        Gate::authorize('access', 'warehouses.delete');

        try {
            $warehouse->delete();

            return redirect()->route('warehouse.warehouses.index')->with('toast', [
                'message' => 'انبار با موفقیت حذف شد.',
                'type'    => 'success',
                'title'   => 'حذف انبار'
            ]);
        } catch (\Exception $e) {
            \Log::error($e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            return redirect()->back()
                ->withErrors(['error' => 'خطا در حذف انبار']);
        }
    }
}