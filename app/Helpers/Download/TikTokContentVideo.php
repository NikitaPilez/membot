<?php

namespace App\Helpers\Download;

use App\DTO\GetContentDTO;
use App\DTO\GetContentUrlDTO;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use HeadlessChromium\BrowserFactory;
use Illuminate\Support\Str;

class TikTokContentVideo implements ContentVideoInterface
{

    public function getContent(string $sourceUrl)
    {
        $client = new Client();

        try {
            $response = $client->get($sourceUrl);
            $body = $response->getBody();
        } catch (GuzzleException $e) {
            return new GetContentDTO(
                success: false,
                message: $e->getMessage(),
            );
        }

        return new GetContentDTO(
            success: true,
            content: $body->getContents(),
        );
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
            $page->navigate('https://downtik.io/')->waitForNavigation();
            $elem = $page->dom()->querySelector('#url');

            $elem->click();
            $elem->sendKeys(Str::finish($videoUrl, '/'));

            sleep(1);

            $page->evaluate("document.querySelector('#send').click()");

            sleep(5);

            $sourceUrl = $page->evaluate("document.querySelector('.abuttons a').href")->getReturnValue();
            $previewImgUrl = $page->evaluate("document.querySelector('.download-downtik-left img').src")->getReturnValue();

            $browser->close();

            return new GetContentUrlDTO(
                success: true,
                sourceUrl: $sourceUrl,
                previewImgUrl: $previewImgUrl,
            );
        } catch (Exception $exception) {
            return new GetContentUrlDTO(
                success: false,
                message: $exception->getMessage(),
            );
        }
    }
}
