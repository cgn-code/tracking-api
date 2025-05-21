<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tracking_events', function (Blueprint $table) {
            $table->id();
            $table->string('client_website_id')->index(); // Your internal ID for the client website, linked to API key
            $table->string('session_id')->index();        // From script: session.getSessionId()
            $table->string('event_type')->index();        // From script: type argument in sendPayload
            $table->json('event_data')->nullable();       // From script: data argument in sendPayload

            // UTM Parameters
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('utm_term')->nullable();
            $table->string('utm_content')->nullable();

            // Contextual Information
            $table->text('initial_referrer')->nullable();
            $table->text('current_url');
            $table->string('page_title')->nullable();
            $table->string('screen_resolution')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('browser_language')->nullable();
            $table->json('browser_languages')->nullable(); // Storing array as JSON
            $table->decimal('pixel_ratio', 3, 2)->nullable(); // e.g., 1.00, 1.50, 2.00
            $table->string('domain')->nullable();         // window.location.hostname

            $table->ipAddress('ip_address')->nullable();  // Captured server-side
            $table->timestamp('event_timestamp');         // From script: new Date().toISOString()
            $table->timestamps();                         // Laravel's created_at and updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_sessions');
    }
};
