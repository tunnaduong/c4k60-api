<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class NotificationController extends Controller
{
    /**
     * Fetch images for the given ID.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getImages(Request $request)
    {
        // // Validate the request input
        // $validated = $request->validate([
        //     'id' => 'required|integer',
        // ]);

        $id = $request->input('id');

        // Fetch the image column from the thongbaolop table
        $result = DB::table('thongbaolop')->where('id', $id)->value('image');

        if ($result) {
            // Split the images by commas and prepare the response
            $images = explode(',', $result);
            $response = array_map(fn($image) => ['uri' => $image], $images);

            return response()->json($response, 200);
        }

        // Return an error response if no matching ID is found
        return response()->json([
            'error' => 'access_denied',
            'requested' => $id,
        ], 404);
    }

    public function getNotifications(Request $request)
    {
        if ($request->has('show') && $request->get('show') == 'all') {
            // Get all notifications
            $notifications = Notification::orderBy('id', 'desc')->get();
            $totalNotification = Notification::count();

            $response = [
                'total' => $totalNotification,
                'results' => []
            ];

            foreach ($notifications as $notification) {
                $images = explode(",", $notification->image);
                $formattedImages = [];
                foreach ($images as $index => $image) {
                    $formattedImages[] = [
                        'img_id' => $index + 1,
                        'url' => $image
                    ];
                }

                $response['results'][] = [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'content' => $notification->content,
                    'createdBy' => $notification->createdBy,
                    'image' => $formattedImages,
                    'date' => $notification->date
                ];
            }

            // Hide more notifications flag
            $response['hideMore'] = true;

            return response()->json($response, 200);
        } else {
            // Show top 5 notifications ordered by id desc if 'show' is not 'all'
            $notifications = Notification::orderBy('id', 'desc')->take(5)->get();
            $totalNotification = Notification::count();
            $otherNotifications = $totalNotification - 5; // Calculate other notifications

            $response = [
                'total' => $totalNotification,
                'results' => [],
                'otherNotifications' => $otherNotifications // Add the otherNotifications field
            ];

            foreach ($notifications as $notification) {
                $images = explode(",", $notification->image);
                $formattedImages = [];
                foreach ($images as $index => $image) {
                    $formattedImages[] = [
                        'img_id' => $index + 1,
                        'url' => $image
                    ];
                }

                $response['results'][] = [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'content' => $notification->content,
                    'createdBy' => $notification->createdBy,
                    'image' => $formattedImages,
                    'date' => $notification->date
                ];
            }

            // Return the response with the top 5 notifications
            return response()->json($response, 200);
        }
    }

    public function updateToken(Request $request)
    {
        // Validate incoming data
        try {
            $validated = $request->validate([
                'username' => 'required|string',
                'token' => 'required|string',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        $username = $validated['username'];
        $token = $validated['token'];

        // Find the user by username
        $user = User::where('username', $username)->first();

        // Check if user exists
        if (!$user) {
            return response()->json([
                'error' => 'user_not_found'
            ], 404);
        }

        // If the token is already in the list, return a message
        if (strpos($user->expo_push_notification_token, $token) !== false) {
            return response()->json([
                'message' => 'duplicate_token_do_nothing'
            ]);
        }

        // If not, add the token to the existing ones (comma-separated)
        if ($user->expo_push_notification_token) {
            $user->expo_push_notification_token .= ',' . $token;
        } else {
            $user->expo_push_notification_token = $token;
        }

        // Save the updated token list
        $user->save();

        return response()->json([
            'message' => 'updated_token_successfully',
            'token' => $token
        ]);
    }

    public function sendNotification(Request $request)
    {
        // Validate incoming request
        try {
            $validated = $request->validate([
                'to' => 'required|string',
                'title' => 'required|string',
                'body' => 'required|string',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        $to = $validated['to'];
        $title = $validated['title'];
        $body = $validated['body'];

        // If 'to' is not ALL, send notification to specific user
        if ($to !== 'ALL') {
            $user = User::where('username', $to)->first();

            if (!$user) {
                return response()->json([
                    'error' => 'username_not_found'
                ], 404);
            }

            $tokens = explode(',', $user->expo_push_notification_token);
            foreach ($tokens as $token) {
                if (!empty($token)) {
                    $this->sendPushNotification($token, $title, $body);
                }
            }

            return response()->json([
                'message' => 'notification_sent_successfully'
            ]);
        }

        // If 'to' is ALL, send notification to all users
        $users = User::whereNotNull('expo_push_notification_token')->get();

        if ($users->isEmpty()) {
            return response()->json([
                'error' => 'no_users_with_tokens'
            ], 404);
        }

        foreach ($users as $user) {
            $tokens = explode(',', $user->expo_push_notification_token);
            foreach ($tokens as $token) {
                if (!empty($token)) {
                    $this->sendPushNotification($token, $title, $body, false);
                }
            }
        }

        return response()->json([
            'message' => 'sent_notification_to_all_success'
        ]);
    }

    private function sendPushNotification($to, $title, $body, $return = true)
    {
        $url = "https://exp.host/--/api/v2/push/send";

        $message = [
            "to" => $to,
            "sound" => "default",
            "title" => $title,
            "body" => $body,
        ];

        $jsonMessage = json_encode($message);

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Host: exp.host',
            'Accept: application/json',
            'Accept-Encoding: gzip, deflate',
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonMessage);

        $response = curl_exec($ch);

        if ($return) {
            if (curl_errno($ch)) {
                return response()->json([
                    'error' => 'curl_error',
                    'message' => curl_error($ch)
                ], 500);
            } else {
                return response()->json([
                    'message' => 'notification_sent',
                    'response' => $response
                ]);
            }
        }

        curl_close($ch);
    }
}
