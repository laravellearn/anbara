<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Http\Requests\SuperAdmin\StorePlanRequest;
use App\Http\Requests\SuperAdmin\UpdatePlanRequest;
use Illuminate\Http\Request;

class SuperPlanController extends Controller
{
    public function index()
    {
        $plans = Plan::withCount('subscriptions')
            ->orderBy('sort_order')
            ->get();
        return view('super-admin.plans.index', compact('plans'));
    }

    public function create()
    {
        return view('super-admin.plans.create');
    }

    public function store(StorePlanRequest $request)
    {
        $data = $request->validated();
        $data['limits']   = is_array($data['limits']   ?? null) ? json_encode($data['limits'])   : ($data['limits']   ?? '{}');
        $data['features'] = is_array($data['features'] ?? null) ? json_encode($data['features']) : ($data['features'] ?? '[]');
        Plan::create($data);
        return redirect()->route('super-admin.plans.index')
            ->with('success', 'پلن با موفقیت ایجاد شد.');
    }

    public function edit(Plan $plan)
    {
        return view('super-admin.plans.edit', compact('plan'));
    }

    public function update(UpdatePlanRequest $request, Plan $plan)
    {
        $data = $request->validated();
        $data['limits']   = is_array($data['limits']   ?? null) ? json_encode($data['limits'])   : ($data['limits']   ?? '{}');
        $data['features'] = is_array($data['features'] ?? null) ? json_encode($data['features']) : ($data['features'] ?? '[]');
        $plan->update($data);
        return redirect()->route('super-admin.plans.index')
            ->with('success', 'پلن به‌روزرسانی شد.');
    }

    public function destroy(Plan $plan)
    {
        if ($plan->subscriptions()->where('status', 'active')->count() > 0) {
            return back()->withErrors(['error' => 'این پلن دارای اشتراک فعال است و قابل حذف نیست.']);
        }
        $plan->delete();
        return back()->with('success', 'پلن حذف شد.');
    }

    public function toggleStatus(Plan $plan)
    {
        $plan->update(['is_active' => !$plan->is_active]);
        return back()->with('success', $plan->is_active ? 'پلن فعال شد.' : 'پلن غیرفعال شد.');
    }
}
