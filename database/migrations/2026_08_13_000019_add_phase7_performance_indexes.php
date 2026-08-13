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
        Schema::table('users', function (Blueprint $table) {
            $table->index('is_active');
        });

        Schema::table('media', function (Blueprint $table) {
            $table->index('mime_type');
        });

        Schema::table('newsletter_subscribers', function (Blueprint $table) {
            $table->index('subscribed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
        });

        Schema::table('media', function (Blueprint $table) {
            $table->dropIndex(['mime_type']);
        });

        Schema::table('newsletter_subscribers', function (Blueprint $table) {
            $table->dropIndex(['subscribed']);
        });
    }
};
