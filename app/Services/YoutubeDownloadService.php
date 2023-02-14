<?php

namespace App\Services;

use HeadlessChromium\BrowserFactory;
use HeadlessChromium\Page;

class YoutubeDownloadService
{
    public function shortsDownload(string $url)
    {
        $browserFactory = new BrowserFactory();
        $browser = $browserFactory->createBrowser();

        $page = $browser->createPage();
        $page->navigate($url)->waitForNavigation(Page::NETWORK_IDLE);
        $html = $page->getHtml(20000);

        preg_match('/"(?=https:\/\/rr).{1,444}(?=mp4).*?"/', $html, $matches);

        if (!$videoUrlWithBrackets = current($matches)) {
            echo 'error'; //todo
        }

        $videoUrl = str_replace('\u0026', '&', str_replace('"', '', $videoUrlWithBrackets));

        app(GoogleDriveService::class)->createFile(file_get_contents($videoUrl));
    }
}
