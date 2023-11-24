<?php

namespace Database\Factories;

use App\Models\ChannelPost;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ChannelPostStat>
 */
class ChannelPostStatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'channel_post_id' => ChannelPost::query()->inRandomOrder()->first(),
            'views' => fake()->randomNumber(),
            'shares' => fake()->randomNumber(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
