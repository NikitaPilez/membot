<?php

namespace Tests\Feature;


use App\DTO\CheckActualPostDTO;
use App\DTO\GetAverageStatByChannelPost;
use App\Models\Channel;
use App\Models\ChannelAverageStat;
use App\Models\ChannelPost;
use App\Models\ChannelPostStat;
use App\Services\CheckActualPostService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckActualPostServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_is_shared_and_is_viewed()
    {
        $channel = Channel::factory()->create();

        $now = now();

        $channelPost = ChannelPost::factory()->create([
            'channel_id' => $channel->id,
            'publication_at' => $now->subHours(2),
        ]);

        $channelPostStat = ChannelPostStat::factory()->create([
            'channel_post_id' => $channelPost->id,
            'created_at' => $now->subHour(),
            'views' => 101,
            'shares' => 11,
        ]);

        /** @var CheckActualPostService $service */
        $service = app(CheckActualPostService::class);

        $channelAverageStat = ChannelAverageStat::factory([
            'channel_id' => $channel->id,
            'hour_count' => 1,
            'avg_share' => 10,
            'avg_views' => 100,
        ])->create();

        $getActualPostDto = $service->check($channelPostStat);

        $this->assertTrue($getActualPostDto->isMostShared);
        $this->assertTrue($getActualPostDto->isMostViewed);
    }

    public function test_no_average_stats()
    {
        $channel = Channel::factory()->create();

        $now = now();

        $channelPost = ChannelPost::factory()->create([
            'channel_id' => $channel->id,
            'publication_at' => $now->subHours(2),
        ]);

        $channelPostStat = ChannelPostStat::factory()->create([
            'channel_post_id' => $channelPost->id,
            'created_at' => $now->subHour(),
            'views' => 101,
            'shares' => 11,
        ]);

        /** @var CheckActualPostService $service */
        $service = app(CheckActualPostService::class);

        $getActualPostDto = $service->check($channelPostStat);

        $this->assertFalse($getActualPostDto->isMostShared);
        $this->assertFalse($getActualPostDto->isMostViewed);
    }
}
