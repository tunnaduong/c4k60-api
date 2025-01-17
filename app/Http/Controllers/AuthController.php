<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


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

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Successfully logged out']);
    }
}