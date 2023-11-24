<?php

namespace Database\Factories;

use App\Models\Channel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ChannelPost>
 */
class ChannelPostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'channel_id' => Channel::query()->inRandomOrder()->first(),
            'post_id' => fake()->randomNumber(),
            'description' => fake()->text,
            'publication_at' => fake()->dateTime,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
