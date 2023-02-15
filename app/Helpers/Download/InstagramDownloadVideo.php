<?php

namespace App\Helpers\Download;

use App\Services\GoogleDriveService;
use HeadlessChromium\BrowserFactory;
use HeadlessChromium\Page;
use Illuminate\Support\Facades\Log;

class InstagramDownloadVideo implements DownloadVideoInterface
{

    public function download(string $url)
    {
        $browserFactory = new BrowserFactory();
        $browser = $browserFactory->createBrowser();

        $page = $browser->createPage();
        $page->navigate($url)->waitForNavigation(Page::NETWORK_IDLE);
        $html = $page->getHtml(20000);

        preg_match('/(?=https:\/\/scontent-waw1-1\.cdninstagram\.com).{1,110}(?=mp4\?).*?"/', $html, $matches);

        if (!$videoUrlWithBrackets = current($matches)) {
            // TODO Error
        }

        $videoUrl = str_replace('amp;', '', str_replace('"', '', $videoUrlWithBrackets));
        Log::info("Downloaded instagram video, content url " . $videoUrl);

        app(GoogleDriveService::class)->createFile(file_get_contents($videoUrl));
    }
}
