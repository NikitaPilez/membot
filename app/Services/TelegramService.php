<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Video;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    private string $memChatId;
    private string $apiUrl;

    public function __construct()
    {
        $telegramApiKey = config('services.telegram.api_key');
        $this->apiUrl = 'https://api.telegram.org/' . $telegramApiKey;
        $this->memChatId = config('services.telegram.chat_id');
    }

    public function sendVideo(Video $video): bool
    {
        if (config('app.env') === 'local') {

            Log::channel('content')->info('Попытка отправить видео в телеграм с локального компьютера.');

            return true;
        }

        $params = [
            'chat_id' => $this->memChatId,
            'video' => config('app.url') . '/' . $video->name,
            'caption' => '[Memkes](https://t.me/+eDaOkG0hXi5mNzAy)',
            'parse_mode' => 'markdown'
        ];

        $response = Http::post($this->apiUrl . '/sendVideo', $params)->json();

        if (array_key_exists('ok', $response) && $response['ok']) {
            Log::channel('content')->info('Видео успешно отправлено в телеграм канал.', $params);

            return true;
        }

        Log::channel('content')->error('Видео не отправлено в телеграм канал.', $response);

        return false;
    }

    public function getChannelStats()
    {
        $proto = MTProtoSingleton::getProtoInstance();
        return $proto->getFullInfo($this->memChatId);
    }
}
