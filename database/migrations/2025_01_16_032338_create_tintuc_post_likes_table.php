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
        Schema::create('tintuc_post_likes', function (Blueprint $table) {
            $table->integer('like_id', true);
            $table->string('liked_username', 99)->index('fk_liked_username');
            $table->timestamp('time_of_like')->useCurrent();
            $table->integer('liked_post_id')->index('fk_liked_post_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tintuc_post_likes');
    }
};
