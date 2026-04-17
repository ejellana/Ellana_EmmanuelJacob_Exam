<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        // Check if account is active
        if ($user && !$user->is_active) {
            return response()->json(['message' => 'Your account has been deactivated.'], 403);
        }

        // Check failed login attempts
        if ($user && $user->failed_login_attempts >= 5) {
            $lockTime = $user->last_failed_attempt_at->addMinutes(5);
            if (now()->lessThan($lockTime)) {
                return response()->json([
                    'message' => 'Account locked. Try again in 5 minutes.'
                ], 429);
            } else {
                // Reset attempts after lock time
                $user->update(['failed_login_attempts' => 0]);
            }
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            if ($user) {
                $user->increment('failed_login_attempts');
                $user->update(['last_failed_attempt_at' => now()]);
            }
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Reset failed attempts on successful login
        if ($user) {
            $user->update([
                'failed_login_attempts' => 0,
                'last_failed_attempt_at' => null
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user'    => $user,
            'token'   => $token,
            'role'    => $user->role
        ]);
    }
}
