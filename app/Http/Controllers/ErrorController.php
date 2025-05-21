<?php

// app/Http/Controllers/Api/ErrorController.php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\FrontendError;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ErrorController extends Controller
{
    public function store(Request $request)
    {
        // Log::info('Frontend error report received:', $request->all());

        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
            'stack' => 'nullable|string',
            'page_url' => 'required|url|max:2048',
        ]);

        if ($validator->fails()) {
            Log::warning('Frontend error validation failed:', [
                'errors' => $validator->errors()->toArray(),
                'data' => $request->all()
            ]);
            // For error reporting, even if validation fails, you might want to log what you received.
            // Don't return 422 here, as the client script might not handle it. Just accept and log.
            // A 200 or 204 (No Content) is fine.
        }

        $validatedData = $validator->validated(); // Get only validated data

        // Retrieve client_website_id from the authenticated API key logic
        $clientWebsiteId = $request->attributes->get('client_website_id', 'unknown_client_error');

        try {
            FrontendError::create([
                'client_website_id' => $clientWebsiteId,
                'message' => Arr::get($validatedData, 'message', $request->input('message', 'Unknown error message')), // Get validated or fallback
                'stack' => Arr::get($validatedData, 'stack', $request->input('stack')),
                'page_url' => Arr::get($validatedData, 'page_url', $request->input('page_url', 'Unknown page URL')),
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
            ]);

            return response()->json(['message' => 'Error reported successfully'], 201); // Or 204 No Content
        } catch (\Exception $e) {
            Log::error('Error storing frontend error report:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $request->all() // Log the original data
            ]);
            // Don't want to cause an error cascade.
            return response()->json(['message' => 'Failed to store error report'], 500);
        }
    }
}
