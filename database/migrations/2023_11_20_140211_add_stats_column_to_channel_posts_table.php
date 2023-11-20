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
        Schema::table('channel_posts', function (Blueprint $table) {
            $table->integer('views_after_hour')->after('publication_at')->nullable();
            $table->integer('views_after_sixth_hour')->after('views_after_hour')->nullable();
            $table->integer('views_after_twelve_hour')->after('views_after_sixth_hour')->nullable();
            $table->integer('views_after_day')->after('views_after_twelve_hour')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('channel_posts', function (Blueprint $table) {
            $table->dropColumn('views_after_hour');
            $table->dropColumn('views_after_sixth_hour');
            $table->dropColumn('views_after_twelve_hour');
            $table->dropColumn('views_after_day');
        });
    }
};
