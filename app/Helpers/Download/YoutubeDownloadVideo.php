<?php

namespace App\Helpers\Download;

use App\Services\GoogleDriveService;
use HeadlessChromium\BrowserFactory;
use HeadlessChromium\Page;
use Illuminate\Support\Facades\Log;

class YoutubeDownloadVideo implements DownloadVideoInterface
{

    public function download(string $url)
    {
        preg_match('/"(?=https:\/\/rr).{1,444}(?=mp4).*?"/', file_get_contents($url), $matches);
        $videoUrl = str_replace('\u0026', '&', str_replace('"', '', $matches[0]));

        Log::info("Downloaded youtube video, content url " . $videoUrl);

        app(GoogleDriveService::class)->createFile(file_get_contents($videoUrl));
    }
}
