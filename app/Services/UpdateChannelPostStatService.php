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
            ->where('publication_at', '<', Carbon::now()->subHour())
            ->where('publication_at', '>', Carbon::now()->subDay()->subMinutes(10))
            ->get();

        foreach ($channelPosts as $post) {
            $this->updateViewsStat($post);
        }
    }

    public function updateViewsStat(ChannelPost $channelPost): void
    {
        $publicationAt = Carbon::createFromFormat('Y-m-d H:i:s', $channelPost->publication_at);

        $channelPostStat = $channelPost->stat;

        if (!$channelPost->stat) {
            $channelPostStat = new ChannelPostStat();
            $channelPostStat->channel_post_id = $channelPost->id;
        }

        $now = Carbon::now();

        if ($publicationAt->lessThan($now->subHour()) && $publicationAt->greaterThan($now->subHour()->subMinutes(10)) && !$channelPostStat->views_after_hour) {
            $channelPostStat->views_after_hour = $channelPost->views;
        } else if ($publicationAt->lessThan($now->subHours(6)) && $publicationAt->greaterThan($now->subHours(6)->subMinutes(10)) && !$channelPostStat->views_after_sixth_hour) {
            $channelPostStat->views_after_sixth_hour = $channelPost->views;
        } else if ($publicationAt->lessThan(Carbon::now()->subHours(12)) && $publicationAt->greaterThan($now->subHours(12)->subMinutes(10)) && !$channelPostStat->views_after_twelve_hour) {
            $channelPostStat->views_after_twelve_hour = $channelPost->views;
        } else if ($publicationAt->lessThan(Carbon::now()->subDay()) && $publicationAt->greaterThan($now->subDay()->subMinutes(10)) && !$channelPostStat->views_after_day) {
            $channelPostStat->views_after_day = $channelPost->views;
        }

        $channelPostStat->save();
    }
}
