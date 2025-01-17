<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GalleryController extends Controller
{
    public function index()
    {
        // Fetch all albums
        $albums = (new Gallery())->setTableAndFillable("album", ['id', 'name', 'bg_image', 'total_pic', 'type'])::select('id', 'name', 'bg_image', 'total_pic', 'type')->get();

        // Check if albums exist
        if ($albums->isEmpty()) {
            return response()->json([
                'error' => 'no_albums_found'
            ], 404);
        }

        // Return the albums as a JSON response
        return response()->json($albums, JSON_UNESCAPED_UNICODE);
    }

    public function getPhotos(Request $request)
    {
        $album = $request->query('album', '0'); // Default to 0 if no album is provided

        // Fetch images for the specified album
        $images = DB::table('thuvienanh')
            ->where('album', $album)
            ->get();

        // If no images are found, return an error message
        if ($images->isEmpty()) {
            return response()->json([
                'error' => 'no_images_found_for_this_album'
            ], 404);
        }

        // Return the images as a JSON response
        return response()->json($images, JSON_UNESCAPED_UNICODE);
    }

    public function getVideos()
    {
        // Fetch all videos from the "videos" table
        $videos = DB::table('videos')->get();

        // Check if any videos exist
        if ($videos->isEmpty()) {
            return response()->json([
                'error' => 'no_videos_found',
            ], 404);
        }

        // Return the videos as a JSON response
        return response()->json($videos, JSON_UNESCAPED_UNICODE);
    }
}
