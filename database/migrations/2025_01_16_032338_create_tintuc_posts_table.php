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
        Schema::create('tintuc_posts', function (Blueprint $table) {
            $table->integer('id', true);
            $table->text('content');
            $table->string('author', 99);
            $table->string('username', 99)->index('fk_username');
            $table->dateTime('timeofpost');
            $table->string('style', 20);
            $table->string('has_comment', 3);
            $table->string('avatar', 999);
            $table->string('has_image', 7);
            $table->string('image', 999);
            $table->integer('c4id')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tintuc_posts');
    }
};
