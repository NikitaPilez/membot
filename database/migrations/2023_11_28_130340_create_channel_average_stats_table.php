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
        Schema::create('channel_average_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('channel_id');
            $table->integer('hour_count');
            $table->integer('avg_share')->default(0);
            $table->integer('avg_views')->default(0);
            $table->foreign('channel_id')->references('id')->on('channels')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('channel_average_stats');
    }
};
