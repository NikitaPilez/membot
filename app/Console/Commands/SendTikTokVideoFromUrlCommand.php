<?php

declare(strict_types=1);

namespace App\Console\Commands;

use HeadlessChromium\BrowserFactory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Exception;

class SendTikTokVideoFromUrlCommand extends Command
{
    protected $signature = 'send:tiktok-from-url {--url=}';

    protected $description = 'Отправляет видео в телеграм канал, используя tiktok url';

    public function handle()
    {
        if (!$url = $this->option('url')) {
            return;
        }

        $browserFactory = new BrowserFactory();
        $browser = $browserFactory->createBrowser([
            'customFlags' => ['--disable-blink-features', '--disable-blink-features=AutomationControlled'],
            'sendSyncDefaultTimeout' => 10000,
        ]);

        $page = $browser->createPage();
        $page->navigate('https://downtik.io/')->waitForNavigation();
        $elem = $page->dom()->querySelector('#url');

        $elem->click();
        $elem->sendKeys($url);

        sleep(1);

        $page->evaluate("document.querySelector('#send').click()");

        sleep(5);

        $sourceUrl = $page->evaluate("document.querySelector('.abuttons a').href")->getReturnValue();

        $browser->close();

        $params = [
            'chat_id' => config('services.telegram.chat_id'),
            'video' => $sourceUrl,
            'caption' => '[Memkes](https://t.me/+eDaOkG0hXi5mNzAy)',
            'parse_mode' => 'markdown'
        ];

        try {
            Http::post(sprintf('https://api.telegram.org/%s/sendVideo', config('services.telegram.api_key')), $params);
        } catch (Exception $exception) {
            info('SendTikTokVideoFromUrlCommand', ['message' => $exception->getMessage()]);
        }
    }
}
