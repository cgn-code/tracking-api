<?php

namespace App\Http\Controllers;

use App\Models\TrackingEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Arr; // For Arr::get, though not strictly needed if using $validatedData directly

class TrackingController extends Controller
{
    /**
     * Store a new tracking event.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // For debugging during development, you can log the raw request.
        // Log::debug('TrackingController request received:', $request->all());

        $validator = Validator::make($request->all(), [
            'event_type' => 'required|string|max:100|in:pageview,cta_click,phone_call_attempt,form_submission,page_hidden,page_visible,page_unload',
            'event_data' => 'nullable|array',
            // Optional, more specific validation for event_data sub-fields:
            'event_data.page_type' => 'sometimes|string|max:255',
            'event_data.submission_method' => 'sometimes|string|max:255',
            'event_data.form_name' => 'sometimes|string|max:255',
            'event_data.thank_you_page_url' => 'sometimes|nullable|url|max:2048',
            'event_data.element_tag' => 'sometimes|string|max:50',
            'event_data.element_text' => 'sometimes|string|max:1000', // Increased max length for element_text
            'event_data.element_cta_id' => 'sometimes|nullable|string|max:255',
            'event_data.phone_number' => 'sometimes|string|max:50',
            'event_data.element_href' => 'sometimes|nullable|string|max:2048',

            // UTM Parameters
            'utm_source' => 'nullable|string|max:255',
            'utm_medium' => 'nullable|string|max:255',
            'utm_campaign' => 'nullable|string|max:255',
            'utm_term' => 'nullable|string|max:255',
            'utm_content' => 'nullable|string|max:255',

            // Contextual Information
            'initial_referrer' => 'nullable|string|max:2048',
            'current_url' => 'required|url|max:2048',
            'page_title' => 'nullable|string|max:1000', // Increased max length
            'screen_resolution' => 'nullable|string|max:20',
            'user_agent' => 'nullable|string|max:1000', // Increased max length
            'browser_language' => 'nullable|string|max:50',
            'browser_languages' => 'nullable|array',
            'browser_languages.*' => 'string|max:50',
            'pixel_ratio' => 'nullable|numeric',
            'domain' => 'nullable|string|max:255',
            'session_id' => 'required|string|max:100',
            'timestamp' => 'required', // ISO 8601 format (e.g., 2025-05-21T18:30:00.000Z)
        ]);

        if ($validator->fails()) {
            Log::warning('Tracking validation failed:', [
                'errors' => $validator->errors()->toArray(),
                'data' => $request->all() // Log the problematic data
            ]);
            return response()->json(['message' => 'Validation failed.', 'errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        // Retrieve client_website_id from the request attributes set by AuthenticateApiKey middleware
        $clientWebsiteId = $request->attributes->get('client_website_id');

        // This check is crucial. If middleware failed or was misconfigured, this would be null.
        if (!$clientWebsiteId) {
            Log::error('Client Website ID not found in request attributes after API key auth.', [
                'url' => $request->fullUrl(),
                'ip' => $request->ip(),
                'headers' => $request->headers->all() // Log headers for debugging auth issues
            ]);
            // Return a generic server error as this is a server-side configuration issue.
            return response()->json(['message' => 'Server configuration error.'], 500);
        }

        try {
            TrackingEvent::create([
                'client_website_id' => $clientWebsiteId,
                'session_id' => $validatedData['session_id'],
                'event_type' => $validatedData['event_type'],
                'event_data' => $validatedData['event_data'] ?? [], // Default to empty array if null
                'utm_source' => $validatedData['utm_source'] ?? null,
                'utm_medium' => $validatedData['utm_medium'] ?? null,
                'utm_campaign' => $validatedData['utm_campaign'] ?? null,
                'utm_term' => $validatedData['utm_term'] ?? null,
                'utm_content' => $validatedData['utm_content'] ?? null,
                'initial_referrer' => $validatedData['initial_referrer'] ?? null,
                'current_url' => $validatedData['current_url'],
                'page_title' => $validatedData['page_title'] ?? null,
                'screen_resolution' => $validatedData['screen_resolution'] ?? null,
                'user_agent' => $validatedData['user_agent'] ?? $request->header('User-Agent'), // Fallback to request header if not sent in payload
                'browser_language' => $validatedData['browser_language'] ?? null,
                'browser_languages' => $validatedData['browser_languages'] ?? [],
                'pixel_ratio' => $validatedData['pixel_ratio'] ?? null,
                'domain' => $validatedData['domain'] ?? null, // This is from payload, could also parse from current_url
                'ip_address' => $request->ip(), // Always capture server-side for accuracy
                'event_timestamp' => $validatedData['timestamp'], // Use the client-provided timestamp
            ]);

            return response()->json(['message' => 'Data tracked successfully.'], 201); // 201 Created
        } catch (\Exception $e) {
            Log::error('Error storing tracking data:', [
                'client_website_id' => $clientWebsiteId,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(), // For detailed debugging
                'validated_data' => $validatedData // Log the data that was attempted to be stored
            ]);
            return response()->json(['message' => 'An error occurred while processing your request.'], 500);
        }
    }
}
