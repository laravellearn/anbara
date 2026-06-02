<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;

class ResetPasswordStoreController extends Controller
{
    public function __invoke(
        ResetPasswordRequest $request
    )
    {
        $status = Password::reset(
            $request->only(
                'email',
                'password',
                'password_confirmation',
                'token'
            ),
            function (User $user, string $password) {

                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(
                    new PasswordReset($user)
                );
            }
        );

        return $status === Password::PASSWORD_RESET

            ? redirect()
                ->route('login')
                ->with(
                    'success',
                    'رمز عبور با موفقیت تغییر یافت.'
                )

            : back()->withErrors([
                'email' => __($status)
            ]);
    }
}