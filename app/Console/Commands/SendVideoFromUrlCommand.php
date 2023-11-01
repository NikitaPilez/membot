<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Helpers\Download\ContentVideoInterface;
use App\Helpers\Utils;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Exception;
use Illuminate\Support\Facades\Log;

class SendVideoFromUrlCommand extends Command
{
    protected $signature = 'send:from-url {--url=}';

    protected $description = 'Отправляет видео в телеграм канал, используя url';

    public function handle(): void
    {
        if (!$url = $this->option('url')) {
            return;
        }

        $socialType = Utils::getSocialTypeByLink($url);

        /** @var ContentVideoInterface $contentVideo */
        $contentVideo = Utils::getVideoContentHelper($socialType);
        $getContentDto = $contentVideo->getContentUrl($url);

        $params = [
            'chat_id' => config('services.telegram.chat_id'),
            'video' => $getContentDto->sourceUrl,
            'caption' => '[Memkes](https://t.me/+eDaOkG0hXi5mNzAy)',
            'parse_mode' => 'markdown'
        ];

        try {
            Http::post(sprintf('https://api.telegram.org/%s/sendVideo', config('services.telegram.api_key')), $params);

            Log::channel('content')->info('Видео успешно отправлено в телеграм по команде', [
                'type' => $socialType,
                'url' => $url,
                'sourceUrl' => $getContentDto->sourceUrl,
            ]);
        } catch (Exception $exception) {
            info('SendVideoFromUrlCommand', ['message' => $exception->getMessage()]);

            Log::channel('content')->error('Ошибка при отправке видео в телеграм по команде', [
                'message' => $exception->getMessage(),
                'type' => $socialType,
                'url' => $url,
                'sourceUrl' => $getContentDto->sourceUrl,
            ]);
        }
    }
}
