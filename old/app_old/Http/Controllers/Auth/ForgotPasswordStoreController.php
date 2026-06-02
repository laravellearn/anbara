<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use Illuminate\Support\Facades\Password;

class ForgotPasswordStoreController extends Controller
{
    public function __invoke(ForgotPasswordRequest $request)
    {
        $status = Password::sendResetLink([
            'email' => $request->email
        ]);

        return $status === Password::RESET_LINK_SENT

            ? back()->with('success', __($status))

            : back()->withErrors([
                'email' => __($status)
            ]);
    }
}