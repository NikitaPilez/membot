<?php

namespace App\Helpers\Download;

use App\DTO\GetContentUrlDTO;
use Exception;
use HeadlessChromium\BrowserFactory;

class InstagramContentVideo implements ContentVideoInterface
{
    public function getContent(string $sourceUrl)
    {
        $ch = curl_init();
        $videoName = 'video.mp4';

        curl_setopt_array($ch, [
            CURLOPT_URL            => $sourceUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_BINARYTRANSFER => true,
        ]);

        $videoContent = curl_exec($ch);

        file_put_contents($videoName, $videoContent);

        curl_close($ch);

        $content = file_get_contents($videoName);

        unlink($videoName);

        return $content;
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
