<?php

namespace App\Helpers\Download;

use HeadlessChromium\BrowserFactory;
use Illuminate\Support\Facades\Log;

class InstagramContent implements ContentVideoInterface
{

    public function getContent(string $sourceUrl)
    {
        return file_get_contents($sourceUrl);
    }

    public function getContentUrl(string $videoUrl): string
    {
        $browserFactory = new BrowserFactory();
        $browser = $browserFactory->createBrowser();

        $page = $browser->createPage();
        $page->navigate($videoUrl)->waitForNavigation();
        $html = $page->getHtml(20000);

        preg_match('/"contentUrl":"(.*?)"/', $html, $match);
        $sourceUrl = stripslashes(json_decode('"' . $match[1] . '"'));
        Log::info("Instagram video, source url " . $sourceUrl);

        return $sourceUrl;
    }
}
