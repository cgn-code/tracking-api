<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('client_websites', function (Blueprint $table) {
            $table->id(); // This 'id' will be used as 'client_website_id' in tracking_events
            $table->string('name'); // A friendly name for the client, e.g., "Client A Inc."
            $table->string('domain_url')->nullable(); // e.g., "https://clienta.com"
            $table->string('api_key')->unique()->index(); // The unique API key
            $table->boolean('is_active')->default(true); // To enable/disable a key
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('client_websites');
    }
};
