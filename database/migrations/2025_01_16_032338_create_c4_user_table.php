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
        Schema::create('c4_user', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name', 99);
            $table->string('firstname', 99);
            $table->string('lastname', 99);
            $table->string('username', 99)->unique('username');
            $table->string('password', 99);
            $table->string('dayofbirth', 2);
            $table->string('monthofbirth', 2);
            $table->string('yearofbirth', 4);
            $table->string('address', 999);
            $table->string('phone_number', 13);
            $table->string('short_name', 99);
            $table->string('fb_link', 999);
            $table->string('ig_link', 999);
            $table->text('additional_info');
            $table->string('role', 10)->default('student');
            $table->boolean('verified');
            $table->string('avatar', 999)->default('default_avatar');
            $table->string('gender', 6);
            $table->dateTime('last_activity')->useCurrent();
            $table->string('expo_push_notification_token', 999)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('c4_user');
    }
};
