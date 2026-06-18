<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use App\Services\TenantManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UnitController extends Controller
{
    protected $manager;

    public function __construct(TenantManager $manager)
    {
        $this->manager = $manager;
    }

    public function index()
    {
        Gate::authorize('access', 'units.view');

        $units = Unit::where('tenant_id', $this->manager->getTenantId())
            ->latest()
            ->paginate(20);

        return view('warehouse.units.index', compact('units'));
    }

    public function store(Request $request)
    {
        Gate::authorize('access', 'units.create');

        $request->validate([
            'title'     => 'required|string|max:255',
            'symbol'    => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        Unit::create([
            'tenant_id' => $this->manager->getTenantId(),
            'title'     => $request->title,
            'symbol'    => $request->symbol,
            'is_active' => $request->is_active ?? true,
        ]);

        flash()->success('واحد جدید با موفقیت ایجاد شد.');
        return redirect()->route('warehouse.units.index');
    }

    public function update(Request $request, Unit $unit)
    {
        Gate::authorize('access', 'units.edit');

        $unit->update($request->validate([
            'title'     => 'required|string|max:255',
            'symbol'    => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]));

        flash()->success('واحد ویرایش شد.');
        return redirect()->route('warehouse.units.index');
    }

    public function destroy(Unit $unit)
    {
        Gate::authorize('access', 'units.delete');

        $unit->delete();
        flash()->success('واحد حذف شد.');
        return back();
    }
}