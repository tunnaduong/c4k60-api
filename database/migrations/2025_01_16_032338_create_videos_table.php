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
        Schema::create('videos', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('caption', 999);
            $table->string('path', 999);
            $table->string('link_youtube', 999);
            $table->string('thumb_path', 999);
            $table->string('type', 11);
            $table->integer('album');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
