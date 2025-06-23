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

Route::get('/session-collect', function (Request $request) {
    $query = \App\Models\TrackingEvent::query();
    
    // Filter by domain
    if ($request->has('domain')) {
        $query->where('domain', $request->get('domain'));
    }
    
    // Filter by session_id
    if ($request->has('session_id')) {
        $query->where('session_id', $request->get('session_id'));
    }
    
    // Filter by date range
    if ($request->has('date_from')) {
        $query->whereDate('event_timestamp', '>=', $request->get('date_from'));
    }
    
    if ($request->has('date_to')) {
        $query->whereDate('event_timestamp', '<=', $request->get('date_to'));
    }
    
    // Filter by event type
    if ($request->has('event_type')) {
        $query->where('event_type', $request->get('event_type'));
    }
    
    // Order by timestamp
    $query->orderBy('event_timestamp', 'asc');
    
    // Limit results to prevent large responses
    $query->limit($request->get('limit', 1000));
    
    $events = $query->get();
    
    return response()->json($events);
});
