<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\SendVideoInTelegramService;
use Illuminate\Console\Command;

class SendVideoInTelegramCommand extends Command
{
    protected $signature = 'send:video-telegram';

    protected $description = 'Отправляет видео в телеграм канал';

    public function handle(SendVideoInTelegramService $service): void
    {
        $service->sendVideoInTelegram();
    }
}
