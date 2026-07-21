<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpersonateController extends Controller
{
    public function store(Request $request)
    {
        $request->validate(['user_id' => 'required|exists:users,id']);
        $targetUser = User::findOrFail($request->user_id);

        // اطمینان از اینکه کاربر هدف tenant دارد
        if (!$targetUser->tenant_id) {
            return back()->withErrors(['user_id' => 'کاربر مورد نظر به سازمانی متصل نیست.']);
        }

        // ذخیره شناسه سوپرادمین در session برای بازگشت
        session(['impersonator_id' => auth()->id()]);

        // پاک‌سازی اطلاعات tenant قبلی از session و container
        session()->forget('current_organization_id');
        app()->forgetInstance('currentTenantId');
        app()->forgetInstance('currentOrganizationId');

        // لاگین کاربر هدف
        auth()->login($targetUser);

        // بازتولید session برای امنیت
        session()->regenerate();

        return redirect()->route('dashboard')->with('toast', [
            'message' => "شما اکنون به‌عنوان {$targetUser->name} وارد شده‌اید.",
            'type'    => 'success',
            'title'   => 'ورود',
        ]);
    }

    public function destroy()
    {
        $impersonatorId = session('impersonator_id');
        if (!$impersonatorId) {
            abort(403);
        }

        // اطمینان از اینکه کاربر ذخیره‌شده واقعاً سوپرادمین است (جلوگیری از privilege escalation)
        $originalUser = User::find($impersonatorId);
        if (!$originalUser || !$originalUser->isSuperAdmin()) {
            // جلوگیری از جعل هویت از طریق دستکاری session
            session()->forget('impersonator_id');
            auth()->logout();
            session()->invalidate();
            session()->regenerateToken();
            abort(403, 'درخواست نامعتبر.');
        }

        // لاگین سوپرادمین اصلی
        auth()->login($originalUser);

        // پاک‌سازی کامل session
        session()->forget('impersonator_id');
        session()->forget('current_organization_id');
        session()->forget('current_company_id');

        session()->regenerate();

        return redirect()->route('super-admin.dashboard')->with('toast', [
            'message' => 'شما به حساب کاربری خود بازگشتید.',
            'type'    => 'success',
            'title'   => 'ورود',
        ]);
    }
}