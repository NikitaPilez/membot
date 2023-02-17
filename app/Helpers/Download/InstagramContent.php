<?php

namespace App\Helpers\Download;

use App\Services\GoogleDriveService;
use HeadlessChromium\BrowserFactory;
use Illuminate\Support\Facades\Log;

class InstagramContent implements ContentVideoInterface
{

    public function getContent(string $url)
    {
        $browserFactory = new BrowserFactory();
        $browser = $browserFactory->createBrowser();

        $page = $browser->createPage();
        $page->navigate($url)->waitForNavigation();
        $html = $page->getHtml(20000);

        preg_match('/"contentUrl":"(.*?)"/', $html, $match);
        $videoUrl = stripslashes(json_decode('"' . $match[1] . '"'));
        Log::info("Downloaded instagram video, content url " . $videoUrl);

        return file_get_contents($videoUrl);
    }
}
