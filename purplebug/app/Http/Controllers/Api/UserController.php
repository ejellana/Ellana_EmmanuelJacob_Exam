<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::select('id', 'full_name', 'email', 'role', 'is_active', 'email_verified_at', 'created_at')
                    ->latest()
                    ->get();
        return response()->json($users);
    }

    public function store(RegisterRequest $request)
    {
        $user = User::create([
            'name'              => $request->full_name,
            'full_name'         => $request->full_name,
            'email'             => $request->email,
            'password'          => Hash::make($request->password),
            'role'              => 'guest',
            'is_active'         => true,
        ]);

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user
        ], 201);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email,' . $user->id,
        ]);

        $user->update([
            'name'       => $request->full_name,
            'full_name'  => $request->full_name,
            'email'      => $request->email,
        ]);

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user
        ]);
    }

    public function destroy(User $user)
    {
        if ($user->email === 'admin@purplebug.com') {
            return response()->json(['message' => 'Cannot delete main admin account'], 403);
        }

        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }

    public function toggleActive(User $user)
    {
        if ($user->email === 'admin@purplebug.com') {
            return response()->json(['message' => 'Cannot deactivate main admin'], 403);
        }

        $user->update(['is_active' => !$user->is_active]);

        return response()->json([
            'message' => $user->is_active ? 'User activated' : 'User deactivated',
            'is_active' => $user->is_active
        ]);
    }
}
