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
        Schema::table('tintuc_post_likes', function (Blueprint $table) {
            $table->foreign(['liked_post_id'], 'fk_liked_post_id')->references(['id'])->on('tintuc_posts')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['liked_username'], 'fk_liked_username')->references(['username'])->on('c4_user')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tintuc_post_likes', function (Blueprint $table) {
            $table->dropForeign('fk_liked_post_id');
            $table->dropForeign('fk_liked_username');
        });
    }
};
