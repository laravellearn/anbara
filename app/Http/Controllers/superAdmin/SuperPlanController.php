<?php

namespace App\Http\Controllers\SuperAdmin;
use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class SuperPlanController extends Controller
{
    public function index()
    {
        $plans = Plan::orderBy('sort_order')->get();
        return view('super-admin.plans.index', compact('plans'));
    }

    public function create()
    {
        return view('super-admin.plans.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:plans',
            'monthly_price' => 'numeric',
            'yearly_price' => 'numeric',
            'duration_days' => 'nullable|integer',
            'limits' => 'nullable|array',
            'features' => 'nullable|array',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);
        $data['limits'] = json_encode($data['limits'] ?? []);
        $data['features'] = json_encode($data['features'] ?? []);
        Plan::create($data);
        return redirect()->route('super-admin.plans.index')->with('success', 'پلن ایجاد شد.');
    }

    // edit, update, destroy مشابه
}