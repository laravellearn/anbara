<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;

class ResetPasswordController extends Controller
{
    public function __invoke(Request $request)
    {
        abort_unless(
            session('password_reset_verified'),
            403
        );

        $request->validate([

            'password' => [

                'required',

                'confirmed',

                Password::min(8)
                    ->mixedCase()
                    ->numbers(),
            ]
        ]);

        $user = User::findOrFail(
            session('reset_password_user_id')
        );

        $user->update([
            'password' => Hash::make(
                $request->password
            )
        ]);

        Auth::login($user);

        $request->session()->regenerate();

        session()->forget([
            'reset_password_user_id',
            'password_reset_verified'
        ]);

        return redirect()->route('admin.dashboard');
    }
}