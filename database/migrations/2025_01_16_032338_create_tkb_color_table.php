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
        Schema::create('tkb_color', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('t2t1', 11);
            $table->string('t2t2')->nullable();
            $table->string('t2t3')->nullable();
            $table->string('t2t4')->nullable();
            $table->string('t2t5')->nullable();
            $table->string('t3t1')->nullable();
            $table->string('t3t2')->nullable();
            $table->string('t3t3')->nullable();
            $table->string('t3t4')->nullable();
            $table->string('t3t5')->nullable();
            $table->string('t4t1')->nullable();
            $table->string('t4t2')->nullable();
            $table->string('t4t3')->nullable();
            $table->string('t4t4')->nullable();
            $table->string('t4t5')->nullable();
            $table->string('t5t1')->nullable();
            $table->string('t5t2')->nullable();
            $table->string('t5t3')->nullable();
            $table->string('t5t4')->nullable();
            $table->string('t5t5')->nullable();
            $table->string('t6t1')->nullable();
            $table->string('t6t2')->nullable();
            $table->string('t6t3')->nullable();
            $table->string('t6t4')->nullable();
            $table->string('t6t5')->nullable();
            $table->string('t7t1')->nullable();
            $table->string('t7t2')->nullable();
            $table->string('t7t3')->nullable();
            $table->string('t7t4')->nullable();
            $table->string('t7t5')->nullable();
            $table->string('t2c')->nullable();
            $table->string('t3c')->nullable();
            $table->string('t4c')->nullable();
            $table->string('t5c')->nullable();
            $table->string('t6c')->nullable();
            $table->string('t7c')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tkb_color');
    }
};
