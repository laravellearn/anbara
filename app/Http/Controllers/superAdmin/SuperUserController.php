<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SuperUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('tenant')
            ->whereNotNull('tenant_id')
            ->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('name', 'like', "%{$s}%")
                ->orWhere('email', 'like', "%{$s}%")
                ->orWhere('mobile', 'like', "%{$s}%"));
        }
        if ($request->filled('tenant_id')) {
            $query->where('tenant_id', $request->tenant_id);
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $users   = $query->paginate(25)->withQueryString();
        $tenants = Tenant::orderBy('name')->get(['id', 'name']);

        $stats = [
            'total'    => User::whereNotNull('tenant_id')->count(),
            'active'   => User::whereNotNull('tenant_id')->where('is_active', true)->count(),
            'inactive' => User::whereNotNull('tenant_id')->where('is_active', false)->count(),
        ];

        return view('super-admin.users.index', compact('users', 'tenants', 'stats'));
    }

    public function create()
    {
        $tenants = Tenant::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        return view('super-admin.users.create', compact('tenants'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'mobile'    => 'nullable|string|max:20',
            'tenant_id' => 'required|exists:tenants,id',
            'password'  => 'required|string|min:8|confirmed',
            'is_active' => 'boolean',
        ]);

        User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'mobile'    => $request->mobile,
            'tenant_id' => $request->tenant_id,
            'password'  => Hash::make($request->password),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('super-admin.users.index')
            ->with('success', 'کاربر با موفقیت ایجاد شد.');
    }

    public function edit(User $user)
    {
        $tenants = Tenant::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        return view('super-admin.users.edit', compact('user', 'tenants'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email,' . $user->id,
            'mobile'    => 'nullable|string|max:20',
            'tenant_id' => 'required|exists:tenants,id',
            'password'  => 'nullable|string|min:8|confirmed',
            'is_active' => 'boolean',
        ]);

        $data = $request->only(['name', 'email', 'mobile', 'tenant_id', 'is_active']);
        $data['is_active'] = $request->boolean('is_active');
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('super-admin.users.index')
            ->with('success', 'اطلاعات کاربر به‌روزرسانی شد.');
    }

    public function destroy(User $user)
    {
        abort_if($user->isSuperAdmin(), 403, 'امکان حذف سوپرادمین وجود ندارد.');
        $user->delete();
        return back()->with('success', 'کاربر حذف شد.');
    }

    public function toggleStatus(User $user)
    {
        abort_if($user->isSuperAdmin(), 403);
        $user->update(['is_active' => !$user->is_active]);
        return back()->with('success', $user->is_active ? 'کاربر فعال شد.' : 'کاربر مسدود شد.');
    }
}

