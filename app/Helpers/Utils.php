<?php

namespace App\Helpers;

use App\Helpers\Download\ContentVideoInterface;
use App\Helpers\Download\InstagramContentVideo;
use App\Helpers\Download\SimpleConverter;
use App\Helpers\Download\TikTokContentVideo;
use App\Helpers\Download\YoutubeContentVideo;

class Utils
{
    public static function getSocialTypeByLink(string $url)
    {
        return collect([
            'instagram.com' => 'instagram',
            'youtube.com/shorts' => 'youtube',
            'tiktok.com' => 'tiktok',
        ])->firstWhere(function ($value, $key) use ($url) {
            return str_contains($url, $key);
        });
    }

    public static function getVideoContentHelper(string $type): ContentVideoInterface
    {
        return match ($type) {
            'tiktok' => new TikTokContentVideo(),
            'youtube' => new YoutubeContentVideo(),
            'instagram' => new InstagramContentVideo(),
            default => new SimpleConverter(),
        };
    }
}
