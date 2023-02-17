<?php

namespace App\Helpers\Download;

use Illuminate\Support\Facades\Log;

class YoutubeContentVideo implements ContentVideoInterface
{

    public function getContent(string $url)
    {
        preg_match('/"(?=https:\/\/rr).{1,444}(?=mp4).*?"/', file_get_contents($url), $matches);
        $videoUrl = str_replace('\u0026', '&', str_replace('"', '', $matches[0]));

        Log::info("Downloaded youtube video, content url " . $videoUrl);

        return file_get_contents($videoUrl);
    }
}
