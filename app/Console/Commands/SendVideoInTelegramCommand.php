<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\SendVideoInTelegramJob;
use Illuminate\Console\Command;

class SendVideoInTelegramCommand extends Command
{
    protected $signature = 'send:video-telegram';

    protected $description = 'Отправляет видео в телеграм канал';

    public function handle(): void
    {
        SendVideoInTelegramJob::dispatch()->onQueue('content');
    }
}
