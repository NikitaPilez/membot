<?php

namespace App\Services;

use App\DTO\CheckActualPostDTO;
use App\Models\ChannelAverageStat;
use App\Models\ChannelPostStat;

class CheckActualPostService
{
    public function check(ChannelPostStat $channelPostStat): CheckActualPostDTO
    {
        $hoursDifferenceBetweenStatAndPost = $channelPostStat->created_at->diffInHours($channelPostStat->post->publication_at);

        /** @var ChannelAverageStat $channelAverageStat */
        $channelAverageStat = ChannelAverageStat::query()
            ->where('hour_count', $hoursDifferenceBetweenStatAndPost)
            ->where('channel_id', $channelPostStat->post->channel_id)
            ->first();

        if (!$channelAverageStat) {
            return new CheckActualPostDTO(
                channelPost: $channelPostStat->post,
            );
        }

        $checkActualPostDTO = new CheckActualPostDTO(
            channelPost: $channelPostStat->post,
            avgShares: $channelAverageStat->avg_share,
            avgViews: $channelAverageStat->avg_views,
        );

        if ($channelPostStat->shares > $channelAverageStat->avg_share) {
            $checkActualPostDTO->isMostShared = true;
        }

        if ($channelPostStat->views > $channelAverageStat->avg_views) {
            $checkActualPostDTO->isMostViewed = true;
        }

        return $checkActualPostDTO;
    }
}
