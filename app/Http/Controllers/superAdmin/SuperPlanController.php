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
        $plans = Plan::orderBy('sort_order')->get();
        return view('super-admin.plans.index', compact('plans'));
    }

    public function create()
    {
        return view('super-admin.plans.create');
    }

    public function store(StorePlanRequest $request)
    {
        $data = $request->validated();
        $data['limits'] = json_encode($data['limits'] ?? []);
        $data['features'] = json_encode($data['features'] ?? []);
        Plan::create($data);
        return redirect()->route('super-admin.plans.index')->with('success', 'پلن ایجاد شد.');
    }

    // edit, update, destroy مشابه
}