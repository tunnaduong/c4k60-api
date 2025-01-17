<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FeedController extends Controller
{
    public function index(Request $request)
    {
        // Define the items per page
        $itemsPerPage = 4;

        // Get the current page or default to 1
        $page = $request->query('page', 1);

        // Calculate the total number of items
        $totalItems = DB::table('tintuc_posts')->count();

        // Calculate the offset
        $offset = ($page - 1) * $itemsPerPage;

        // Fetch paginated results
        $posts = DB::table('tintuc_posts')
            ->orderBy('timeofpost', 'desc')
            ->limit($itemsPerPage)
            ->offset($offset)
            ->get();

        // Prepare the response
        return response()->json([
            'code' => 200,
            'total_items' => $totalItems,
            'page' => (int) $page,
            'items' => $posts,
        ], 200, [], JSON_UNESCAPED_UNICODE);
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
