<?php

namespace Tests\Feature\UpdateChannelPostStat;

use App\DTO\ChannelPostTGStatDTO;
use App\Models\Channel;
use App\Models\ChannelPost;
use App\Models\ChannelPostStat;
use App\Services\UpdateChannelPostStatService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateViewsStatTest extends TestCase
{
    use RefreshDatabase;

    public function test_no_isset_stats()
    {
        $channel = Channel::factory()->create();

        $hourCount = 2;

        $channelPost = ChannelPost::factory()->create([
            'channel_id' => $channel->id,
            'publication_at' => now()->subHours($hourCount),
        ]);

        $channelPostTgStatDTO = new ChannelPostTGStatDTO(
            id: 1,
            views: 10,
            shares: 10,
            createdAt: now()->subMinutes(10),
            description: 'Test',
        );

        /** @var UpdateChannelPostStatService $service */
        $service = app(UpdateChannelPostStatService::class);
        $service->updateViewsStat($channelPost, $channelPostTgStatDTO);

        /** @var ChannelPostStat $channelPostStat */
        $channelPostStat = ChannelPostStat::query()->first();
        $this->assertSame((string) $channelPost->publication_at->addHours($hourCount), (string) $channelPostStat->created_at);
    }

    public function test_isset_stats()
    {
        $channel = Channel::factory()->create();

        $hourCount = 3;

        $channelPost = ChannelPost::factory()->create([
            'channel_id' => $channel->id,
            'publication_at' => now()->subHours($hourCount),
        ]);

        $issetChannelPostStat = ChannelPostStat::factory()->create([
            'channel_post_id' => $channelPost->id,
            'created_at' => now()->subHour(),
        ]);

        $channelPostTgStatDTO = new ChannelPostTGStatDTO(
            id: 1,
            views: 10,
            shares: 10,
            createdAt: now(),
            description: 'Test',
        );

        /** @var UpdateChannelPostStatService $service */
        $service = app(UpdateChannelPostStatService::class);
        $service->updateViewsStat($channelPost, $channelPostTgStatDTO);

        /** @var ChannelPostStat $channelPostStat */
        $channelPostStat = ChannelPostStat::query()->where('channel_post_id', $channelPost->id)->orderByDesc('created_at')->first();

        $this->assertSame((string) $issetChannelPostStat->created_at->addHour(), (string) $channelPostStat->created_at);
    }
}
