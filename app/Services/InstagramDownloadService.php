<?php

namespace App\Services;

use HeadlessChromium\BrowserFactory;
use HeadlessChromium\Page;

class InstagramDownloadService
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
            echo 'error'; //todo
        }

        $videoUrl = str_replace('amp;', '', str_replace('"', '', $videoUrlWithBrackets));

        app(GoogleDriveService::class)->createFile(file_get_contents($videoUrl));
    }
}
