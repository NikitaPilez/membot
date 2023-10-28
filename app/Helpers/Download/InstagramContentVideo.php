<?php

namespace App\Helpers\Download;

use App\DTO\GetContentUrlDTO;
use Exception;
use HeadlessChromium\BrowserFactory;

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
            $browser = $browserFactory->createBrowser([
                'customFlags' => ['--disable-blink-features', '--disable-blink-features=AutomationControlled'],
                'sendSyncDefaultTimeout' => 10000,
            ]);

            $page = $browser->createPage();
            $page->navigate('https://sssinstagram.com')->waitForNavigation();
            $elem = $page->dom()->querySelector('#main_page_text');

            $elem->click();
            $elem->sendKeys($videoUrl);

            sleep(1);

            $page->evaluate("document.querySelector('#submit').click()");

            sleep(5);

            $sourceUrl = $page->evaluate("document.querySelector('.download-wrapper a').href")->getReturnValue();

            $browser->close();
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
