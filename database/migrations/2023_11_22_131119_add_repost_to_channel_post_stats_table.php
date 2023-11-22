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
        Schema::table('channel_post_stats', function (Blueprint $table) {
            $table->integer('views');
            $table->integer('shares');
            $table->dropColumn('views_after_hour');
            $table->dropColumn('views_after_sixth_hour');
            $table->dropColumn('views_after_twelve_hour');
            $table->dropColumn('views_after_day');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('channel_post_stats', function (Blueprint $table) {
            $table->integer('views_after_hour')->nullable();
            $table->integer('views_after_sixth_hour')->nullable();
            $table->integer('views_after_twelve_hour')->nullable();
            $table->integer('views_after_day')->nullable();
            $table->dropColumn('views');
            $table->dropColumn('shares');
        });
    }
};
