<?php

namespace Database\Factories;

use App\Models\Channel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Channel>
 */
class ChannelAverageStatFactory extends Factory
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
            'hour_count' => rand(1, 24),
            'avg_share' => rand(1, 1000),
            'avg_views' => rand(1, 1000),
            'created_at' => now(),
        ];
    }
}
