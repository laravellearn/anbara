<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $data = $request->validate([
            'name'   => 'required|string|max:255',
            'mobile' => 'required|unique:users,mobile,' . $user->id,
            'email'  => 'nullable|email|unique:users,email,' . $user->id,
        ]);
        $user->update($data);
        flash()->success('پروفایل به‌روز شد.');
        return back();
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|min:8|confirmed',
        ]);
        auth()->user()->update(['password' => Hash::make($request->password)]);
        flash()->success('رمز عبور تغییر کرد.');
        return back();
    }
}
