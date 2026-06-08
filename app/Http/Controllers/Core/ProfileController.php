<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user();
        return view('core.profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name'   => 'required|string|max:255',
            'mobile' => ['required', 'string', 'size:11', Rule::unique('users')->ignore($user->id)],
            'email'  => ['nullable', 'email', Rule::unique('users')->ignore($user->id)],
        ]);

        $user->update($request->only('name', 'mobile', 'email'));

        return back()->with('swal_success', 'پروفایل با موفقیت به‌روز شد.');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        auth()->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('swal_success', 'رمز عبور تغییر کرد.');
    }
}