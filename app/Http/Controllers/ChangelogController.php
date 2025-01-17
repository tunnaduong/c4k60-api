<?php

namespace App\Http\Controllers;

use App\Models\Changelog;
use Illuminate\Http\Request;

class ChangelogController extends Controller
{
    // Fetch all changelogs
    public function index()
    {
        $changelogs = Changelog::orderBy('release_date', 'desc')->get();
        return response()->json($changelogs, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function latest()
    {
        // Get the latest changelog, ordered by release_date descending
        $latestChangelog = Changelog::orderBy('release_date', 'desc')->first();

        return response()->json($latestChangelog, 200, [], JSON_UNESCAPED_UNICODE);
    }
}
