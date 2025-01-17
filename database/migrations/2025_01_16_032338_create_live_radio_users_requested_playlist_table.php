<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('live_radio_users_requested_playlist', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('video_id', 11);
            $table->string('requested_by', 20);
            $table->timestamp('request_time')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('live_radio_users_requested_playlist');
    }
};
