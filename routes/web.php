<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BirthdayController;
use App\Http\Controllers\LiveRadioController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});