<?php

namespace Tests\Feature;

use App\Models\Channel;
use App\Models\ChannelPost;
use App\Models\ChannelPostStat;
use App\Services\UpdateChannelPostStatService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\CreatesApplication;
use Tests\TestCase;

class UpdateChannelPostStatTest extends TestCase
{
    use CreatesApplication;
    use DatabaseMigrations;

    public function test_is_need_update_stat()
    {
        $channel = Channel::factory()->create();
        $channelPost = ChannelPost::factory()->create(['channel_id' => $channel->id]);
        /** @var UpdateChannelPostStatService $service */
        $service = app(UpdateChannelPostStatService::class);

        $isNeedUpdateStat = $service->isNeedUpdateStat($channelPost, now()->subHour());
        $this->assertTrue($isNeedUpdateStat);

        ChannelPostStat::factory()->create([
            'created_at' => now()->subHour()->subMinutes(5),
        ]);

        $isNeedUpdateStat = $service->isNeedUpdateStat($channelPost, now()->subHour());
        $this->assertTrue($isNeedUpdateStat);

        ChannelPostStat::factory()->create([
            'created_at' => now()->subMinutes(30),
        ]);

        $isNeedUpdateStat = $service->isNeedUpdateStat($channelPost, now()->subHour());
        $this->assertFalse($isNeedUpdateStat);
    }
}
