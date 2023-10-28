<?php

declare(strict_types=1);

namespace App\Console\Commands;

use HeadlessChromium\BrowserFactory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Exception;

class SendVideoFromUrlCommand extends Command
{
    protected $signature = 'send:video-from-url {--url=}';

    protected $description = 'Отправляет видео в телеграм канал, используя url';

    public function handle()
    {
        $browserFactory = new BrowserFactory();
        $browser = $browserFactory->createBrowser([
            'customFlags' => ['--disable-blink-features', '--disable-blink-features=AutomationControlled'],
            'sendSyncDefaultTimeout' => 10000,
        ]);

        $page = $browser->createPage();
        $page->navigate('https://sssinstagram.com')->waitForNavigation();
        $elem = $page->dom()->querySelector('#main_page_text');

        $elem->click();
        $elem->sendKeys($this->option('url'));

        sleep(1);

        $page->evaluate("document.querySelector('#submit').click()");

        sleep(5);
        $url = $page->evaluate("document.querySelector('.download-wrapper a').href")->getReturnValue();
        $browser->close();

        $params = [
            'chat_id' => config('services.telegram.chat_id'),
            'video' => $url,
            'caption' => '[Memkes](https://t.me/+eDaOkG0hXi5mNzAy)',
            'parse_mode' => 'markdown'
        ];

        try {
            Http::post(sprintf('https://api.telegram.org/%s/sendVideo', config('services.telegram.api_key')), $params);
        } catch (Exception $exception) {
            info('SendVideoFromUrlCommand', ['message' => $exception->getMessage()]);
        }
    }
}
