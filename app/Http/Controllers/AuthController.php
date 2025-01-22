<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        $user = User::where('username', $request->username)->first();

        if (!$user || $user->password !== $request->password) {
            return response()->json(['Message' => 'Sai thông tin đăng nhập!'], 401);
        }

        return response()->json([
            'Message' => 'Thành công!',
            'Username' => $user->username,
            'Name' => $user->name,
            'Firstname' => $user->firstname,
            'Lastname' => $user->lastname,
            'Level' => $user->role,
            'Avatar' => $user->avatar,
        ]);
    }

    public function changePassword(Request $request)
    {
        // Validate input data
        try {
            $validatedData = $request->validate([
                'username' => 'required|string',
                'old_password' => 'required|string',
                'new_password' => 'required|string|min:6',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        // Retrieve user by username
        $user = User::where('username', $validatedData['username'])->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Verify old password
        if ($user->password !== $validatedData['old_password']) {
            return response()->json(['error' => 'Old password is incorrect'], 401);
        }

        // Update password
        $user->password = $validatedData['new_password'];
        $user->save();

        return response()->json(['message' => 'Password changed successfully'], 200);
    }
}