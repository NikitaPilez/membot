<?php

namespace App\DTO;

use App\Models\ChannelPost;

class CheckActualPostDTO
{
    public function __construct(public ChannelPost $channelPost, public int $avgShares = 0, public int $avgViews = 0, public bool $isMostViewed = false, public bool $isMostShared = false)
    {
    }
}
