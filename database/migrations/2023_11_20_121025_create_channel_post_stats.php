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
        Schema::create('channel_post_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('channel_post_id');
            $table->integer('views');
            $table->foreign('channel_post_id')->references('id')->on('channel_posts')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('channel_post_stats');
    }
};
