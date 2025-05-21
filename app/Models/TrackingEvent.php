<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrackingEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_website_id', // Will be set based on API key
        'session_id',
        'event_type',
        'event_data',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_term',
        'utm_content',
        'initial_referrer',
        'current_url',
        'page_title',
        'screen_resolution',
        'user_agent',
        'browser_language',
        'browser_languages',
        'pixel_ratio',
        'domain',
        'ip_address', // Will be set server-side
        'event_timestamp',
    ];

    protected $casts = [
        'event_data' => 'array',
        'browser_languages' => 'array',
        'event_timestamp' => 'datetime',
        'pixel_ratio' => 'float', // Or 'decimal:2' if you prefer strings for exact decimal values
    ];
}
