<?php

namespace App\Helpers\CheckPotentialVideo;

use App\Models\Channel;

interface CheckPotentialVideoInterface
{
    public function getChannelPosts(Channel $channel): array;
}
