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
        Schema::create('thongbaolop', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('title', 99)->nullable();
            $table->text('content')->nullable();
            $table->string('createdBy', 99)->default('Admin C4K60');
            $table->string('image', 999)->default('no');
            $table->timestamp('date')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thongbaolop');
    }
};
