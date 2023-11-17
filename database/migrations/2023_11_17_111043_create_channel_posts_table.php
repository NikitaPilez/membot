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
        Schema::create('channel_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('channel_id');
            $table->integer('post_id');
            $table->text('description')->nullable();
            $table->dateTime('publication_at');
            $table->foreign('channel_id')->references('id')->on('channels')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('channel_posts');
    }
};
