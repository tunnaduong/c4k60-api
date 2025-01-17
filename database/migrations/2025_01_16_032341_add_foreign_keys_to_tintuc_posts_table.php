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
        Schema::table('tintuc_posts', function (Blueprint $table) {
            $table->foreign(['username'], 'fk_username')->references(['username'])->on('c4_user')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tintuc_posts', function (Blueprint $table) {
            $table->dropForeign('fk_username');
        });
    }
};
