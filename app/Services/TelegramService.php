<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Channel;
use App\Models\Video;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TelegramService
{
    private string $apiUrl;

    public function __construct()
    {
        $telegramApiKey = config('services.telegram.api_key');
        $this->apiUrl = 'https://api.telegram.org/' . $telegramApiKey;
    }

    public function sendVideo(Video $video): bool
    {
        if (config('app.env') === 'local') {

            Log::channel('content')->info('Попытка отправить видео в телеграм с локального компьютера.');

            return true;
        }

        $description = $video->description ? "$video->description \n\n[Memkes](https://t.me/+eDaOkG0hXi5mNzAy)" : '[Memkes](https://t.me/+eDaOkG0hXi5mNzAy)';

        $params = [
            'chat_id' => $video->is_prod ? config('services.telegram.chat_id_prod') : config('services.telegram.chat_id_dev'),
            'video' => Storage::disk('public')->url($video->name),
            'caption' => $description,
            'parse_mode' => 'markdown'
        ];

        $response = Http::post($this->apiUrl . '/sendVideo', $params)->json();

        if (array_key_exists('ok', $response) && $response['ok']) {
            Log::channel('content')->info('Видео успешно отправлено в телеграм канал.', $params);

            return true;
        }

        Log::channel('content')->error('Видео не отправлено в телеграм канал.', array_merge($params, $response));

        return false;
    }

    public function sendNotificationAboutNewPost(Channel $channel): bool
    {
        $params = [
            'chat_id' => config('services.telegram.chat_id_dev'),
            'text' => 'Новый пост на канале ' . $channel->name,
        ];

        $response = Http::post($this->apiUrl . '/sendMessage', $params)->json();

        if (array_key_exists('ok', $response) && $response['ok']) {
            Log::channel('content')->info('Уведомление успешно отправлено в телеграм канал.', $params);

            return true;
        }

        Log::channel('content')->error('Уведомление не отправлено в телеграм канал.', array_merge($params, $response));

        return false;
    }
}
