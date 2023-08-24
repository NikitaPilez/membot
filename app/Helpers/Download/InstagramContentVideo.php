<?php

namespace App\Helpers\Download;

use App\DTO\GetContentUrlDTO;
use Exception;
use HeadlessChromium\BrowserFactory;
use Illuminate\Support\Facades\Log;

class InstagramContentVideo implements ContentVideoInterface
{

    public function getContent(string $sourceUrl)
    {
        return file_get_contents($sourceUrl);
    }

    public function getContentUrl(string $videoUrl): GetContentUrlDTO
    {
        try {
            $browserFactory = new BrowserFactory();
            $browser = $browserFactory->createBrowser();

            $page = $browser->createPage();
            $page->navigate($videoUrl)->waitForNavigation();
            $html = $page->getHtml(20000);

            preg_match('/"contentUrl":"(.*?)"/', $html, $match);
            $sourceUrl = stripslashes(json_decode('"' . $match[1] . '"'));
        } catch (Exception $exception) {
            return new GetContentUrlDTO(
                success: false,
                message: $exception->getMessage(),
            );
        }

        return new GetContentUrlDTO(
            success: true,
            sourceUrl: $sourceUrl,
        );
    }
}
