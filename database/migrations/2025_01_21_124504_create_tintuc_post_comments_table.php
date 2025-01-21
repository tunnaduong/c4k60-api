<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('tintuc_post_comments', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->integer('post_id');// Foreign key for the post
            $table->string('username', 99)->collation('utf8mb4_general_ci');// Username of the commenter
            $table->text('content')->nullable(); // Content of the comment
            $table->string('image')->nullable(); // Image URL (optional)
            $table->timestamps(); // created_at and updated_at

            // Add foreign key constraints
            $table->foreign('post_id')->references('id')->on('tintuc_posts')->onDelete('cascade');
            $table->foreign('username')->references('username')->on('c4_user')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tintuc_post_comments');
    }
};
