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
        Schema::table('posts', function (Blueprint $table) {
            $table->index(['status', 'visibility', 'published_at']);
            $table->index('view_count');
        });

        Schema::table('post_tag', function (Blueprint $table) {
            $table->index('tag_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex(['status', 'visibility', 'published_at']);
            $table->dropIndex(['view_count']);
        });

        Schema::table('post_tag', function (Blueprint $table) {
            $table->dropIndex(['tag_id']);
        });
    }
};
