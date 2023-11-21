<?php

namespace App\Services;

use App\Models\ChannelPost;
use App\Models\ChannelPostStat;
use Illuminate\Support\Carbon;

class UpdateChannelPostStatService
{
    public function run(): void
    {
        $channelPosts = ChannelPost::query()
            ->whereRelation('channel', 'is_active', 1)
            ->whereDate('publication_at', '<', Carbon::now()->subHour())
            ->where('publication_at', '>', Carbon::now()->subDay())
            ->get();

        foreach ($channelPosts as $post) {
            $this->updateViewsStat($post);
        }
    }

    public function updateViewsStat(ChannelPost $channelPost): void
    {
        $publicationAt = Carbon::createFromFormat('Y-m-d H:i:s', $channelPost->publication_at);
        $oneHourAgo = Carbon::now()->subHour();
        $sixthHoursAgo = Carbon::now()->subHours(6);
        $twelveHoursAgo = Carbon::now()->subHours(12);
        $dayAgo = Carbon::now()->subDay();

        $channelPostStat = $channelPost->stat;

        if (!$channelPost->stat) {
            $channelPostStat = new ChannelPostStat();
            $channelPostStat->channel_post_id = $channelPost->id;
        }

        if ($publicationAt->lessThan($oneHourAgo) && !$channelPost->views_after_hour) {
            $channelPostStat->views_after_hour = $channelPost->views;
        }

        if ($publicationAt->lessThan($sixthHoursAgo) && !$channelPost->views_after_sixth_hour) {
            $channelPostStat->views_after_sixth_hour = $channelPost->views;
        }

        if ($publicationAt->lessThan($twelveHoursAgo) && !$channelPost->views_after_twelve_hour) {
            $channelPostStat->views_after_twelve_hour = $channelPost->views;
        }

        if ($publicationAt->lessThan($dayAgo) && !$channelPost->views_after_day) {
            $channelPostStat->views_after_day = $channelPost->views;
        }

        $channelPostStat->save();
    }
}
