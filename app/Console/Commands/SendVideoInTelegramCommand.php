<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Video;
use App\Services\SendVideoInTelegramService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendVideoInTelegramCommand extends Command
{
    protected $signature = 'send:video-telegram';

    protected $description = 'Отправляет видео в телеграм канал';

    public function handle(SendVideoInTelegramService $service): void
    {
        $video = Video::where('google_file_id', '!=', null)
            ->where('is_sent', 0)
            ->where('publication_date', '>', now())
            ->orderBy('publication_date')
            ->first();

        if ($video) {
            Log::channel('content')->info('Найдено видео для отправки', [
                'video' => $video->id,
                'link' => $video->url,
            ]);

            $service->sendVideoInTelegram($video);
        }
    }
}
