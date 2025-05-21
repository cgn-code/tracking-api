<?php

// app/Http/Middleware/AuthenticateApiKey.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ClientWebsite; // Make sure this is imported
use Illuminate\Support\Facades\Log;

class AuthenticateApiKey
{
    public function handle(Request $request, Closure $next)
    {
        $submittedApiKey = $request->header('X-API-KEY'); // Or $request->bearerToken() if you use Bearer tokens

        if (empty($submittedApiKey)) {
            Log::channel('security')->warning('API key missing.', [ // Consider a dedicated security log channel
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
            ]);
            return response()->json(['message' => 'API key required.'], 401);
        }

        // Find the client by the submitted API key
        $clientWebsite = ClientWebsite::where('api_key', $submittedApiKey)->first();

        if (!$clientWebsite) {
            Log::channel('security')->warning('Invalid API key submitted.', [
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
                'submitted_key_partial' => substr($submittedApiKey, 0, 8) . '...' . substr($submittedApiKey, -4)
            ]);
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        if (!$clientWebsite->is_active) {
            Log::channel('security')->warning('Inactive API key used.', [
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
                'client_id' => $clientWebsite->id,
                'client_name' => $clientWebsite->name,
            ]);
            return response()->json(['message' => 'API key is inactive.'], 403); // 403 Forbidden
        }

        // Add client_website_id and other client info to the request attributes
        // The 'id' of the clientWebsite record is our 'client_website_id' for tracking_events
        $request->attributes->add(['client_website_id' => $clientWebsite->id]);
        $request->attributes->add(['client_name' => $clientWebsite->name]); // Optional: if controllers need it
        $request->attributes->add(['client_domain' => $clientWebsite->domain_url]); // Optional

        return $next($request);
    }
}
