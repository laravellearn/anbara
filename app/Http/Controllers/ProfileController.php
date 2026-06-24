<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user()->load('employee');
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        try {
            $rules = [
                'name'          => ['required', 'string', 'max:255'],
                'email'         => ['nullable', 'email', Rule::unique('users')->ignore($user->id)],
                'avatar_base64' => ['nullable', 'string'],   // رشته Base64
            ];

            if (! $user->employee) {
                $rules['national_code'] = ['nullable', 'string', 'max:20'];
            }

            $data = $request->validate($rules);

            // پردازش آواتار از Base64
            $avatarPath = $this->saveAvatarFromBase64($data['avatar_base64'] ?? null, $user);
            if ($avatarPath !== null) {
                $data['avatar'] = $avatarPath;
            }
            unset($data['avatar_base64']);   // فیلد اضافی پاک شود

            $user->update($data);

            return redirect()->route('profile.edit')->with('toast', [
                'message' => 'پروفایل با موفقیت به‌روز شد.',
                'type'    => 'success',
                'title'   => 'ویرایش پروفایل'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
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
                    \Illuminate\Validation\Rules\Password::min(8)->mixedCase()->numbers()
                ],
            ]);

            auth()->user()->update(['password' => Hash::make($request->password)]);

            return redirect()->route('profile.edit')->with('toast', [
                'message' => 'رمز عبور با موفقیت تغییر کرد.',
                'type'    => 'success',
                'title'   => 'تغییر رمز عبور'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors());
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * ذخیرهٔ آواتار با استفاده از PHP خالص (کاملاً مشابه storeLogo در CompanyController)
     * این روش مسیر موقت C:\Windows\Temp را کاملاً دور می‌زند.
     */
    private function saveAvatarFromBase64(?string $base64, $user): ?string
    {
        if (empty($base64) || ! str_contains($base64, ',')) {
            return null;
        }

        // جداسازی داده از هدر (data:image/...;base64,XXXX)
        [, $raw] = explode(',', $base64, 2);
        $decoded = base64_decode($raw);

        if ($decoded === false || strlen($decoded) === 0) {
            return null;
        }

        // تشخیص پسوند
        preg_match('/^data:image\/(\w+);base64/', $base64, $matches);
        $extension = $matches[1] ?? 'png';

        $filename  = Str::uuid() . '.' . $extension;
        $destination = public_path('/storage/avatars');
        if (! file_exists($destination)) {
            mkdir($destination, 0755, true);
        }

        // ذخیرهٔ فیزیکی
        file_put_contents($destination . '/' . $filename, $decoded);

        // حذف آواتار قبلی (اگر آپلودی باشد)
        if ($user->avatar && str_starts_with($user->avatar, 'storage/')) {
            $oldPath = public_path(ltrim($user->avatar, '/'));
            if (file_exists($oldPath)) {
                @unlink($oldPath);
            }
        }

        return 'storage/avatars/' . $filename;
    }
}
