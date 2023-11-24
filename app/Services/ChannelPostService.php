<?php

namespace App\Services;

class ChannelPostService
{
    public function getHumanViews(string $views): int
    {
        $position = strpos($views, 'K');
        $humanViews = $position !== false ? (float) str_replace('K', '', $views) * 1000 : $views;

        return (int) $humanViews;
    }
}
