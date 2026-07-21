<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TenantManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthApiController extends Controller
{
    public function __construct(private TenantManager $manager) {}

    /** صدور API token برای کاربر احراز هویت‌شده */
    public function token(Request $request)
    {
        $request->validate([
            'email'       => 'required|string',
            'password'    => 'required|string',
            'device_name' => 'required|string|max:100',
        ]);

        $user = \App\Models\User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'اطلاعات ورود نادرست است.'], 401);
        }

        if (!$user->is_active) {
            return response()->json(['message' => 'حساب کاربری غیرفعال است.'], 403);
        }

        // لغو توکن‌های قدیمی همین دستگاه
        $user->tokens()->where('name', $request->device_name)->delete();

        $token = $user->createToken($request->device_name, ['api'])->plainTextToken;

        return response()->json([
            'token'      => $token,
            'token_type' => 'Bearer',
            'user'       => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    /** ابطال token جاری */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'با موفقیت خارج شدید.']);
    }

    /** اطلاعات کاربر جاری */
    public function me(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'id'     => $user->id,
            'name'   => $user->name,
            'email'  => $user->email,
            'mobile' => $user->mobile,
        ]);
    }
}
