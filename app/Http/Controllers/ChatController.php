<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Chat;
use App\Models\User;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ChatController extends Controller
{
    /**
     * Handle sending messages and updating conversations.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMessage(Request $request)
    {
        // Validate the incoming data
        try {
            $validatedData = $request->validate([
                'message' => 'required|string',
                'user_to' => 'required|string',
                'user_from' => 'required|string',
                'type' => 'required|string',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        $message = $validatedData['message'];
        $toId = $validatedData['user_to'];
        $fromId = $validatedData['user_from'];
        $type = $validatedData['type'];

        try {
            // Insert the message into the chat table
            $chat = Chat::create([
                'user_from' => $fromId,
                'user_to' => $toId,
                'message' => $message,
                'type' => $type,
            ]);

            // Check if it's the first conversation
            $conversationExists = Conversation::where(function ($query) use ($fromId, $toId) {
                $query->where('user_1', $fromId)->where('user_2', $toId);
            })->orWhere(function ($query) use ($fromId, $toId) {
                $query->where('user_2', $fromId)->where('user_1', $toId);
            })->exists();

            if (!$conversationExists && $toId !== 'class_group') {
                // Insert a new conversation
                Conversation::create([
                    'user_1' => $fromId,
                    'user_2' => $toId,
                ]);
                return response()->json(['status' => '200'], 200, [], JSON_UNESCAPED_UNICODE);
            } else {
                // Update the `updated_at` timestamp of the conversation
                $time = Carbon::now();
                Conversation::where(function ($query) use ($fromId, $toId) {
                    $query->where('user_1', $fromId)->where('user_2', $toId);
                })->orWhere(function ($query) use ($fromId, $toId) {
                    $query->where('user_2', $fromId)->where('user_1', $toId);
                })->update(['updated_at' => $time]);

                return response()->json([
                    'status' => '200',
                    'message' => 'msg_and_time_inserted_successfully',
                ], 200, [], JSON_UNESCAPED_UNICODE);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => '400',
                'message' => 'Error: ' . $e->getMessage(),
            ], 400, [], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Retrieve chat messages based on the user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMessages(Request $request)
    {
        $userTo = $request->query('user_to', 'class_group');
        $userFrom = $request->query('user_from', null);

        try {
            if ($userTo === 'class_group') {
                // Get messages for class_group
                $messages = Chat::where('user_to', 'class_group')
                    ->orderByDesc('id')
                    ->get();
            } else {
                // Get messages between user_from and user_to
                $messages = Chat::where(function ($query) use ($userFrom, $userTo) {
                    $query->where('user_to', $userTo)->where('user_from', $userFrom);
                })->orWhere(function ($query) use ($userFrom, $userTo) {
                    $query->where('user_to', $userFrom)->where('user_from', $userTo);
                })->orderByDesc('id')
                    ->get();
            }

            return response()->json($messages, 200, [], JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 400,
                'message' => $e->getMessage(),
            ], 400, [], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Get all conversations for the logged-in user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getConversations(Request $request)
    {
        $user = $request->query('username', '');

        if (empty($user)) {
            return response()->json([
                'code' => 400,
                'message' => 'Username is required.'
            ], 400);
        }

        try {
            // Retrieve all conversations involving the user
            $conversations = Conversation::where('user_1', $user)
                ->orWhere('user_2', $user)
                ->orderBy('updated_at', 'desc')
                ->get();

            // Fetch details for each user in the conversation
            $conversationData = [];
            foreach ($conversations as $conversation) {
                // Get the other user in the conversation
                $otherUser = ($conversation->user_1 === $user) ? $conversation->user_2 : $conversation->user_1;

                // Retrieve user data
                $userDetails = User::where('username', $otherUser)->first();
                if ($userDetails) {
                    $conversationData[] = $userDetails;
                }
            }

            return response()->json($conversationData, 200, [], JSON_UNESCAPED_UNICODE);

        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle chat image upload and message insertion.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadImage(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'user_to' => 'required|string',
                'user_from' => 'required|string',
                'type' => 'required|string',
                'image' => 'required|file|mimes:jpg,jpeg,png,gif|max:10240', // max 10MB
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        try {
            $toId = $validatedData['user_to'];
            $fromId = $validatedData['user_from'];
            $type = $validatedData['type'];

            // Handle image upload
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $imagePath = $image->storeAs('public/chats', $imageName);

            // Insert into chat table
            $chat = new Chat();
            $chat->user_from = $fromId;
            $chat->user_to = $toId;
            $chat->image_url = Storage::url($imagePath);
            $chat->type = $type;
            $chat->save();

            // Check if it's the first conversation
            $conversationExists = Conversation::where(function ($query) use ($fromId, $toId) {
                $query->where('user_1', $fromId)
                    ->where('user_2', $toId);
            })->orWhere(function ($query) use ($fromId, $toId) {
                $query->where('user_2', $fromId)
                    ->where('user_1', $toId);
            })->exists();

            if (!$conversationExists && $toId !== 'class_group') {
                $conversation = new Conversation();
                $conversation->user_1 = $fromId;
                $conversation->user_2 = $toId;
                $conversation->save();
            }

            return response()->json([
                'status' => '200',
                'message' => 'Image uploaded and message inserted successfully',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => '400',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function getLastMessage(Request $request)
    {
        try {
            // Validate the request parameters
            $validatedData = $request->validate([
                'user_from' => 'nullable|string',
                'user_to' => 'nullable|string',
                'type' => 'nullable|string|in:group,private',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        $userFrom = $validatedData['user_from'] ?? null;
        $userTo = $validatedData['user_to'] ?? null;
        $type = $validatedData['type'] ?? null;

        if ($type === "group") {
            // Fetch the latest message for the group
            $message = DB::table('chat')
                ->where('user_to', 'class_group')
                ->orderByDesc('id')
                ->first();

            return response()->json($message ? [$message] : [], 200, [], JSON_UNESCAPED_UNICODE);
        }

        // Fetch the latest private message between the users
        $message = DB::table('chat')
            ->where('user_to', '!=', 'class_group')
            ->where(function ($query) use ($userFrom, $userTo) {
                $query->where(function ($q) use ($userFrom, $userTo) {
                    $q->where('user_from', $userFrom)
                        ->where('user_to', $userTo);
                })->orWhere(function ($q) use ($userFrom, $userTo) {
                    $q->where('user_from', $userTo)
                        ->where('user_to', $userFrom);
                });
            })
            ->orderByDesc('id')
            ->first();

        return response()->json($message ? [$message] : [], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function getActivity()
    {
        // Fetch users sorted by last_activity in descending order
        $users = DB::table('c4_user')
            ->select('username', 'name', 'last_activity')
            ->orderByDesc('last_activity')
            ->get();

        return response()->json($users, 200, [], JSON_UNESCAPED_UNICODE);
    }
}
