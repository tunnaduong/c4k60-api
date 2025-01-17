<?php

namespace App\Http\Controllers;

use App\Models\Sponsor;

class SponsorController extends Controller
{
    // Fetch all donators
    public function index()
    {
        $donators = Sponsor::all();
        return response()->json($donators, 200, [], JSON_UNESCAPED_UNICODE);
    }
}
