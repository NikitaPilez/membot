<?php

namespace App\Helpers\Download;

use Illuminate\Support\Facades\Log;

class YoutubeContentVideo implements ContentVideoInterface
{

    public function getContentUrl(string $videoUrl): string
    {
        preg_match('/"(?=https:\/\/rr).{1,444}(?=mp4).*?"/', file_get_contents($videoUrl), $matches);
        $sourceUrl = str_replace('\u0026', '&', str_replace('"', '', $matches[0]));

        Log::info("Youtube video, source url " . $sourceUrl);

        return $sourceUrl;
    }

    public function getContent(string $sourceUrl)
    {
        return file_get_contents($sourceUrl);
    }
}
