<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

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

        try {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],

                'email' => [
                    'nullable',
                    'email',
                    Rule::unique('users')->ignore($user->id),
                ],
            ]);

            $user->update($request->only('name', 'email'));

            return redirect()->route('profile.edit')->with('toast', [
                'message' => 'پروفایل با موفقیت به‌روز شد.',
                'type'    => 'success',
                'title'   => 'ویرایش پروفایل'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'خطا در به‌روزرسانی پروفایل: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function changePassword(Request $request)
    {
        try {
            $request->validate([
                'current_password' => 'required|current_password',
                'password'         => [
                    'required',
                    'confirmed',
                    \Illuminate\Validation\Rules\Password::min(8)
                        ->mixedCase()
                        ->numbers()
                ],
            ]);

            auth()->user()->update([
                'password' => Hash::make($request->password),
            ]);

            return redirect()->route('profile.edit')->with('toast', [
                'message' => 'رمز عبور با موفقیت تغییر کرد.',
                'type'    => 'success',
                'title'   => 'تغییر رمز عبور'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors());
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'خطا در تغییر رمز عبور: ' . $e->getMessage()]);
        }
    }
}