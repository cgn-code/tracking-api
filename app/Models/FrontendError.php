<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FrontendError extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_website_id',
        'message',
        'stack',
        'page_url',
        'ip_address',
        'user_agent',
    ];
}
