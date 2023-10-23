<?php

declare(strict_types=1);

namespace App\Console\Commands;

use HeadlessChromium\BrowserFactory;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
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
            'customFlags' => ['--disable-blink-features=AutomationControlled'],
//            'headless' => false
        ]);

        $page = $browser->createPage();
        $page->navigate('https://en.savefrom.net/1-youtube-video-downloader-4/')->waitForNavigation();

        $page->evaluate(sprintf("document.getElementsByName('sf_url')[0].value='%s'", $this->option('url')));
        $page->evaluate("document.getElementsByName('sf_submit')[0].click()");
        sleep(4);
        $html = $page->getHtml(10000);

        preg_match('/data-type="mp4" href="(.*?)" data-ga-event/', $html, $match);

        if (!Arr::get($match, 1)) {
            return;
        }

        $telegramApiKey = config('services.telegram.api_key');
        $this->apiUrl = 'https://api.telegram.org/' . $telegramApiKey;
        $this->memChatId = config('services.telegram.chat_id');

        $params = [
            'chat_id' => $this->memChatId,
            'video' => htmlspecialchars_decode($match[1] ?? ''),
            'caption' => '[Memkes](https://t.me/+eDaOkG0hXi5mNzAy)',
            'parse_mode' => 'markdown'
        ];

        try {
            Http::post($this->apiUrl . '/sendVideo', $params);
        } catch (Exception $exception) {
            info('SendVideoFromUrlCommand', ['message' => $exception->getMessage()]);
        }
    }
}
