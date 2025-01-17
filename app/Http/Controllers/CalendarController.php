<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CalendarController extends Controller
{
    public function getCalendarEvents()
    {
        try {
            // Fetch all records from the 'calendar' table and order by 'id' descending
            $events = DB::table('calendar')
                ->orderByDesc('id')
                ->get();

            return response()->json($events, 200, [], JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
