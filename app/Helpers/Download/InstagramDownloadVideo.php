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
        $page->navigate($url)->waitForNavigation();
        $html = $page->getHtml(20000);

        preg_match('/"contentUrl":"(.*?)"/', $html, $match);
        $videoUrl = stripslashes(json_decode('"' . $match[1] . '"'));
        Log::info("Downloaded instagram video, content url " . $videoUrl);

        app(GoogleDriveService::class)->createFile(file_get_contents($videoUrl));
    }
}
