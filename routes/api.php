<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ErrorController;
use App\Http\Controllers\TrackingController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::middleware('auth.apikey')->post('/session-collect', [TrackingController::class, 'store']);
Route::middleware('auth.apikey')->post('/error', [ErrorController::class, 'store']);
