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
        Schema::create('images_upload', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('c4id');
            $table->string('username', 999);
            $table->string('filename', 999);
            $table->dateTime('time_of_upload');
            $table->string('caption', 999);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('images_upload');
    }
};
