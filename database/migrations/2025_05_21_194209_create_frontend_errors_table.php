<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('frontend_errors', function (Blueprint $table) {
            $table->id();
            $table->string('client_website_id')->index()->nullable(); // To associate error with a client if possible via API key
            $table->text('message');
            $table->text('stack')->nullable();
            $table->text('page_url');
            $table->ipAddress('ip_address')->nullable();    // Captured server-side
            $table->text('user_agent')->nullable();       // Captured server-side
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('frontend_errors');
    }
};
