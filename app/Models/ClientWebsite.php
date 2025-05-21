<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; // For API key generation

class ClientWebsite extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'domain_url',
        'api_key',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($clientWebsite) {
            $clientWebsite->api_key = static::generateApiKey();
        });
    }

    /**
     * Generate a unique API key.
     * Consider prefixing keys, e.g., 'cgn_trk_'.
     */
    public static function generateApiKey(): string
    {
        do {
            // Generate a secure random string.
            // Using Str::random(60) gives a good balance of length and character set.
            // For higher security, you might consider base64_encode(random_bytes(45)) for 60 characters.
            $apiKey = 'cgn_trk_' . Str::random(60); // Example prefix + random part
        } while (static::where('api_key', $apiKey)->exists()); // Ensure uniqueness

        return $apiKey;
    }
}
