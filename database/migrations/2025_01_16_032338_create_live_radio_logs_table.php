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
        Schema::create('live_radio_logs', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('created_by', 20);
            $table->enum('msg_type', ['chat', 'user_join', 'user_left', 'user_like', 'user_dislike', 'user_vote_skip', 'user_vote_remove'])->default('chat');
            $table->mediumText('msg');
            $table->string('thumbnail', 99);
            $table->timestamp('time')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('live_radio_logs');
    }
};
