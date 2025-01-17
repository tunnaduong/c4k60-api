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
        Schema::create('chat', function (Blueprint $table) {
            $table->integer('id', true);
            $table->text('message');
            $table->string('user_from', 999);
            $table->string('user_to', 25);
            $table->string('image_url', 999)->nullable();
            $table->dateTime('time')->useCurrent();
            $table->string('type', 13)->default('group');
            $table->boolean('sent')->default(true);
            $table->boolean('received')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat');
    }
};
