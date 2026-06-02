<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class OtpController extends Controller
{
    public function __invoke(User $user)
    {    
        $userId = session('pending_user_id');

        abort_unless($userId, 403);
    
        $user = User::findOrFail($userId);
    
        return view('auth.otp', [
            'user' => $user,
            'expiresAt' => $user->otp->expires_at,
        ]);
    
    }
}