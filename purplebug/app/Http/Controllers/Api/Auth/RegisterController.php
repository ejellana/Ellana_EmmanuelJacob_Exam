<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name'          => $request->full_name,
            'full_name'     => $request->full_name,
            'email'         => $request->email,
            'password'      => Hash::make($request->password),
            'role'          => 'guest',
            'is_active'     => true,
        ]);

        $user->sendEmailVerificationNotification();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful. Please check your email to verify your account.',
            'user'    => $user,
            'token'   => $token,
            'role'    => $user->role
        ], 201);
    }
}
