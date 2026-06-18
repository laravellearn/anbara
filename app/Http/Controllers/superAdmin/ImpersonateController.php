<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ImpersonateController extends Controller
{
    public function store(Request $request)
    {
        $request->validate(['user_id' => 'required|exists:users,id']);
        $user = User::findOrFail($request->user_id);

        // ذخیره شناسه سوپرادمین در session
        session(['impersonator_id' => auth()->id()]);

        auth()->login($user);

        flash()->success("شما اکنون به‌عنوان {$user->name} وارد شده‌اید.");
        return redirect()->route('dashboard');
    }

    public function destroy()
    {
        $impersonatorId = session('impersonator_id');
        if (!$impersonatorId) abort(403);

        auth()->loginUsingId($impersonatorId);
        session()->forget('impersonator_id');

        flash()->info('شما به حساب کاربری خود بازگشتید.');
        return redirect()->route('super-admin.dashboard');
    }
}