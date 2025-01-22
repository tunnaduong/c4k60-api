<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $username = $request->query('username');

        if ($username) {
            // Fetch user by username
            $user = User::where('username', $username)->first();

            if ($user) {
                return response()->json($user, 200, [], JSON_UNESCAPED_UNICODE);
            } else {
                return response()->json(['error' => 'User not found'], 404);
            }
        } else {
            // Fetch all users
            $users = User::all();

            if ($users->isEmpty()) {
                return response()->json(['error' => 'No users found'], 404);
            }

            return response()->json($users, 200, [], JSON_UNESCAPED_UNICODE);
        }
    }

    // Get user avatar
    public function getAvatar($username)
    {
        // Retrieve user by username
        $user = User::where('username', $username)->firstOrFail();

        if (!$user || !$user->avatar) {
            return response()->json(['error' => 'User or avatar not found'], 404);
        }

        // Replace the base URL in the avatar path
        $avatarPath = str_replace("https://c4k60.com/", base_path() . "/", $user->avatar);

        // Check if the file exists
        if (!file_exists($avatarPath)) {
            return response()->json(['error' => 'Avatar file not found'], 404);
        }

        // Open the file in binary mode and return it
        $file = fopen($avatarPath, 'rb');
        $fileSize = filesize($avatarPath);

        return response()->stream(function () use ($file) {
            fpassthru($file);
        }, 200, [
            "Content-Type" => mime_content_type($avatarPath),
            "Content-Length" => $fileSize,
        ]);
    }

    public function updateLastActivity(Request $request)
    {
        $username = $request->input('username');
        $date = now(); // Current date and time

        if (!empty($username)) {
            $updated = DB::table('c4_user')
                ->where('username', $username)
                ->update(['last_activity' => $date]);

            if ($updated) {
                return response()->json([
                    'status' => 'success',
                ], 200, [], JSON_UNESCAPED_UNICODE);
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Failed to update the user or user not found',
                ], 500, [], JSON_UNESCAPED_UNICODE);
            }
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Username cannot be blank!',
            ], 400, [], JSON_UNESCAPED_UNICODE);
        }
    }

    public function changeAvatar(Request $request)
    {
        // Validate input data
        try {
            $validatedData = $request->validate([
                'username' => 'required|string',
                'avatar' => 'required|image|mimes:jpg,jpeg,png,gif|max:10240', // max 10MB
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        // Retrieve user by username
        $user = User::where('username', $validatedData['username'])->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Handle avatar upload
        $avatar = $request->file('avatar');
        $avatarName = time() . '_' . $avatar->getClientOriginalName();
        $avatarPath = 'avatars/' . $avatarName;

        // Compress and save the image
        $image = Image::make($avatar)->encode('jpg', 60); // Compress to 60%
        Storage::disk('public')->put($avatarPath, (string) $image);

        if (!Storage::disk('public')->exists($avatarPath)) {
            return response()->json(['errors' => ['avatar' => ['The avatar failed to upload.']]], 422);
        }

        // Update user's avatar URL
        $user->avatar = 'https://api.c4k60.com/' . Storage::url($avatarPath);
        $user->save();

        return response()->json(['message' => 'Avatar updated successfully', 'avatar_url' => $user->avatar], 200);
    }

    public function getUserInfo(Request $request)
    {
        // Validate input data
        try {
            $validatedData = $request->validate([
                'username' => 'required|string'
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        // Retrieve user info from database
        $user = User::where('username', $validatedData['username'])->first();

        // Check if user exists
        if ($user) {
            $dob = $user->dayofbirth . '/' . $user->monthofbirth . '/' . $user->yearofbirth;
            $response = [
                'code' => 200,
                'message' => 'Successfully retrieved user\'s info!',
                'info' => [
                    'username' => $user->username,
                    'full_name' => $user->name,
                    'first_name' => $user->firstname,
                    'last_name' => $user->lastname,
                    'date_of_birth' => $dob,
                    'role' => $user->role,
                    'gender' => $user->gender,
                ]
            ];
        } else {
            $response = [
                'code' => 400,
                'message' => 'No account found!',
            ];
        }

        return response()->json($response);
    }
}
