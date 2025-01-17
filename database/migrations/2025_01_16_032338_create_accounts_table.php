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
        Schema::create('accounts', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('username', 1111);
            $table->string('password', 111);
            $table->string('email', 999);
            $table->string('name', 999);
            $table->string('gender', 6);
            $table->date('date_of_birth');
            $table->string('about', 95);
            $table->string('permission', 11);
            $table->string('oauth_provider', 999);
            $table->string('oauth_uid', 999);
            $table->string('profile_pic', 999);
            $table->date('date');
            $table->string('verified', 11);
            $table->string('activation_code', 11);
            $table->string('location', 999);
            $table->string('school', 99);
            $table->string('live_in', 999);
            $table->string('relationship', 11);
            $table->integer('followers');
            $table->string('cover_pic', 999);
            $table->string('other_name', 999);
            $table->string('has_cover', 11);
            $table->string('highlight_photo', 999);
            $table->integer('profile_pic_id');
            $table->integer('cover_pic_id');
            $table->integer('highlight_pic_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
