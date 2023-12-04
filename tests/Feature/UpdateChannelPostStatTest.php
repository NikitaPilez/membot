<?php

namespace Tests\Feature;

use App\Models\Channel;
use App\Models\ChannelPost;
use App\Models\ChannelPostStat;
use App\Services\UpdateChannelPostStatService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateChannelPostStatTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_posts_needing_stat_update_publication_hour_ago()
    {
        $channel = Channel::factory()->create();
        $channelPost = ChannelPost::factory()->create([
            'channel_id' => $channel->id,
            'publication_at' => now()->subHour(),
        ]);

        /** @var UpdateChannelPostStatService $service */
        $service = app(UpdateChannelPostStatService::class);

        /** @var Collection<int, ChannelPost> $postNeedingStatUpdate */
        $postNeedingStatUpdate = $service->getPostsNeedingStatUpdate($channel, (array) $channelPost->post_id);
        $this->assertCount(1, $postNeedingStatUpdate);
    }

    public function test_get_posts_not_needing_stat_update_publication_half_hour_ago()
    {
        $channel = Channel::factory()->create();
        $channelPost = ChannelPost::factory()->create([
            'channel_id' => $channel->id,
            'publication_at' => now()->subMinutes(30),
        ]);

        /** @var UpdateChannelPostStatService $service */
        $service = app(UpdateChannelPostStatService::class);

        /** @var Collection<int, ChannelPost> $postNeedingStatUpdate */
        $postNeedingStatUpdate = $service->getPostsNeedingStatUpdate($channel, (array) $channelPost->post_id);
        $this->assertCount(0, $postNeedingStatUpdate);
    }

    public function test_get_posts_not_needing_stat_update_publication_one_day_one_hour_ago()
    {
        $channel = Channel::factory()->create();
        $channelPost = ChannelPost::factory()->create([
            'channel_id' => $channel->id,
            'publication_at' => now()->subHours(25),
        ]);

        /** @var UpdateChannelPostStatService $service */
        $service = app(UpdateChannelPostStatService::class);

        /** @var Collection<int, ChannelPost> $postNeedingStatUpdate */
        $postNeedingStatUpdate = $service->getPostsNeedingStatUpdate($channel, (array) $channelPost->post_id);
        $this->assertCount(0, $postNeedingStatUpdate);
    }

    public function test_get_posts_not_needing_stat_update_publication_last_stat_less_hour_ago()
    {
        $channel = Channel::factory()->create();

        $channelPost = ChannelPost::factory()->create([
            'channel_id' => $channel->id,
            'publication_at' => now()->subHours(2),
        ]);

        ChannelPostStat::factory()->create([
            'channel_post_id' => $channelPost->id,
            'created_at' => now()->subMinutes(59),
        ]);

        /** @var UpdateChannelPostStatService $service */
        $service = app(UpdateChannelPostStatService::class);

        /** @var Collection<int, ChannelPost> $postNeedingStatUpdate */
        $postNeedingStatUpdate = $service->getPostsNeedingStatUpdate($channel, (array) $channelPost->post_id);
        $this->assertCount(0, $postNeedingStatUpdate);
    }

    public function test_get_posts_needing_stat_update_publication_last_stat_more_hour_ago()
    {
        $channel = Channel::factory()->create();

        $channelPost = ChannelPost::factory()->create([
            'channel_id' => $channel->id,
            'publication_at' => now()->subHours(2),
        ]);

        ChannelPostStat::factory()->create([
            'channel_post_id' => $channelPost->id,
            'created_at' => now()->subMinutes(70),
        ]);

        /** @var UpdateChannelPostStatService $service */
        $service = app(UpdateChannelPostStatService::class);

        /** @var Collection<int, ChannelPost> $postNeedingStatUpdate */
        $postNeedingStatUpdate = $service->getPostsNeedingStatUpdate($channel, (array) $channelPost->post_id);
        $this->assertCount(1, $postNeedingStatUpdate);
    }
}
