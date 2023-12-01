<?php

namespace App\DTO;

class GetAverageStatByChannelPost
{
    public function __construct(public int $channelId, public int $hourCount, public int $avgShares, public int $avgViews)
    {
    }
}
