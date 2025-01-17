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
        Schema::create('baitap_gv', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('title', 99);
            $table->string('gvgiao', 99);
            $table->dateTime('ngaygiao');
            $table->dateTime('hannop');
            $table->string('theloai', 11);
            $table->string('link', 99);
            $table->string('monhoc', 11);
            $table->string('urltype', 11);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('baitap_gv');
    }
};
