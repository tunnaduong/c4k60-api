<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class FeedController extends Controller
{
    public function index(Request $request)
    {
        // Define the items per page
        $itemsPerPage = 7;

        // Get the current page or default to 1
        $page = $request->query('page', 1);

        // Calculate the total number of items
        $totalItems = DB::table('tintuc_posts')->count();

        // Calculate the offset
        $offset = ($page - 1) * $itemsPerPage;

        // Fetch paginated results with author details
        $posts = DB::table('tintuc_posts')
            ->orderBy('timeofpost', 'desc')
            ->limit($itemsPerPage)
            ->offset($offset)
            ->get()
            ->map(function ($post) {
                // Fetch the author details from the User model/table
                $author = DB::table('c4_user')
                    ->select('name', 'verified', 'avatar', 'username')
                    ->where('username', $post->username) // Adjust 'user_id' based on your schema
                    ->first();

                $likes = DB::table('tintuc_post_likes')
                    ->where('liked_post_id', $post->id)
                    ->get()
                    ->map(function ($like) {
                        $user = DB::table('c4_user')
                            ->select('name as full_name', 'lastname as last_name', 'avatar')
                            ->where('username', $like->liked_username)
                            ->first();

                        return [
                            'like_id' => $like->like_id,
                            'liked_username' => $like->liked_username,
                            'time_of_like' => $like->time_of_like,
                            'liked_post_id' => $like->liked_post_id,
                            'user_detail' => $user ? [
                                'full_name' => $user->full_name,
                                'last_name' => $user->last_name,
                                'avatar' => $user->avatar,
                            ] : null, // Handle cases where user might not exist
                        ];
                    });

                return [
                    'id' => $post->id,
                    'content' => $post->content,
                    'timeofpost' => $post->timeofpost,
                    'image' => $post->image,
                    'author' => $author ? [
                        'name' => $author->name,
                        'username' => $author->username,
                        'verified' => $author->verified,
                        'avatar' => $author->avatar,
                    ] : null, // Handle cases where the author might be missing
                    'likes' => $likes->toArray(),
                ];
            });

        // Prepare the response
        return response()->json([
            'code' => 200,
            'total_items' => $totalItems,
            'page' => (int) $page,
            'items' => $posts,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Add a new feed post to the tintuc_posts table.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addPost(Request $request)
    {
        // Validate incoming request data
        try {
            $validatedData = $request->validate([
                'content' => 'required|string',
                'image' => 'nullable|file|mimes:jpg,jpeg,png,gif|max:10240', // max 10MB
                'username' => 'required|string',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        try {
            // Handle image upload if exists
            $imagePath = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();

                // Use Intervention Image to compress the image to 60% quality
                $imageInstance = Image::make($image);
                $imageInstance->save(storage_path('app/public/feed/' . $imageName), 60); // Save with 60% quality

                $imagePath = 'public/posts/' . $imageName; // Save the image path
            }

            // Insert the new post into the database
            $postId = DB::table('tintuc_posts')->insertGetId([
                'content' => $validatedData['content'],
                'image' => $imageName ? $imageName : null,
                'username' => $validatedData['username'],
                'timeofpost' => now(),
            ]);

            // Respond with success and the newly created post ID
            return response()->json([
                'code' => 200,
                'message' => 'Post added successfully.',
                'post_id' => $postId,
            ], 200);
        } catch (\Exception $e) {
            // Handle errors
            return response()->json([
                'code' => 500,
                'message' => 'Error adding post: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function handleLikes(Request $request)
    {
        if ($request->isMethod('post')) {
            // Add a like
            try {
                $validated = $request->validate([
                    'liked_post_id' => 'required|integer',
                    'liked_username' => 'required|string|max:255',
                ]);
            } catch (ValidationException $e) {
                return response()->json(['errors' => $e->errors()], 422);
            }

            try {
                DB::table('tintuc_post_likes')->insert([
                    'liked_post_id' => $validated['liked_post_id'],
                    'liked_username' => $validated['liked_username'],
                ]);

                return response()->json([
                    'code' => 200,
                    'message' => 'Successfully added like!',
                ], 200, [], JSON_UNESCAPED_UNICODE);
            } catch (\Exception $e) {
                return response()->json([
                    'code' => 500,
                    'message' => 'Server error! Please try again.',
                ], 500, [], JSON_UNESCAPED_UNICODE);
            }
        } elseif ($request->isMethod('delete')) {
            // Remove a like
            try {
                $validated = $request->validate([
                    'liked_post_id' => 'required|integer',
                    'liked_username' => 'required|string|max:255',
                ]);
            } catch (ValidationException $e) {
                return response()->json(['errors' => $e->errors()], 422);
            }

            try {
                $deleted = DB::table('tintuc_post_likes')
                    ->where('liked_post_id', $validated['liked_post_id'])
                    ->where('liked_username', $validated['liked_username'])
                    ->delete();

                if ($deleted) {
                    return response()->json([
                        'code' => 200,
                        'message' => 'Successfully deleted like!',
                    ], 200, [], JSON_UNESCAPED_UNICODE);
                }

                return response()->json([
                    'code' => 404,
                    'message' => 'Like not found.',
                ], 404, [], JSON_UNESCAPED_UNICODE);
            } catch (\Exception $e) {
                return response()->json([
                    'code' => 500,
                    'message' => 'Server error! Please try again.',
                ], 500, [], JSON_UNESCAPED_UNICODE);
            }
        } else {
            // Invalid method
            return response()->json([
                'code' => 400,
                'message' => 'Invalid request method!',
            ], 400, [], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Fetch likes based on post ID, username, or both.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLikes(Request $request)
    {
        $postId = $request->query('id', '');
        $username = $request->query('username', '');

        if (!empty($postId) && !empty($username)) {
            // Fetch likes by post ID and username
            $likes = DB::table('tintuc_post_likes')
                ->where('liked_post_id', $postId)
                ->where('liked_username', $username)
                ->get();
        } elseif (!empty($username)) {
            // Fetch likes by username
            $likes = DB::table('tintuc_post_likes')
                ->where('liked_username', $username)
                ->get();
        } elseif (!empty($postId)) {
            // Fetch likes by post ID
            $likes = DB::table('tintuc_post_likes')
                ->where('liked_post_id', $postId)
                ->get();
        } else {
            // No valid parameters provided
            return response()->json([
                'code' => 400,
                'message' => 'Invalid or missing query parameters!',
            ], 400);
        }

        // Return response
        return response()->json([
            'code' => 200,
            'items' => $likes,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
