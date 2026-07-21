<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Requests\Profile\ChangePasswordRequest;
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

    public function update(UpdateProfileRequest $request)
    {
        $user = auth()->user();

        try {
            $data = $request->validated();

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
            \Log::error($e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            return redirect()->back()->withErrors(['error' => 'خطایی رخ داد. لطفاً مجدداً تلاش کنید.'])->withInput();
        }
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        try {
            auth()->user()->update(['password' => Hash::make($request->validated()['password'])]);

            return redirect()->route('profile.edit')->with('toast', [
                'message' => 'رمز عبور با موفقیت تغییر کرد.',
                'type'    => 'success',
                'title'   => 'تغییر رمز عبور'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors());
        } catch (\Exception $e) {
            \Log::error($e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            return redirect()->back()->withErrors(['error' => 'خطایی رخ داد. لطفاً مجدداً تلاش کنید.']);
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
