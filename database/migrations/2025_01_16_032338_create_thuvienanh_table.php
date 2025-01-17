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
        Schema::create('thuvienanh', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('image_name', 999);
            $table->string('path', 999);
            $table->string('thumb_path', 999);
            $table->string('album', 99);
            $table->string('imgtype', 11);
            $table->integer('imgsize');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thuvienanh');
    }
};
