<?php

namespace App\Http\Controllers;

use App\Models\LiveRadio;
use Illuminate\Http\Request;

class LiveRadioController extends Controller
{
    public function getIdlePlaylist()
    {
        // Retrieve all video IDs from the live_radio_idle_playlist table
        $idlePlaylist = (new LiveRadio())->setTableAndFillable(
            'live_radio_idle_playlist',
            ['id', 'video_id']
        );
        $videos = $idlePlaylist->pluck('video_id');

        if ($videos->isNotEmpty()) {
            return response()->json([
                'idle_playlist' => $videos,
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }

        return response()->json([
            'message' => 'No video or server error!',
        ], 404, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Handle log creation (POST) and retrieval (GET).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleLogs(Request $request)
    {
        if ($request->isMethod('post')) {
            return $this->createLog($request);
        }

        if ($request->isMethod('get')) {
            return $this->getLogs($request);
        }

        return response()->json([
            'code' => 405,
            'message' => 'Method Not Allowed',
        ], 405);
    }

    /**
     * Create a new log entry.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    private function createLog(Request $request)
    {
        $validated = $request->validate([
            'by' => 'required|string',
            'msg_type' => 'required|string',
            'message' => 'required|string',
            'thumbnail' => 'nullable|string',
        ]);

        $log = (new LiveRadio())->setTableAndFillable(
            'live_radio_logs',
            ['id', 'created_by', 'msg_type', 'msg', 'thumbnail', 'time']
        )->create([
                    'created_by' => $validated['by'],
                    'msg_type' => $validated['msg_type'],
                    'msg' => $validated['message'],
                    'thumbnail' => $validated['thumbnail'] ?? '',
                ]);

        if ($log) {
            return response()->json([
                'code' => 200,
                'message' => 'Successfully added chatlog!',
            ], 200);
        }

        return response()->json([
            'code' => 500,
            'message' => 'Wrong API parameters or server error! Please try again.',
        ], 500);
    }

    /**
     * Retrieve log entries with pagination.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    private function getLogs(Request $request)
    {
        $itemsPerPage = 30;
        $page = $request->query('page', 1);

        $totalItems = (new LiveRadio())->setTableAndFillable(
            'live_radio_logs',
            ['id', 'created_by', 'msg_type', 'msg', 'thumbnail', 'time']
        )->count();
        $logs = (new LiveRadio())->setTableAndFillable(
            'live_radio_logs',
            ['id', 'created_by', 'msg_type', 'msg', 'thumbnail', 'time']
        )->orderBy('id', 'desc')
            ->skip(($page - 1) * $itemsPerPage)
            ->take($itemsPerPage)
            ->get();

        if ($logs->isNotEmpty()) {
            return response()->json([
                'code' => 200,
                'total_items' => $totalItems,
                'page' => (int) $page,
                'items' => $logs,
            ], 200);
        }

        return response()->json([
            'code' => 400,
            'message' => 'No result.',
        ], 400);
    }
}
