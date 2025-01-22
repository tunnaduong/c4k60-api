<?php

use App\Http\Controllers\CalendarController;
use App\Http\Controllers\ChangelogController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\SponsorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BirthdayController;
use App\Http\Controllers\LiveRadioController;
use App\Http\Controllers\NotificationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v2.0')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::any('/users', [UserController::class, 'getUserInfo']);
    Route::get('/users/avatar/get/{username}', [UserController::class, 'getAvatar']);
    Route::post('/users/avatar/upload', [UserController::class, 'changeAvatar']);
    Route::get('/users/birthday', [BirthdayController::class, 'index']);
    Route::get('/users/list', [UserController::class, 'index']);
    Route::get('/users/online', [UserController::class, 'updateLastActivity']);
    Route::any('/users/change-password', [AuthController::class, 'changePassword']);

    Route::get('/radio/idle', [LiveRadioController::class, 'getIdlePlaylist']);
    Route::any('/radio/chatlogs', [LiveRadioController::class, 'handleLogs']);

    Route::any('/notification/image', [NotificationController::class, 'getImages']);
    Route::get('/notification/list', [NotificationController::class, 'getNotifications']);
    Route::any('/notification/token', [NotificationController::class, 'updateToken']);
    Route::any('/notification/send', [NotificationController::class, 'sendNotification']);

    Route::get('/gallery', [GalleryController::class, 'index']);
    Route::get('/gallery/photos', [GalleryController::class, 'getPhotos']);
    Route::get('/gallery/videos', [GalleryController::class, 'getVideos']);

    Route::get('/feed/list', [FeedController::class, 'index']);
    Route::any('/feed/likes/add', [FeedController::class, 'handleLikes']);
    Route::get('/feed/likes', [FeedController::class, 'getLikes']);
    Route::any('/feed/add', [FeedController::class, 'addPost']);
    Route::any('/feed/comments/add', [FeedController::class, 'addComment']);

    Route::any('/chat/conversations', [ChatController::class, 'sendMessage']);
    Route::any('/chat/messages', [ChatController::class, 'getMessages']);
    Route::get('/chat/home', [ChatController::class, 'getConversations']);
    Route::post('/chat/image', [ChatController::class, 'uploadImage']);
    Route::get('/chat/last-chat', [ChatController::class, 'getLastMessage']);
    Route::get('/chat/online', [ChatController::class, 'getActivity']);

    Route::get('/calendar/list', [CalendarController::class, 'getCalendarEvents']);

    Route::get('/sponsors', [SponsorController::class, 'index']);

    Route::get('/changelogs', [ChangelogController::class, 'index']);
    Route::get('/changelogs/latest', [ChangelogController::class, 'latest']);
});