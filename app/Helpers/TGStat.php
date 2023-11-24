<?php

namespace App\Helpers;

class TGStat
{
    public static function getHumanViews(string $views): int
    {
        $position = strpos($views, 'k');
        $humanViews = $position !== false ? (float) str_replace('k', '', $views) * 1000 : $views;

        return (int) $humanViews;
    }
}
